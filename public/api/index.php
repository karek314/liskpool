<?php
error_reporting(error_reporting() & ~E_NOTICE);
require_once('../../utils.php');
$data = $_GET['data'];
$forger = $_GET['dtx'];
$data = mysql_fix_escape_string($data);
$forger = mysql_fix_escape_string($forger);

if ($data == '_miner_balance' && $forger) {
	$config = include('../../config.php');
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
	$existQuery = "SELECT value,var_timestamp FROM miner_balance WHERE miner='$forger' ORDER BY id ASC";
	$existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
	$count = mysqli_num_rows($existResult);
	$x++;
	$miner_payouts = array();
	echo '[';
	while ($row=mysqli_fetch_row($existResult)){
		$stamp = $row[1]*1000;
		$real = $row[0];
		$x++;
	    echo '['.$stamp.','.$real.']';
    	if ($x-1 != $count) {
    		echo ',';
    	}
	}
	echo ']';
} else {
	die(json_encode(array('success' => false,'error' => 'data or forger not specified')));
}

?>
