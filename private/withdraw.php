<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('priv_utils.php');
$payout_threshold = $config['payout_threshold'];
$withdraw_interval_in_sec = $config['withdraw_interval_in_sec'];
$fixed_withdraw_fee = $config['fixed_withdraw_fee'];
$delegate = $config['delegate_address'];
$secret1 = $config['secret'];
$secret2 = $config['secondSecret'];
$protocol = $config['protocol'];
$lisk_host = $config['lisk_host'][0];
$lisk_port = $config['lisk_port'][0];

while(1){
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
	if (IsBalanceOkToWithdraw($mysqli,$config)) {
		$ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/accounts?address='.$delegate);                                                                      
  		curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");                                                                                      
  		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);     
  		$result1 = curl_exec($ch1);
  		$publicKey_json = json_decode($result1, true); 
  		$publicKey = $publicKey_json['account']['publicKey'];
		$existQuery = "SELECT address,balance FROM miners WHERE balance!='0'";
		$existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
		while ($row=mysqli_fetch_row($existResult)){
			$payer_adr = $row[0];
			$balance = $row[1];
			$balanceinlsk = floatval($balance/100000000);
			echo "\n-------------------------------------------";
			echo "\n".$payer_adr.' -> '.$balanceinlsk;
			if ($balanceinlsk > $payout_threshold) {
				$deduced_by_fee = $balanceinlsk - $fixed_withdraw_fee;
				$deduced_by_fee = $deduced_by_fee * 100000000;
				if (!$secret2) {
					$data = array("secret" => $secret1, "amount" => $deduced_by_fee, "recipientId" => $payer_adr, "publicKey" => $publicKey); 
				} else {
					$data = array("secret" => $secret1, "amount" => $deduced_by_fee, "recipientId" => $payer_adr, "publicKey" => $publicKey, "secondSecret" => $secret2);
				}
				$data_string = json_encode($data);  
				$ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/transactions');                                                                      
				curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "PUT");              
				curl_setopt($ch1, CURLOPT_POSTFIELDS, $data_string);                                                                        
				curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch1, CURLOPT_HTTPHEADER, array(                                                                          
    				'Content-Type: application/json',                                                                                
    				'Content-Length: ' . strlen($data_string))                                                                       
				);  
				$result1 = curl_exec($ch1);
				$json_arr = json_decode($result1, true); 
				$txid = $json_arr['transactionId'];
				print_r($result1);
				if ($txid) {
					$timestamp = time();
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