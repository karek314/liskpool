<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('../lisk-php/main.php');
require_once('priv_utils.php');
$secret1 = $config['secret'];
$secret2 = $config['secondSecret'];
$slow_withdraw = $config['slow_withdraw'];
$fancy_secret = $config['fancy_withdraw_hub'];
$public_directory = $config['public_directory'];
$tx_data = $config['withdraw_message'];
$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
$payer_adr = $argv[1];
if ($payer_adr == '') {
	die('missing parm1');
}
$balanceinlsk = $argv[2];
if ($balanceinlsk == '') {
	die('missing parm2');
}
$lsk = new Math_BigInteger(LSK_BASE);
$balanceinlsk = new Math_BigInteger($balanceinlsk);
$calculatedFee = new Math_BigInteger(SEND_FEE);
list($fee_quotient, $fee_reminder) = $calculatedFee->divide($lsk);
$floatFee = floatval($fee_quotient->toString().".".$fee_reminder->toString());
$BalanceDeducedByFee = $balanceinlsk->subtract($calculatedFee);
$deduced_by_fee = $BalanceDeducedByFee->toString();
list($balance_quotient, $balance_reminder) = $balanceinlsk->divide($lsk);
$original_balance = floatval($balance_quotient->toString().".".$balance_reminder->toString());
if ($fancy_secret) {
	$tx = CreateTransaction($payer_adr, $deduced_by_fee, $fancy_secret, false, $tx_data, -10);
} else {
	if (!$secret2) {
		$tx = CreateTransaction($payer_adr, $deduced_by_fee, $secret1, false, $tx_data, -10);
	} else {
		$tx = CreateTransaction($payer_adr, $deduced_by_fee, $secret1, $secret2, $tx_data, -10);
	}
}
$tx_resp = SendTransaction(json_encode($tx),$server);
$status = $tx_resp['meta']['status'];
$txid = $tx['id'];
$th_reply = array();
$th_reply['tx_resp'] = $tx_resp;
if ($status) {
	$timestamp = time();
	AppendChartData('voters/withdraw',$original_balance,$timestamp,$payer_adr,$public_directory);
	$tas22k = 'INSERT INTO payout_history (address, balance, time, txid, fee) VALUES ("'.$payer_adr.'", "'.$balanceinlsk->toString().'", "'.$timestamp.'", "'.$txid.'", "'.$floatFee.'")';
	$query = mysqli_query($mysqli,$tas22k) or die("Database Error");
	$task = "UPDATE miners SET balance='0' WHERE address='$payer_adr';";	
	$query = mysqli_query($mysqli,$task) or die("Database Error");
	$th_reply['ok'] = $txid;	
}
if ($slow_withdraw) {
	sleep(10);
}
echo json_encode($th_reply);
?>