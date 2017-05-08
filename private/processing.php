<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
$delegate = $config['delegate_address'];
$pool_fee = floatval(str_replace('%', '', $config['pool_fee']));
$pool_fee_payout_address = $config['pool_fee_payout_address'];
$protocol = $config['protocol'];
$df = 0;
	
while(1) {
	$m = new Memcached();
	$m->addServer('localhost', 11211);
	$lisk_host = $m->get('lisk_host');
	$lisk_port = $m->get('lisk_port');
	$df++;
	$forged_block_revenue = 0;
	$total_voters_power = 0;
	$total = 0;
	$precentage = 0;
	$user_revenue = 0;
	$splitted = 0;
	echo "\n";echo $df.":Getting last 100 blocks forged...\n";
	//Retrive Public Key
	$ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/accounts?address='.$delegate);                                                                      
	curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");                                                                                      
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);     
	$result1 = curl_exec($ch1);
	$publicKey_json = json_decode($result1, true); 
	$publicKey = $publicKey_json['account']['publicKey'];
	//Retrive last forged block
	$ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/blocks/?generatorPublicKey='.$publicKey.'&limit=100&offset=0&orderBy=height:desc');
	curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");                                                                                      
	curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);     
	$result1 = curl_exec($ch1);
	$forged_block_json = json_decode($result1, true); 
	$block_jarray = $forged_block_json['blocks'];

	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
	foreach ($block_jarray as $key => $value) {
		$value_block = $value['height'];
		$value_reward = $value['reward'];
		$forged_block = $value_block;
		$forged_block_revenue = $value_reward;
		echo "\n[".$key."]Forged Block: ".$value_block." with reward:".$value_reward;
		$task = "SELECT * FROM blocks WHERE blockid = '$forged_block' LIMIT 1";	
		$query = mysqli_query($mysqli,$task) or die("Database Error");	
		if($query->num_rows == 0) {
			echo "\nProcessing block with height: ".$value_block;
			$task = "INSERT INTO blocks (blockid) SELECT * FROM (SELECT '$forged_block') AS tmp WHERE NOT EXISTS (SELECT * FROM blocks WHERE blockid = '$forged_block' LIMIT 1)";
			$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
			$affected = $mysqli -> affected_rows;

			if ($forged_block_revenue != 0) {
				echo "\nForged block at height:".$forged_block;
				//Retrive current voters
				$ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/delegates/voters?publicKey='.$publicKey);
				curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");                                                                                      
				curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);     
				$result1 = curl_exec($ch1);
				$voters = json_decode($result1, true); 
				$voters_array = $voters['accounts'];

				//Add Likstats contributors
				$liskstats_task = "SELECT object FROM liskstats";
				$liskstats_result = mysqli_query($mysqli,$liskstats_task)or die("Database Error");
				while ($row=mysqli_fetch_row($liskstats_result)){
					$object = $row[0];
					$isPayable = false;
					if (strpos($object, 'L') !== false) {
						$tmp = str_replace('L', '', $object);
						if (is_numeric($tmp)) {
							$isPayable = true;
						}
					}
					if ($isPayable) {
						echo "\nLiskStats Contributor [".$object."] - Payable";
						$t_array = array('username' => NULL,'address' => $object,'publicKey' => '','balance' => "5000000000000");
						array_push($voters_array, $t_array);
					} else {
						echo "\nLiskStats Contributor [".$object."] - NOT Payable";
					}
				}
				//var_dump($voters_array);
				//die();
				
				echo "\nCurrent Voters:";
				$total_voters_power = 0;
				foreach ($voters_array as $key => $value) {
					//Count total power of users and add them to miners table if not added before
					$address = $value['address'];
					$balance = $value['balance'];
					$total_voters_power = $total_voters_power + $balance;
					$task = "INSERT INTO miners (address,balance) SELECT * FROM (SELECT '$address','0') AS tmp WHERE NOT EXISTS (SELECT * FROM miners WHERE address = '$address' LIMIT 1)";
					$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
				}
				echo "\nTotal Power -> ".$total_voters_power;
				//Split forging reward
				echo "\nMined block worth -> ".$forged_block_revenue;
				echo "\nPool fee ".$pool_fee.'%';
				if ($pool_fee > 0) {
					//Pool takes fee - lets deduce
					$pool_revenue = ($forged_block_revenue * $pool_fee)/100;
					$forged_block_revenue = $forged_block_revenue - $pool_revenue;
					$task = "INSERT INTO miners (address,balance) SELECT * FROM (SELECT '$pool_fee_payout_address','0') AS tmp WHERE NOT EXISTS (SELECT * FROM miners WHERE address = '$address' LIMIT 1)";
					$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
					$task = "UPDATE miners SET balance=balance+'$pool_revenue' WHERE address='$pool_fee_payout_address';";	
					$query = mysqli_query($mysqli,$task) or die("Database Error");	
					echo "\nPool revenue -> ".$pool_revenue;
				}
				echo "\nTotal Pool Revenue to Split -> ".$forged_block_revenue;

				foreach ($voters_array as $key => $value) {
					$address = $value['address'];
					$balance = $value['balance'];
					$total = $total_voters_power;
					$precentage = $balance / $total;
					$user_revenue = $precentage * $forged_block_revenue;
					echo "\n".$key.' => '.$address.' => '.$balance.' / '.$total.' = '.$precentage.'% -> '.$user_revenue;
					$task = "UPDATE miners SET balance=balance+'$user_revenue' WHERE address='$address';";	
					$query = mysqli_query($mysqli,$task) or die("Database Error");
					$splitted = $splitted + $user_revenue;
				}
				echo "\nSplitted:".$splitted;
				echo "\n___Block:".$forged_block_revenue;
			} else {
				echo "\nBlock reward = 0 ??";
			}
		} else {
			echo "\nAlready processed: ".$value_block;
		}
	}
	echo "\nTaking a nap for 2s";
	sleep(2);
}
?>