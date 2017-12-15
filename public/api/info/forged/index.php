<?php
error_reporting(error_reporting() & ~E_NOTICE);
$m = new Memcached();
$m->addServer('localhost', 11211);
$data = $m->get('forgers_balance');
$response = array('data' => $data,'success' => true);
die(json_encode($response));
?>