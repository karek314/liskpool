<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
require_once('../lisk-php/main.php');
$config = include('../config.php');
$df = 0;
$delegate = $config['delegate_address'];
$pool_fee = floatval(str_replace('%', '', $config['pool_fee']));
$pool_fee_payout_address = $config['pool_fee_payout_address'];
$protocol = $config['protocol'];
$config_lisk_host = $config['lisk_host'];
$config_lisk_port = $config['lisk_port'];


while(1) {
  $start_time = time();
  $df++;
  $m = new Memcached();
  $m->addServer('localhost', 11211);
  $lisk_host = $m->get('lisk_host');
  $lisk_port = $m->get('lisk_port');
  echo "\n/////////////////////////////////////////\nCurrent iteration: ".$df;
  echo "\nCurrent lisk node: ".$lisk_host.':'.$lisk_port;
  echo "\nCurrent nodes count definied in config: ".count($config_lisk_host);
  if (count($config_lisk_host) > 1) {
    $heights = array();
    for ($i=0; $i < count($config_lisk_host); $i++) {
      $curr_host = $config_lisk_host[$i];
      $curr_port = $config_lisk_port[$i];
      echo "\n[".$i."]Checking node: ".$curr_host.':'.$curr_port;
      $custom = $protocol.'://'.$curr_host.':'.$curr_port.'/';
      $block = NodeStatus($custom);
      array_push($heights, $block["height"]);
      echo "\nHeight: ".$block["height"];
    }
    $best_height = max($heights);
    $key = array_search($best_height, $heights);
    echo "\nBest height: ".$best_height;
    $best_host = $config_lisk_host[$key];
    $best_port = $config_lisk_port[$key];
    echo "\nBest node: ".$best_host.':'.$best_port;
    $m->set('lisk_host', $best_host, 3600*365);
    $m->set('lisk_port', $best_port, 3600*365);
    $m->set('lisk_protocol', $protocol, 3600*365);
    
    $lisk_host_tmp = $m->get('lisk_host');
    $lisk_port_tmp = $m->get('lisk_port');
    echo "\nCurrent lisk node is set to: ".$lisk_host_tmp.':'.$lisk_port_tmp;
  } else {
    echo "\nNothing to do here... Setting only one as best";
    $m->set('lisk_host', $config_lisk_host[0], 3600*365);
    $m->set('lisk_port', $config_lisk_port[0], 3600*365);
    $m->set('lisk_protocol', $protocol, 3600*365);
    $lisk_host_tmp = $m->get('lisk_host');
    $lisk_port_tmp = $m->get('lisk_port');
    echo "\nCurrent lisk node is set to: ".$lisk_host_tmp.':'.$lisk_port_tmp;
  }

  $end_time = time();
  $took = $end_time - $start_time;
  $time_sleep = 10-$took;
  if ($time_sleep < 1) {
    $time_sleep = 1;
  }
  echo "\n".'Took:'.$took.' sleep:'.$time_sleep;
  sleep($time_sleep);
}


?>