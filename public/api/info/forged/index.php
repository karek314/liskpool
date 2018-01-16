<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../../../../config.php');
require_once('../../../../utils.php');
$by = mysql_fix_escape_string($_GET["by"]);
if ($by) {
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
	$queryy = "SELECT balance FROM miners WHERE address = '$by'";
	$result = mysqli_query($mysqli,$queryy)or die("Database Error");
	$row = mysqli_fetch_array($result);
	$balance = $row[0];
	$balanceinlsk = floatval($balance/100000000);
	$balance = array('lsk' => $balanceinlsk, "raw" => $balance);
	$tmp = array('balance' => $balance,'success' => true);
	header('Content-Type: application/json');
	die(json_encode($tmp));
} else {
	$m = new Memcached();
	$m->addServer('localhost', 11211);
	$data = $m->get('forgers_balance');
	$response = array('data' => $data,'success' => true);
	die(json_encode($response));
}
?>
