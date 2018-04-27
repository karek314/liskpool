<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../../config.php');
require_once('../../lisk-php/main.php');

while(1){
	$accounts = array();
	for ($i=0; $i < 1500; $i++) { 
		$passphrase = GenerateAccount('12');
	    $output = getKeysFromSecret($passphrase,true);
		$tx = CreateTransaction(getAddressFromPublicKey($output['public']), 10000000000+rand(1,1000000), "crash there easy", false, false, -10);
		$tx_resp = SendTransaction(json_encode($tx),$server);
		$status = $tx_resp['data']['message'];
		var_dump($tx_resp);
		echo "\nSending ~100 LSK Status to account [".getAddressFromPublicKey($output['public'])."]: ".$status;
		array_push($accounts, $passphrase);
	}
	csleep(120);
	foreach ($accounts as $key => $pass) {
		$votes = array();
		$votes[] = '+e08ed949edb5ddc3eea05e6c0258d4a942e93c28fb456716ab08087330e21435';
		$tx_vote = Vote($votes,$pass);
		$tx_resp = SendTransaction(json_encode($tx_vote),$server);
		$status = $tx_resp['data']['message'];
		var_dump($tx_resp);
		$output = getKeysFromSecret($pass,true);
		echo "\nVote Status from [".getAddressFromPublicKey($output['public'])."]: ".$status;
	}
}
?>