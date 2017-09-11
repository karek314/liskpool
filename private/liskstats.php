<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('logging.php');
require_once('priv_utils.php');
require('wss/Client.php');
use WebSocket\Client;

$i=0;
while (1) {
  $i++;
  $timestamp_ms = time()*1000;
  $client = new Client("ws://liskstats.net:3000/primus/?_primuscb=".$timestamp_ms."-0");
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
          if (isset($node['info'])) {
            if (isset($node['info']['protocol'])) {
              $object = $node["id"];
              $version = $node["info"]["protocol"];
              $tmp = array('object' => $object, 'version' => $version);
              $node_list[] = $tmp;
            }
          }
        }
      }
    }
  }
  foreach ($node_list as $key => $node) {
    $object = $node["object"];
    $version = $node["version"];
    if (strtolower($object) == strtolower("thePoolIo")) {
      $latest_lisk_version = $version;
    }
  }
  clog("[".$i."]Latest version of Lisk:".$latest_lisk_version,'liskstats');
  $ok = 0;
  $all = 0;
  foreach ($node_list as $key => $node) {
    $all++;
    $object = $node["object"];
    $version = $node["version"];
    if (strtolower($version) == strtolower($latest_lisk_version)) {
      clog("[".$i."][OK]".$object."->".$version." RUNNING LATEST VERSION",'liskstats');
      $ok++;
      $task = "INSERT INTO liskstats (object) SELECT * FROM (SELECT '$object') AS tmp WHERE NOT EXISTS (SELECT * FROM liskstats WHERE object = '$object' LIMIT 1)";
      $query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
    } else {
      clog("[".$i."]".$object."->".$version." Running old version",'liskstats');
    }
  }
  clog("[".$i."]Good nodes:".$ok."/".$all,'liskstats');
  csleep(1600);
  $x++;
}


?>

