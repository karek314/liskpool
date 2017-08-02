<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
$config = include('../../config.php');
require_once('../priv_utils.php');


$m = new Memcached();
$m->addServer('localhost', 11211);
$lisk_host = $m->get('lisk_host');
$lisk_port = $m->get('lisk_port');


$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
echo "\nFetching data...\n";
if (IsBalanceOkToWithdraw($mysqli,$config)) {
	echo "\n\nCorrect - balance valid\n\n";
} else {
	echo "\n\n!!! Incorrect - balance invalid !!!\n\n";
}


?>