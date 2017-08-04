<?php


function IsBalanceOkToWithdraw($mysqli_handle,$config,$debug = true){
	$balanceinlsk_p = getCurrentBalance($config,$debug);
	$total = getCurrentDBUsersBalance($mysqli_handle,$debug);
	if ($debug) {
		echo "\n\nCalculated Profit for voters:".$total;
		echo "\nCurrent owned wallet balance:".$balanceinlsk_p;
	}
	if ($balanceinlsk_p > $total) {
		return true;
	} else {
		return false;
	}
}


function getCurrentBalance($config,$debug = true){
	$m = new Memcached();
	$m->addServer('localhost', 11211);
	$lisk_host = $m->get('lisk_host');
	$lisk_port = $m->get('lisk_port');
	$delegate = $config['delegate_address'];
	$pool_fee = floatval(str_replace('%', '', $config['pool_fee']));
	$pool_fee_payout_address = $config['pool_fee_payout_address'];
	$protocol = $config['protocol'];
	$ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/accounts?address='.$delegate);                                                                      
	curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");                                                                                      
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);     
	$result1 = curl_exec($ch1);
	$publicKey_json = json_decode($result1, true); 
	$publicKey = $publicKey_json['account']['publicKey'];
	$pool_balance = $publicKey_json['account']['balance'];
	$balanceinlsk_p = floatval($pool_balance/100000000);
	return $balanceinlsk_p;
}


function getCurrentDBUsersBalance($mysqli_handle,$debug = true){
	$existQuery = "SELECT address,balance FROM miners WHERE balance!='0'";
	$existResult = mysqli_query($mysqli_handle,$existQuery)or die("Database Error");
	$total = 0;
	while ($row=mysqli_fetch_row($existResult)){
		$payer_adr = $row[0];
		$balance = $row[1];
		$balanceinlsk = floatval($balance/100000000);
		if ($debug) {
			echo "\n".$payer_adr.' -> '.$balanceinlsk;
		}
		$total = $total + $balanceinlsk;
	}
	return $total;
}


?>
