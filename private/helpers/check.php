<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
$config = include('../../config.php');
require_once('../../lisk-php/main.php');
require_once('../priv_utils.php');


$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
echo "\nFetching data...\n";
if (IsBalanceOkToWithdraw($mysqli,$server,$config['delegate_address'])) {
	echo "\n\nCorrect - balance valid\n\n";
} else {
	echo "\n\n!!! Incorrect - balance invalid !!!\n\n";
}


?>