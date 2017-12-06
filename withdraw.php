<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('priv_utils.php');
require_once('logging.php');
require_once('../lisk-php/main.php');
$thread_file = "php ".realpath(dirname(__FILE__))."/wthread.php";
$threads = ((int)shell_exec("cat /proc/cpuinfo | grep processor | wc -l")*2)-1;
$payout_threshold = $config['payout_threshold'];
$withdraw_interval_in_sec = $config['withdraw_interval_in_sec'];
$fixed_withdraw_fee = $config['fixed_withdraw_fee'];
$delegate = $config['delegate_address'];
$secret1 = $config['secret'];
$secret2 = $config['secondSecret'];
$fancy_secret = $config['fancy_withdraw_hub'];
$protocol = $config['protocol'];
$public_directory = $config['public_directory'];

while(1){
	$m = new Memcached();
  	$m->addServer('localhost', 11211);
  	$lisk_host = $m->get('lisk_host');
  	$lisk_port = $m->get('lisk_port');
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
	$withdraw_array = array();
	if (IsBalanceOkToWithdraw($mysqli,$server,$delegate)) {
  		$json = AccountForAddress($delegate,$server);
  		$publicKey = $json['account']['publicKey'];
		$existQuery = "SELECT address,balance FROM miners WHERE balance!='0'";
		$existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
		while ($row=mysqli_fetch_row($existResult)){
			$payer_adr = $row[0];
			$balance = $row[1];
			$balanceinlsk = floatval($balance/100000000);
			clog("-------------------------------------------",'withdraw');
			clog($payer_adr.' -> '.$balanceinlsk,'withdraw');
			if ($balanceinlsk > $payout_threshold) {
				clog("Adding to withdraw queue\n",'withdraw');
				$withdraw_array[$payer_adr] = $balanceinlsk;
				$required_balance+=$balanceinlsk;
			} else {
				clog("Not exceeded threshold\n",'withdraw');
			}
		}
		$wcount = count($withdraw_array);
		$txleft = $wcount;
		clog($wcount." eligible for withdraw",'withdraw');
		clog($required_balance." required for withdraw",'withdraw');
		if ($fancy_secret) {
			$output = getKeysFromSecret($fancy_secret,true);
			$fancy_address = getAddressFromPublicKey($output['public']);
			$required_balance+=1.0;
			clog("Transfering:".$required_balance." LSK for withdraw to fancy hub: ".$fancy_address,'withdraw');
			$required_balance = $required_balance * 100000000;
			if (!$secret2) {
				$tx = CreateTransaction($fancy_address, $required_balance, $secret1, false, false, -10);
			} else {
				$tx = CreateTransaction($fancy_address, $required_balance, $secret1, $secret2, false, -10);
			}
			$tx_resp = SendTransaction(json_encode($tx),$server);
			$txid = $tx_resp['transactionId'];
			clog("Sleeping for: 120 sec, waiting for hub transfer[".$txid."] to settle",'withdraw');
			csleep(120);
		}

		$pipes = array();
		foreach ($withdraw_array as $recipient => $balanceinlsk) {
			$pcount = count($pipes);
			if ($pcount < $threads || $txleft < $threads) {
				clog("[".$pcount."]Creating withdraw thread for ".$recipient."->".$balanceinlsk,'withdraw');
				$pipes[$pcount] = popen($thread_file." ".$recipient." ".$balanceinlsk, 'r');
			} else {
				clog("[".$pcount."]Creating withdraw thread for ".$recipient."->".$balanceinlsk,'withdraw');
				$pipes[$pcount] = popen($thread_file." ".$recipient." ".$balanceinlsk, 'r');
				for ($j=0; $j<$threads+1; ++$j) {
					$response = stream_get_contents($pipes[$j]);
					pclose($pipes[$j]);
					clog("[".$j."]".$response,'withdraw');
				}
				if ($txleft < $threads) {
					$btxleft = $txleft;
					$txleft = $txleft - $txleft;
				} else {
					$btxleft = $threads+1;
					$txleft = $txleft - ($threads+1);
				}
				clog("Txleft:".$txleft.", sent:".$btxleft,'withdraw');
				$pipes = array();
			}
		}
		if ($wcount > 0) {
			for ($j=0; $j<$txleft; ++$j) {
				$response = stream_get_contents($pipes[$j]);
				pclose($pipes[$j]);
				clog("[".$j."]".$response,'withdraw');
			}
			$btxleft = $txleft;
			$txleft = $txleft - $txleft;
			clog("Txleft:".$txleft.", sent:".$btxleft,'withdraw');
		} else {
			clog("No withdraws has been made",'withdraw');
		}
		clog("Sleeping for:".$withdraw_interval_in_sec." sec",'withdraw');
		csleep($withdraw_interval_in_sec);
	} else {
		clog("\n!!! Incorrect - balance invalid !!!",'withdraw');
		clog("!!! Can't withdraw, retrying after 30min !!!\n\n",'withdraw');
		csleep(1800);
	}
}
?>