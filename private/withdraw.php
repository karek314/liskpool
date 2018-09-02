<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('priv_utils.php');
require_once('logging.php');
require_once('../lisk-php/main.php');
$thread_file = "php ".realpath(dirname(__FILE__))."/wthread.php";
$threads = ((int)shell_exec("cat /proc/cpuinfo | grep processor | wc -l")*2)-1;
$payout_threshold = floatval($config['payout_threshold']);
$withdraw_interval_in_sec = $config['withdraw_interval_in_sec'];
$delegate = $config['delegate_address'];
$secret1 = $config['secret'];
$secret2 = $config['secondSecret'];
$fancy_secret = $config['fancy_withdraw_hub'];
$protocol = $config['protocol'];
$public_directory = $config['public_directory'];
$payout_threshold = LSK_BASE * $payout_threshold;
$payout_threshold = new Math_BigInteger($payout_threshold);
$lsk = new Math_BigInteger(LSK_BASE);

while(1){
	$m = new Memcached();
  	$m->addServer('localhost', 11211);
  	$server = getCurrentServer($m);
  	clog("Current server set to: ".$server,'withdraw');
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
	$withdraw_array = array();
	$required_balance = new Math_BigInteger('0');
	if (IsBalanceOkToWithdraw($mysqli,$server,$delegate,false)) {
  		$json = AccountForAddress($delegate,$server);
  		$publicKey = $json['data'][0]['publicKey'];
		$existQuery = "SELECT address,balance FROM miners WHERE balance!='0'";
		$existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
		while ($row=mysqli_fetch_row($existResult)){
			$payer_adr = $row[0];
			$balance = new Math_BigInteger($row[1]);
			list($balance_quotient, $balance_reminder) = $balance->divide($lsk);
			$balanceinlsk = $balance_quotient->toString();
			$valueAsFloat = floatval($balance_quotient->toString().".".$balance_reminder->toString());
			if ($balance->compare($payout_threshold) > 0) {
				clog("Address:".$payer_adr." - Adding to withdraw queue ".$valueAsFloat." LSK",'withdraw');
				$withdraw_array[$payer_adr] = $balance->toString();
				$required_balance = $required_balance->add($balance);
			} else {
				clog("Address:".$payer_adr." - Not exceeded threshold ".$balance->toString()."/".$payout_threshold->toString(),'withdraw');
			}
		}
		$wcount = count($withdraw_array);
		$txleft = $wcount;
		clog($wcount." eligible for withdraw",'withdraw');
		list($total_balance_quotient, $total_balance_reminder) = $required_balance->divide($lsk);
		$required_balance_float = floatval($total_balance_quotient->toString().".".$total_balance_reminder->toString());
		clog($required_balance_float." required for withdraw",'withdraw');
		if ($fancy_secret && $required_balance->toString() != "0") {
			$output = getKeysFromSecret($fancy_secret,true);
			$fancy_address = getAddressFromPublicKey($output['public']);
			$OneBdd = new Math_BigInteger('1');
			$required_balance = $required_balance->add($lsk);
			list($totalPlusOne_balance_quotient, $totalPlusOne_balance_reminder) = $required_balance->divide($lsk);
			$required_balance_floatPlusOne = floatval($totalPlusOne_balance_quotient->toString().".".$totalPlusOne_balance_reminder->toString());
			clog("Transfering:".$required_balance_floatPlusOne."LSK for withdraw to fancy hub: ".$fancy_address,'withdraw');
			$fancy_withdraw_hub_balance = new Math_BigInteger(AccountForAddress($fancy_address,$server)["data"][0]["balance"]);
			$topup_balance = $required_balance->subtract($fancy_withdraw_hub_balance);
			clog("Top up in Beddows:".$topup_balance->toString(),'withdraw');
			if ($topup_balance->compare($OneBdd) > 0) {
				if ($required_balance->toString() != "0") {
					list($topup_balance_quotient, $topup_balance_reminder) = $topup_balance->divide($lsk);
					$topupFloatVal = floatval($topup_balance_quotient->toString().".".$topup_balance_reminder->toString());
					clog("Top up:".$topupFloatVal."LSK",'withdraw');
					if (!$secret2) {
						$tx = CreateTransaction($fancy_address, $topup_balance->toString(), $secret1, false, false, -10);
					} else {
						$tx = CreateTransaction($fancy_address, $topup_balance->toString(), $secret1, $secret2, false, -10);
					}
					$tx_resp = json_encode(SendTransaction(json_encode($tx),$server));
					clog("Sleeping for: 120 sec, waiting for hub transfer ".$tx['id']." with status[".$tx_resp."] to settle",'withdraw');
					csleep(120);
				} else {
					clog("Amount on fancy hub correct! 1",'withdraw');
					csleep(2);
				}
			} else {
				clog("Amount on fancy hub correct! 2",'withdraw');
				csleep(2);
			}
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