<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../../../../config.php');
require_once('../../../../utils.php');
$by = mysql_fix_escape_string($_GET["by"]);
$lscontributor=false;
if ($by) {
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
	$queryy = "SELECT balance FROM miners WHERE address = '$by'";
	$result = mysqli_query($mysqli,$queryy)or die("Database Error");
	$row = mysqli_fetch_array($result);
	$balance = $row[0];
	if ($balance != "") {
		$balanceinlsk = floatval($balance/100000000);
		$liskstats_task = "SELECT id FROM liskstats WHERE object = '$by'";
		$liskstats_result = mysqli_query($mysqli,$liskstats_task)or die("Database Error");
		$lsid = mysqli_fetch_array($liskstats_result);
		$lsid = (string)$lsid[0];
		if ($lsid != "") {
			$lscontributor = true;
		}
		$withdrawTask = "SELECT balance,time,txid,fee FROM payout_history WHERE address='$by' ORDER BY id DESC LIMIT 50;";
		$withdrawResult = mysqli_query($mysqli,$withdrawTask)or die("Database Error");
		$withdraws = array();
		while ($row=mysqli_fetch_row($withdrawResult)){
	    	$balance = $row[0];
	    	$balanceinlsk = floatval($balance/100000000);
	    	$temp_balance = array('lsk' => $balanceinlsk, "raw" => $balance);
	    	$withdrawObject = array('balance' => $temp_balance, "timestamp" => $row[1], "txid" => $row[2], "fee" => $row[3]);
	    	array_push($withdraws, $withdrawObject);
		}
		$balance = array('lsk' => $balanceinlsk, "raw" => $balance);
		$tmp = array('balance' => $balance, 'success' => true, 'liskstats' => $lscontributor, 'withdraws' => $withdraws);
	} else {
		$tmp = array('success' => false, 'info' => "voter not exists");
	}
	header('Content-Type: application/json');
	die(json_encode($tmp));
} else {
	$m = new Memcached();
	$m->addServer('localhost', 11211);
	$data = $m->get('internal_voters_balance');
	$response = array('data' => $data, 'success' => true, 'info' => "biggest 100 only");
	header('Content-Type: application/json');
	die(json_encode($response));
}
?>
