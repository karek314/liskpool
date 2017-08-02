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
  $approval = $d_data['approval'];
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
    $total_voters_power_d = $approval;
    if ($total_voters_power_d != '' && $total_voters_power_d != ' ') {
      AppendChartData(false,$total_voters_power_d,$cur_time,'approval',$public_directory);
    }
    $balanceinlsk_p = floatval($pool_balance/100000000);
    if ($balanceinlsk_p != '' && $balanceinlsk_p != ' ') {
      AppendChartData(false,$balanceinlsk_p,$cur_time,'balance',$public_directory);
    }
    if ($voters_count != '' && $voters_count != ' ') {
      AppendChartData(false,$voters_count,$cur_time,'voters',$public_directory);
    }
    if ($rank != '' && $rank != ' ') {
      AppendChartData(false,$rank,$cur_time,'rank',$public_directory);
    }
    $voters_task = "SELECT address,balance FROM miners";
    $task_result = mysqli_query($mysqli,$voters_task)or die("Database Error");
    while ($row=mysqli_fetch_row($task_result)){
      $voter_address = $row[0];
      $balanceinlsk = $row[1];
      $balanceinlsk = floatval($balanceinlsk/100000000);
      if ($balanceinlsk != 0) {
        AppendChartData('voters',$balanceinlsk,$cur_time,$voter_address,$public_directory);
      }
    }
    $end_time = time();
    $took = $end_time - $start_time;
    $time_sleep = 60-$took;
    if ($time_sleep < 1) {
      $time_sleep = 1;
    }
    echo "\nAdding...".$df.' took:'.$took.' sleep:'.$time_sleep.' Active voters -> '.$voters_count.' Approval -> '.$approval.' votepower -> '.$total_voters_power.'  balance -> '.$balanceinlsk_p.'  rank -> '.$rank;
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