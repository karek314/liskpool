<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('priv_utils.php');
require_once('../lisk-php/main.php');
$payout_threshold = $config['payout_threshold'];
$withdraw_interval_in_sec = $config['withdraw_interval_in_sec'];
$fixed_withdraw_fee = $config['fixed_withdraw_fee'];
$delegate = $config['delegate_address'];
$secret1 = $config['secret'];
$secret2 = $config['secondSecret'];
$protocol = $config['protocol'];
$public_directory = $config['public_directory'];

while(1){
	$m = new Memcached();
  	$m->addServer('localhost', 11211);
  	$lisk_host = $m->get('lisk_host');
  	$lisk_port = $m->get('lisk_port');
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
	if (IsBalanceOkToWithdraw($mysqli,$server,$delegate)) {
  		$json = AccountForAddress($delegate,$server);
  		$publicKey = $json['account']['publicKey'];
		$existQuery = "SELECT address,balance FROM miners WHERE balance!='0'";
		$existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
		while ($row=mysqli_fetch_row($existResult)){
			$payer_adr = $row[0];
			$balance = $row[1];
			$balanceinlsk = floatval($balance/100000000);
			echo "\n-------------------------------------------";
			echo "\n".$payer_adr.' -> '.$balanceinlsk;
			if ($balanceinlsk > $payout_threshold) {
				$deduced_by_fee_h = $balanceinlsk - $fixed_withdraw_fee;
				$deduced_by_fee = $deduced_by_fee_h * 100000000;
				if (!$secret2) {
					$tx = CreateTransaction($payer_adr, $deduced_by_fee, $secret1, false, false, -10);
				} else {
					$tx = CreateTransaction($payer_adr, $deduced_by_fee, $secret1, $secret2, false, -10);
				}
				$tx_resp = SendTransaction(json_encode($tx),$server);
				$txid = $tx_resp['transactionId'];
				echo "\n".json_encode($tx_resp);
				if ($txid) {
					$timestamp = time();
					AppendChartData('voters/withdraw',$deduced_by_fee_h,$timestamp,$payer_adr,$public_directory);
					$tas22k = 'INSERT INTO payout_history (address, balance, time, txid, fee) VALUES ("'.$payer_adr.'", "'.$balance.'", "'.$timestamp.'", "'.$txid.'", "'.$fixed_withdraw_fee.'")';
					$query = mysqli_query($mysqli,$tas22k) or die("Database Error");
					$task = "UPDATE miners SET balance='0' WHERE address='$payer_adr';";	
					$query = mysqli_query($mysqli,$task) or die("Database Error");	
					echo "\nWithdraw OK ->".$txid;
				} else {
					print_r($json_arr);
					print_r($data);
				}
				usleep(100000);
				$withdrawcount++;
			} else {
				echo "\nNot exceeded threshold\n";
			}
		}
		echo "\nSleeping for:".$withdraw_interval_in_sec." sec";
		sleep($withdraw_interval_in_sec);
	} else {
		echo "\n\n!!! Incorrect - balance invalid !!!";
		echo "\n!!! Can't withdraw, retrying after 30min !!!\n\n";
		sleep(1800);
	}
}
?>