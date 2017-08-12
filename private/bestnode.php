<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
require_once('../lisk-php/main.php');
require_once('logging.php');
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
  clog("/////////////////////////////////////////\nCurrent iteration: ".$df,'bestnode');
  clog("Current lisk node: ".$lisk_host.':'.$lisk_port,'bestnode');
  clog("Current nodes count definied in config: ".count($config_lisk_host),'bestnode');
  if (count($config_lisk_host) > 1) {
    $heights = array();
    for ($i=0; $i < count($config_lisk_host); $i++) {
      $curr_host = $config_lisk_host[$i];
      $curr_port = $config_lisk_port[$i];
      clog("[".$i."]Checking node: ".$curr_host.':'.$curr_port,'bestnode');
      $custom = $protocol.'://'.$curr_host.':'.$curr_port.'/';
      $block = NodeStatus($custom);
      array_push($heights, $block["height"]);
      clog("Height: ".$block["height"],'bestnode');
    }
    $best_height = max($heights);
    $key = array_search($best_height, $heights);
    clog("Best height: ".$best_height,'bestnode');
    $best_host = $config_lisk_host[$key];
    $best_port = $config_lisk_port[$key];
    clog("Best node: ".$best_host.':'.$best_port,'bestnode');
    $m->set('lisk_host', $best_host, 3600*365);
    $m->set('lisk_port', $best_port, 3600*365);
    $m->set('lisk_protocol', $protocol, 3600*365);
    
    $lisk_host_tmp = $m->get('lisk_host');
    $lisk_port_tmp = $m->get('lisk_port');
    clog("Current lisk node is set to: ".$lisk_host_tmp.':'.$lisk_port_tmp,'bestnode');
  } else {
    clog("Nothing to do here... Setting only one as best",'bestnode');
    $m->set('lisk_host', $config_lisk_host[0], 3600*365);
    $m->set('lisk_port', $config_lisk_port[0], 3600*365);
    $m->set('lisk_protocol', $protocol, 3600*365);
    $lisk_host_tmp = $m->get('lisk_host');
    $lisk_port_tmp = $m->get('lisk_port');
    clog("Current lisk node is set to: ".$lisk_host_tmp.':'.$lisk_port_tmp,'bestnode');
  }

  $end_time = time();
  $took = $end_time - $start_time;
  $time_sleep = 10-$took;
  if ($time_sleep < 1) {
    $time_sleep = 1;
  }
  clog('Took:'.$took.' sleep:'.$time_sleep,'bestnode');
  sleep($time_sleep);
}


?>