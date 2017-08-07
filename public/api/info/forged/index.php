<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../../../../config.php');
$fee = $config['pool_fee_payout_address'];
$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");

$task = "SELECT balance,address FROM miners ORDER BY balance DESC LIMIT 2000;";
$result = mysqli_query($mysqli,$task)or die("Database Error");
$data = array();
while ($row=mysqli_fetch_row($result)){
    $balance = $row[0];
    $address = $row[1];
    if ($address != $fee) {
      $balanceinlsk = floatval($balance/100000000);
      $balance_ar = array('lsk' => number_format($balanceinlsk, 8),'raw' => $balance);
      $tmp = array('address' => $address,'balance' => $balance_ar);
      array_push($data, $tmp);
	}
}
$response = array('data' => $data,
				  'success' => true);
die(json_encode($response));
?>
