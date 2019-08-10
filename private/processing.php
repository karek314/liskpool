<?php
error_reporting(error_reporting() & ~E_NOTICE);
require_once('priv_utils.php');
$config = include('../config.php');
require_once('../lisk-php/main.php');
require_once('logging.php');
$delegate = $config['delegate_address'];
$pool_fee = new Math_BigInteger(floatval(str_replace('%', '', $config['pool_fee']))*10);
$pool_fee_payout_address = $config['pool_fee_payout_address'];
$protocol = $config['protocol'];
$support_standby_delegates = $config['support_standby_delegates'];
$support_standby_delegates_amount = $config['support_standby_delegates_amount'];
$cap_balance = new Math_BigInteger($config['cap_balance']);
$divide1k = new Math_BigInteger('1000');
$df = 0;
	
while(1) {
	$m = new Memcached();
	$m->addServer('localhost', 11211);
	$server = getCurrentServer($m);
  	clog("Current server set to: ".$server,'processing');
	$df++;
	clog("[".$df."]Getting last 100 blocks forged...",'processing');
	//Retrive Public Key
	$json = AccountForAddress($delegate,$server);
	$publicKey = $json['data'][0]['publicKey'];
	clog("[".$df."]PublicKey:".$publicKey,'processing');
	while(!$json && count($publicKey) != 64){
		clog("[".$df."]Public key was empty, retrying...",'processing');
		csleep(5);
		$json = AccountForAddress($delegate,$server);
		$publicKey = $json['data'][0]['publicKey'];
		clog("[".$df."]PublicKey:".$publicKey,'processing');
	}
	//Retrive last forged block
	$forged_block_json = GetBlocksBy($publicKey,$server); 
	$block_jarray = $forged_block_json['data'];
	$blocks_count = count($block_jarray);
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
	if ($blocks_count>0) {
		foreach ($block_jarray as $key => $value) {
			$forged_block_revenue = new Math_BigInteger('0');
			$total_voters_power = new Math_BigInteger('0');
			$splitted = new Math_BigInteger('0');
			$forged_block = $value['height'];
			$forged_block_revenue = new Math_BigInteger($value['reward']);
			clog("[".$key."]Forged Block: ".$forged_block." with reward:".$forged_block_revenue->toString(),'processing');
			$task = "SELECT * FROM blocks WHERE blockid = '$forged_block' LIMIT 1";	
			$query = mysqli_query($mysqli,$task) or die("Database Error");	
			if($query->num_rows == 0) {
				if ($forged_block_revenue != 0) {
					clog("Forged block at height:".$forged_block,'processing');
					//Retrive current voters
					$voters_array = null;
					$cached = ReadCache('voters_list');
  					$voters_array = json_decode($cached,true);
					$voters_count = count($voters_array);
					clog("Current voters count read form memory:".$voters_count,'processing');
					if (!$voters_array) {
						clog("[".$df."]Couldn't get voters list, sleeping 10s then breaking to main loop and retrying...",'processing');
						csleep(10);
						break;
					} else {
						clog("processing block with height: ".$forged_block,'processing');
						$task = "INSERT INTO blocks (blockid) SELECT * FROM (SELECT '$forged_block') AS tmp WHERE NOT EXISTS (SELECT * FROM blocks WHERE blockid = '$forged_block' LIMIT 1)";
						$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
						$affected = $mysqli -> affected_rows;
					}
					clog("Current Voters:",'processing');
					foreach ($voters_array as $key => $value) {
						//Count total power of users and add them to miners table if not added before
						$address = $value['address'];
						$balance = new Math_BigInteger($value['balance']);
						if ($balance > $cap_balance) {
							$capped_balance = new Math_BigInteger('0');
							$bdifference = new Math_BigInteger('0');
							$bdifference = $balance->subtract($cap_balance);
							list($cp_quotient, $cp_remainder) = $bdifference->divide($divide1k);
							$capped_balance = $cap_balance->add($cp_quotient);
							clog("TCap balance[".$balance->toString()."] -> ".$capped_balance->toString(),'processing');
							$balance = $capped_balance;
						}
						$total_voters_power = $total_voters_power->add($balance);
						$task = "INSERT INTO miners (address,balance) SELECT * FROM (SELECT '$address','0') AS tmp WHERE NOT EXISTS (SELECT * FROM miners WHERE address = '$address' LIMIT 1)";
						$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
					}
					clog("Total Power -> ".$total_voters_power->toString(),'processing');
					//Split forging reward
					clog("Mined block worth -> ".$forged_block_revenue->toString(),'processing');
					$pool_fee_in_precentage = $pool_fee->toString();
					$pool_fee_tmp = str_split($pool_fee_in_precentage);
					$fee_count = count($pool_fee_tmp);
					$pool_fee_in_precentage = "";
					for ($i=0; $i < $fee_count; $i++) { 
						if ($i != $fee_count-1) {
							$pool_fee_in_precentage .= $pool_fee_tmp[$i];
						} else {
							$pool_fee_in_precentage .= ','.$pool_fee_tmp[$i];
						}
					}
					clog("Pool fee ".$pool_fee_in_precentage.'%','processing');
					if ($pool_fee > 0) {
						$pool_revenue = new Math_BigInteger('0');
						$pool_revenue = $forged_block_revenue->multiply($pool_fee);
						list($pf_quotient, $pf_remainder) = $pool_revenue->divide($divide1k);
						$pool_revenue = $pf_quotient;
						$pool_revenue_string = $pool_revenue->toString();
						$forged_block_revenue = $forged_block_revenue->subtract($pool_revenue);
						$task = "INSERT INTO miners (address,balance) SELECT * FROM (SELECT '$pool_fee_payout_address','0') AS tmp WHERE NOT EXISTS (SELECT * FROM miners WHERE address = '$address' LIMIT 1)";
						$query = mysqli_query($mysqli,$task) or die(mysqli_error($mysqli));
						$task = "UPDATE miners SET balance=balance+'$pool_revenue_string' WHERE address='$pool_fee_payout_address';";	
						$query = mysqli_query($mysqli,$task) or die("Database Error");	
						clog("Pool revenue -> ".$pool_revenue_string,'processing');
					}
					clog("Total Pool Revenue to Split -> ".$forged_block_revenue->toString(),'processing');
					foreach ($voters_array as $key => $value) {
						$address = $value['address'];
						$balance = new Math_BigInteger($value['balance']);
						if ($balance > $cap_balance) {
							$capped_balance = new Math_BigInteger('0');
							$bdifference = new Math_BigInteger('0');
							$bdifference = $balance->subtract($cap_balance);
							list($cp_quotient, $cp_remainder) = $bdifference->divide($divide1k);
							$capped_balance = $cap_balance->add($cp_quotient);
							clog("Cap balance[".$balance->toString()."] -> ".$capped_balance->toString(),'processing');
							$balance = $capped_balance;
						}
						$voter_revenue = new Math_BigInteger('0');
						$voter_revenue = $balance->multiply($forged_block_revenue);
						list($vrprop_quotient, $vrprop_remainder) = $voter_revenue->divide($total_voters_power);
						$voter_revenue_str = $vrprop_quotient->toString();
						clog($key.' => '.$address.' => '.$balance->toString().' / '.$total_voters_power->toString().' = '.$voter_revenue_str,'processing');
						$task = "UPDATE miners SET balance=balance+'$voter_revenue_str' WHERE address='$address';";	
						$query = mysqli_query($mysqli,$task) or die("Database Error");
						$splitted = $splitted->add($vrprop_quotient);
					}
					clog("Splitted:".$splitted->toString(),'processing');
					clog("___Block:".$forged_block_revenue->toString(),'processing');
				} else {
					clog("Block reward = 0 ??",'processing');
				}
			} else {
				clog("Already processed: ".$forged_block,'processing');
			}
		}
	} else {
		clog("Empty response, no blocks to iterate",'processing');
	}
	clog("120s sleep",'processing');
	csleep(120);
}
?>