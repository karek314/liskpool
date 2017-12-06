<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('../lisk-php/main.php');
require_once('priv_utils.php');
$fixed_withdraw_fee = $config['fixed_withdraw_fee'];
$secret1 = $config['secret'];
$secret2 = $config['secondSecret'];
$slow_withdraw = $config['slow_withdraw'];
$fancy_secret = $config['fancy_withdraw_hub'];
$public_directory = $config['public_directory'];
$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
$payer_adr = $argv[1];
if ($payer_adr == '') {
	die('missing parm1');
}
$balanceinlsk = $argv[2];
if ($balanceinlsk == '') {
	die('missing parm2');
}
$deduced_by_fee_h = $balanceinlsk - $fixed_withdraw_fee;
$deduced_by_fee = $deduced_by_fee_h * 100000000;
$original_balance = $balanceinlsk * 100000000;
if ($fancy_secret) {
	$tx = CreateTransaction($payer_adr, $deduced_by_fee, $fancy_secret, false, false, -10);
} else {
	if (!$secret2) {
		$tx = CreateTransaction($payer_adr, $deduced_by_fee, $secret1, false, false, -10);
	} else {
		$tx = CreateTransaction($payer_adr, $deduced_by_fee, $secret1, $secret2, false, -10);
	}
}
$tx_resp = SendTransaction(json_encode($tx),$server);
$txid = $tx_resp['transactionId'];
$th_reply = array();
$th_reply['tx_resp'] = $tx_resp;
if ($txid) {
	$timestamp = time();
	AppendChartData('voters/withdraw',$balanceinlsk,$timestamp,$payer_adr,$public_directory);
	$tas22k = 'INSERT INTO payout_history (address, balance, time, txid, fee) VALUES ("'.$payer_adr.'", "'.$original_balance.'", "'.$timestamp.'", "'.$txid.'", "'.$fixed_withdraw_fee.'")';
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