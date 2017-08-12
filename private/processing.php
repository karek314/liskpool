<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../config.php');
require_once('../lisk-php/main.php');
require_once('logging.php');
$delegate = $config['delegate_address'];
$pool_fee = floatval(str_replace('%', '', $config['pool_fee']));
$pool_fee_payout_address = $config['pool_fee_payout_address'];
$protocol = $config['protocol'];
$cap_balance = $config['cap_balance'];
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
	clog("[".$df."]Getting last 100 blocks forged...\n",'processing');
	//Retrive Public Key
	$json = AccountForAddress($delegate,$server);
	$publicKey = $json['account']['publicKey'];
	//Retrive last forged block
	$forged_block_json = GetBlocksBy($publicKey,$server); 
	$block_jarray = $forged_block_json['blocks'];
	$blocks_count = count($block_jarray);
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
	if ($blocks_count>0) {
		foreach ($block_jarray as $key => $value) {
			$value_block = $value['height'];
			$value_reward = $value['reward'];
			$forged_block = $value_block;
			$forged_block_revenue = $value_reward;
			clog("[".$key."]Forged Block: ".$value_block." with reward:".$value_reward,'processing');
			$task = "SELECT * FROM blocks WHERE blockid = '$forged_block' LIMIT 1";	
			$query = mysqli_query($mysqli,$task) or die("Database Error");	
			if($query->num_rows == 0) {
				clog("processing block with height: ".$value_block,'processing');
				$task = "INSERT INTO blocks (blockid) SELECT * FROM (SELECT '$forged_block') AS tmp WHERE NOT EXISTS (SELECT * FROM blocks WHERE blockid = '$forged_block' LIMIT 1)";
				$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
				$affected = $mysqli -> affected_rows;

				if ($forged_block_revenue != 0) {
					clog("Forged block at height:".$forged_block,'processing');
					//Retrive current voters
					$voters = GetVotersFor($publicKey,$server);
					$voters_array = null;
					$voters_array = $voters['accounts'];

					//Add Likstats contributors
					$liskstats_task = "SELECT DISTINCT object FROM liskstats";
					$liskstats_result = mysqli_query($mysqli,$liskstats_task)or die("Database Error");
					$tmp_arr = array();
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
							clog("LiskStats Contributor [".$object."] - Payable",'processing');
							array_push($tmp_arr, $object);
						} else {
							clog("LiskStats Contributor [".$object."] - NOT Payable",'processing');
						}
					}
					//Will not split anything if liskstats.php script is not running and getting current contributors.
					$total_weight_to_distribute = 150000000000000;
					$count_of_current_contributors = count($tmp_arr);
					clog("LiskStats Contributors Count:".$count_of_current_contributors,'processing');
					$single_weight = (string)floor($total_weight_to_distribute/$count_of_current_contributors);
					foreach ($tmp_arr as $key => $value) {
						clog("Adding LiskStats Contributor [".$value."] with balance:".$single_weight,'processing');
						$t_array = array('username' => NULL,'address' => $value,'publicKey' => '','balance' => $single_weight);
						array_push($voters_array, $t_array);
					}
					//var_dump($voters_array);
					//die();
					
					clog("Current Voters:",'processing');
					$total_voters_power = 0;
					foreach ($voters_array as $key => $value) {
						//Count total power of users and add them to miners table if not added before
						$address = $value['address'];
						$balance = $value['balance'];
						if ($balance > $cap_balance) {
							$bdifference = $balance-$cap_balance;
							$capped_balance = $bdifference/1000;
							clog("TCap balance[".$balance."] -> ".$capped_balance,'processing');
							$balance = $capped_balance;
						}
						$total_voters_power = $total_voters_power + $balance;
						$task = "INSERT INTO miners (address,balance) SELECT * FROM (SELECT '$address','0') AS tmp WHERE NOT EXISTS (SELECT * FROM miners WHERE address = '$address' LIMIT 1)";
						$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
					}
					clog("Total Power -> ".$total_voters_power,'processing');
					//Split forging reward
					clog("Mined block worth -> ".$forged_block_revenue,'processing');
					clog("Pool fee ".$pool_fee.'%','processing');
					if ($pool_fee > 0) {
						//Pool takes fee - lets deduce
						$pool_revenue = ($forged_block_revenue * $pool_fee)/100;
						$forged_block_revenue = $forged_block_revenue - $pool_revenue;
						$task = "INSERT INTO miners (address,balance) SELECT * FROM (SELECT '$pool_fee_payout_address','0') AS tmp WHERE NOT EXISTS (SELECT * FROM miners WHERE address = '$address' LIMIT 1)";
						$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
						$task = "UPDATE miners SET balance=balance+'$pool_revenue' WHERE address='$pool_fee_payout_address';";	
						$query = mysqli_query($mysqli,$task) or die("Database Error");	
						clog("Pool revenue -> ".$pool_revenue,'processing');
					}
					clog("Total Pool Revenue to Split -> ".$forged_block_revenue,'processing');

					foreach ($voters_array as $key => $value) {
						$address = $value['address'];
						$balance = $value['balance'];
						if ($balance > $cap_balance) {
							$bdifference = $balance-$cap_balance;
							$capped_balance = $bdifference/1000;
							clog("Cap balance[".$balance."] -> ".$capped_balance,'processing');
							$balance = $capped_balance;
						}
						$total = $total_voters_power;
						$precentage = $balance / $total;
						$user_revenue = $precentage * $forged_block_revenue;
						clog($key.' => '.$address.' => '.$balance.' / '.$total.' = '.$precentage.'% -> '.$user_revenue,'processing');
						$task = "UPDATE miners SET balance=balance+'$user_revenue' WHERE address='$address';";	
						$query = mysqli_query($mysqli,$task) or die("Database Error");
						$splitted = $splitted + $user_revenue;
					}
					clog("Splitted:".$splitted,'processing');
					clog("___Block:".$forged_block_revenue,'processing');
				} else {
					clog("Block reward = 0 ??",'processing');
				}
			} else {
				clog("Already processed: ".$value_block,'processing');
			}
		}
	} else {
		clog("Empty response, no blocks to iterate",'processing');
	}
	clog("2s sleep",'processing');
	sleep(2);
}
?>