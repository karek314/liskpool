<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('logging.php');
require_once('priv_utils.php');
require('wss/Client.php');
CONST LAZY_BLOCKHEIGHT_DIFF = 15;
use WebSocket\Client;

$black_list = array('4505864207850607262L',
                    '13707442412647196954L',
                    '2200695806490741028L',
                    '10326425301524121518L',
                    '3084676006015759509L',
                    '8192500104962567979L',
                    '12017072520984999553L',
                    '8442496832851399403L',
                    '3321295353527389994L',
                    '12908225732623920882L',
                    '3803512974926693981L',
                    '4488870420145179813L',
                    '6400631954431997967L',
                    '373660208352396834L',
                    '4588563411850930339L');

$i=0;
while (1) {
  $m = new Memcached();
  $m->addServer('localhost', 11211);
  $public_array = array();
  $i++;
  $timestamp_ms = time()*1000;
  $client = new Client("ws://report.liskstats.net/primus/?_primuscb=".$timestamp_ms."-0");
  $client->send('{"emit":["ready"]}');

  clog("[".$i."]Cleaning everything",'liskstats');
  $mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
  $task = "TRUNCATE TABLE `liskstats`";
  $query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));

  $response = json_decode($client->receive(),true);
  $node_list = array();
  $latest_lisk_version = "";
  if (isset($response['emit'])) {
    if (isset($response['emit'][1])) { 
      if (isset($response['emit'][1]['nodes'])) { 
        $array_of_nodes = $response['emit'][1]['nodes'];
        foreach ($array_of_nodes as $key => $node) {
          if (isset($node['stats'])) {
            if (isset($node['stats']['block'])) {
              if (isset($node['stats']['block']['number'])) {
                $block = $node['stats']['block']['number'];
              }
            }
          }
          if (!$block) {
            $block = 0;
          }
          if (isset($node['info'])) {
            $object = $node['id'];
            $node = $node['info'];
            if (!$object) {
              $object = $node["name"];
            }
            $version = $node["protocol"];
            $contact = $node["contact"];
            $full_version = $node["node"];
            $port = $node["port"];
            $os = $node["os"];
            $ip = $node["ip"];
            $tmp = array('object' => $object,
                         'version' => $version,
                         'contact' => $contact,
                         'full_version' => $full_version,
                         'port' => $port,
                         'os' => $os,
                         'height' => $block,
                         'ip' => $ip);
            $node_list[] = $tmp;
          }
        }
      }
    }
  }
  $height_array = array();
  foreach ($node_list as $key => $node) {
    $object = $node["object"];
    $version = $node["version"];
    $height = $node["height"];
    $height_array[] = $height;
    if (strtolower($object) == strtolower("thePoolIo") || strtolower($object) == strtolower("thepool.io")) {
      $latest_lisk_version = $version;
    }
  }
  if (!$height_array) {
    continue;
  }
  $best_height = max($height_array);
  if ($best_height < 4000000) {
    continue;
  }
  clog("[".$i."]Latest version of Lisk:".$latest_lisk_version,'liskstats');
  $public_array['latest_lisk_core_version'] = $latest_lisk_version;
  $public_array['info'] = "Good nodes are being paid while bad not";
  $ok = 0;
  $all = 0;
  $public_good = array();
  $public_bad = array();
  foreach ($node_list as $key => $node) {
    $all++;
    $object = $node["object"];
    $version = $node["version"];
    $contact = $node["contact"];
    $height = $node['height'];
    $ip = $node['ip'];
    if (strlen($contact) > 2) {
      if (!isLiskAddress($contact)) {
        if (!IsOffender($ip,$node_list)) {
          if (strtolower($version) == strtolower($latest_lisk_version)) {
            clog("[".$i."][OK]".$object."->".$version." All good",'liskstats');
            if (!in_array($object, $black_list)) {
              $diff = $best_height - LAZY_BLOCKHEIGHT_DIFF;
              if ($height > $diff) {
                $ok++;
                $task = "INSERT INTO liskstats (object) SELECT * FROM (SELECT '$object') AS tmp WHERE NOT EXISTS (SELECT * FROM liskstats WHERE object = '$object' LIMIT 1)";
                $query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
                if (isLiskAddress($object)) {
                  $node['info'] = 'On payroll';
                } else {
                  $node['info'] = 'voluntary';
                }
                $public_good[] = $node;
              } else {
                clog("[".$i."] (".$height.") ".$object."->".$version." Node stucked",'liskstats');
                $tmp = array('bad_node' => true, 'details' => 'node stucked / too much behind network best height');
                $node['info'] = $tmp;
                $public_bad[] = $node;
              }
            } else {
              clog("[".$i."] (".$height.") ".$object."->".$version." Blacklist",'liskstats');
              $tmp = array('bad_node' => true, 'details' => 'blacklisted');
              $node['info'] = $tmp;
              $public_bad[] = $node;
            }
          } else {
            clog("[".$i."] (".$height.") ".$object."->".$version." Running old version",'liskstats');
            $tmp = array('bad_node' => true, 'details' => 'running old version');
            $node['info'] = $tmp;
            $public_bad[] = $node;
          } 
        } else {
          clog("[".$i."] (".$height.") ".$object."->".$version." multiple nodes from one ip",'liskstats');
          $tmp = array('bad_node' => true, 'details' => 'multiple nodes from one ip');
          $node['info'] = $tmp;
          $public_bad[] = $node;
        }
      } else {
        clog("[".$i."] (".$height.") ".$object."->".$version." Contact info is invalid",'liskstats');
        $tmp = array('bad_node' => true, 'details' => 'contact info is invalid');
        $node['info'] = $tmp;
        $public_bad[] = $node;
      }
    } else {
      clog("[".$i."] (".$height.") ".$object."->".$version." Contact info is missing",'liskstats');
      $tmp = array('bad_node' => true, 'details' => 'contact info is missing');
      $node['info'] = $tmp;
      $public_bad[] = $node;
    }
  }
  clog("[".$i."]Good nodes:".$ok."/".$all,'liskstats');
  $tmp_goodbad = array('good' => $ok, 'bad' => $all-$ok, 'all' => $all);
  $public_array['nodes_count'] = $tmp_goodbad;
  $public_array['nodes'] = array('good' => $public_good, 'bad' => $public_bad);
  $m->set('liskstats', $public_array, 3600*365);
  csleep(60);
  $x++;
}


function IsOffender($ip,$array_of_nodes){
  $count=0;
  foreach ($array_of_nodes as $key => $node) {
    $all++;
    if ($node["ip"] == $ip) {
      $count++;
    }
  }
  if ($count == 1) {
    return false;
  }
  return true;
}


function isLiskAddress($object){
  if (strpos($object, 'L') !== false) {
    $tmp = str_replace('L', '', $object);
    if (is_numeric($tmp)) {
      return true;
    }
  }
  return false;
}

?>