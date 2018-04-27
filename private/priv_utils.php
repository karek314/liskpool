<?php


function csleep($wait_time){
	$org_wait_time = $wait_time;
	$start_time = time();
	$chr = 50;
	echo "\n";
	$chars = array();
	$wait_time = $wait_time/$chr;
	for ($i=0; $i <= $chr; $i++) {
		$chars[] = "#";
		$count = count($chars);
		$string = '[';
		$string .= implode('',$chars);
		$empty = $chr-$count;
		for ($j=0; $j <= $empty; $j++) { 
			$string .= ' ';
		}
		$precent = (double)($i/$chr)*100;
		$current_time = time();
		$diff = $current_time-$start_time;
		$left = $org_wait_time-$diff;
		echo "\rSleeping ".$left."s [".$i."/".$chr."(".$wait_time."s)] ".$string."] ".$precent."%";
		if ($wait_time > 300) {
			sleep(floor($wait_time));
		} else {
			$u_wait_time = $wait_time*1000000;
			usleep($u_wait_time);
		}
	}
}


function IsBalanceOkToWithdraw($mysqli_handle,$server,$delegate,$debug = true){
	$balanceinlsk_p = getCurrentBalance($delegate,$server,$debug);
	$total = getCurrentDBUsersBalance($mysqli_handle,$debug);
	echo "\n\nCalculated Profit for voters:".$total;
	echo "\nCurrent owned wallet balance:".$balanceinlsk_p;
	if ($balanceinlsk_p > $total) {
		return true;
	} else {
		return false;
	}
}


function getCurrentBalance($delegate,$server,$debug = true){
	$json = AccountForAddress($delegate,$server); 
	$pool_balance = $json['data'][0]['balance'];
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


function AppendChartData($subdir,$value,$time,$name,$public_directory){
  if (!$subdir) {
    $real_path = realpath('../'.$public_directory.'/data').'/'.$name.'.json';
  } else {
    $real_path = realpath('../'.$public_directory.'/data/'.$subdir).'/'.$name.'.json';
  }
  $time = $time*1000;
  if (file_exists($real_path)) {
    $fh = fopen(realpath($real_path), 'r+');
    $stat = fstat($fh);
    ftruncate($fh, $stat['size']-1);
    fclose($fh);
    $fh = fopen(realpath($real_path), 'a+');
    $data = ',['.$time.','.$value.']]';
    fwrite($fh, $data);
    fclose($fh);
  } else {
    $data = '[['.$time.','.$value.']]';
    file_put_contents($real_path, $data);
    chmod($real_path, 0664);
  }
}


?>
