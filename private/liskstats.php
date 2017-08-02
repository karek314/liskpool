<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require('wss/Client.php');
use WebSocket\Client;

$timestamp_ms = time()*1000;
$client = new Client("ws://liskstats.net:3000/primus/?_primuscb=".$timestamp_ms."-0");
$client->send('{"emit":["ready"]}');
$x=0;
$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
while (1) {
  if ($x > 3600) {
    echo "\nCleaning everything (once per hour)";
    $task = "TRUNCATE TABLE `liskstats`";
    $query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
    $x=0;
  }
  $response = json_decode($client->receive(),true);
  if (isset($response['data'])) {
    if (isset($response['data']['id'])) { 
      $object = $response['data']['id'];
      echo "\n".$object;
      $task = "INSERT INTO liskstats (object) SELECT * FROM (SELECT '$object') AS tmp WHERE NOT EXISTS (SELECT * FROM liskstats WHERE object = '$object' LIMIT 1)";
      $query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
    }
  }
  sleep(1);
  $x++;
}


?>

