<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
$config = include('../config.php');
$df = 0;
$delegate = $config['delegate_address'];
$pool_fee = floatval(str_replace('%', '', $config['pool_fee']));
$pool_fee_payout_address = $config['pool_fee_payout_address'];
$protocol = $config['protocol'];
$public_directory = $config['public_directory'];

while(1) {
  $m = new Memcached();
  $m->addServer('localhost', 11211);
  $lisk_host = $m->get('lisk_host');
  $lisk_port = $m->get('lisk_port');
  $df++;
  $start_time = time();
  echo "\nFetching data...\n";
  //Retrive Public Key
  $ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/accounts?address='.$delegate);                                                                      
  curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");                                                                                      
  curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);     
  $result1 = curl_exec($ch1);
  $publicKey_json = json_decode($result1, true); 
  $publicKey = $publicKey_json['account']['publicKey'];
  $pool_balance = $publicKey_json['account']['balance'];
  //get forging delegate info
  $ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/delegates/get/?publicKey='.$publicKey);
  curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");                                                                                      
  curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);     
  $result1 = curl_exec($ch1);
  $d_data = json_decode($result1, true); 
  $d_data = $d_data['delegate'];
  $rank = $d_data['rate'];
  //Retrive voters
  $ch1 = curl_init($protocol.'://'.$lisk_host.':'.$lisk_port.'/api/delegates/voters?publicKey='.$publicKey);
  curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");                                                                                      
  curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);     
  $result1 = curl_exec($ch1);
  $voters = json_decode($result1, true); 
  $voters_array = $voters['accounts'];
  $voters_count = count($voters_array);
  $total_voters_power = 0;
  foreach ($voters_array as $key => $value) {
    $balance = $value['balance'];
    $total_voters_power = $total_voters_power + $balance;
  }
  if ($voters_count != 0 && $total_voters_power) {
    $cur_time = time();
    $mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
    $total_voters_power_d = $total_voters_power/100000000000000;
    if ($total_voters_power_d != '' && $total_voters_power_d != ' ') {
      $add2Stats = "INSERT INTO pool_votepower (votepower, val_timestamp) VALUES ('$total_voters_power_d', '$cur_time')";
      $querydone = mysqli_query($mysqli,$add2Stats) or die("Database Error 0");
    }
    $balanceinlsk_p = floatval($pool_balance/100000000);
    if ($balanceinlsk_p != '' && $balanceinlsk_p != ' ') {
      $add2Stats = "INSERT INTO pool_balance (value, var_timestamp) VALUES ('$balanceinlsk_p', '$cur_time')";
      $querydone = mysqli_query($mysqli,$add2Stats) or die("Database Error 0");
    }
    if ($voters_count != '' && $voters_count != ' ') {
      $add2Stats = "INSERT INTO pool_voters (value, var_timestamp) VALUES ('$voters_count', '$cur_time')";
      $querydone = mysqli_query($mysqli,$add2Stats) or die("Database Error 0");
    }
    if ($rank != '' && $rank != ' ') {
      $add2Stats = "INSERT INTO pool_rank (value, var_timestamp) VALUES ('$rank', '$cur_time')";
      $querydone = mysqli_query($mysqli,$add2Stats) or die("Database Error 0");
    }
    $db_users_count = 0;
    $users_data = '';
    $existQuery = "SELECT address,balance FROM miners";
    $existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
    while ($row=mysqli_fetch_row($existResult)){
      $val1 = $row[0];
      $val2 = $row[1];
      $balanceinlsk = floatval($val2/100000000);
      if ($balanceinlsk != 0) {
        $users_data = $users_data.' '."('$val1', '$balanceinlsk', '$cur_time'),";
      }
      $db_users_count++;
    }
    if ($users_data != '') {
      $mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
      $users_data = substr($users_data, 0, -1);
      $add2Stats = "INSERT INTO miner_balance (miner, value, var_timestamp) VALUES".$users_data;
      $querydone = mysqli_query($mysqli,$add2Stats) or die('erros miner_balance');
    }
    //Update data for /charts/
    //approval
    $existQuery = "SELECT votepower,val_timestamp FROM pool_votepower ORDER BY id ASC";
    $existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
    $count = 0;
    $count = mysqli_num_rows($existResult);
    $x=0;
    $x++;
    $json_output = '[';
    while ($row=mysqli_fetch_row($existResult)){
      $stamp = $row[1]*1000;
      $real = $row[0];///(1000*1000);
      $x++;
      $json_output .= '['.$stamp.','.$real.']';
      if ($x-1 != $count) {
        $json_output .= ',';
      }
    }
    $json_output .= ']';
    file_put_contents('../'.$public_directory.'/data/approval.json', $json_output);
    //pool_rank
    $existQuery = "SELECT value,var_timestamp FROM pool_rank ORDER BY id ASC";
    $existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
    $count = 0;
    $count = mysqli_num_rows($existResult);
    $x=0;
    $x++;
    $json_output = '[';
    while ($row=mysqli_fetch_row($existResult)){
      $stamp = $row[1]*1000;
      $real = $row[0];
      $x++;
      $json_output .= '['.$stamp.','.$real.']';
      if ($x-1 != $count) {
        $json_output .= ',';
      }
    }
    $json_output .= ']';
    file_put_contents('../'.$public_directory.'/data/rank.json', $json_output);
    //pool_balance
    $existQuery = "SELECT value,var_timestamp FROM pool_balance ORDER BY id ASC";
    $existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
    $count = 0;
    $count = mysqli_num_rows($existResult);
    $x=0;
    $x++;
    $json_output = '[';
    while ($row=mysqli_fetch_row($existResult)){
      $stamp = $row[1]*1000;
      $real = $row[0];
      $x++;
      $json_output .= '['.$stamp.','.$real.']';
      if ($x-1 != $count) {
        $json_output .= ',';
      }
    }
    $json_output .= ']';
    file_put_contents('../'.$public_directory.'/data/balance.json', $json_output);
    //voters
    $existQuery = "SELECT value,var_timestamp FROM pool_voters ORDER BY id ASC";
    $existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
    $count = 0;
    $count = mysqli_num_rows($existResult);
    $x=0;
    $x++;
    $json_output = '[';
    while ($row=mysqli_fetch_row($existResult)){
      $stamp = $row[1]*1000;
      $real = $row[0];
      $x++;
      $json_output .= '['.$stamp.','.$real.']';
      if ($x-1 != $count) {
        $json_output .= ',';
      }
    }
    $json_output .= ']';
    file_put_contents('../'.$public_directory.'/data/voters.json', $json_output);
    //End of chart data updating
  
    $end_time = time();
    $took = $end_time - $start_time;
    $time_sleep = 60-$took;
    if ($time_sleep < 1) {
      $time_sleep = 1;
    }
    echo "\nAdding...".$df.' took:'.$took.' sleep:'.$time_sleep.' Active voters -> '.$voters_count.' votepower -> '.$total_voters_power.'  balance -> '.$balanceinlsk_p.'  rank -> '.$rank;
    sleep($time_sleep);
  } else {
    //Can't get data, dont mess chart
    $end_time = time();
    $took = $end_time - $start_time;
    $time_sleep = 60-$took;
    if ($time_sleep < 1) {
      $time_sleep = 1;
    }
    sleep($time_sleep);
    echo "Can't get data...";
  }
}
?>