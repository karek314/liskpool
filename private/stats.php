<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
require_once('priv_utils.php');
require_once('../lisk-php/main.php');
require_once('logging.php');
$config = include('../config.php');
$df = 0;
$delegate = $config['delegate_address'];
$pool_fee = floatval(str_replace('%', '', $config['pool_fee']));
$protocol = $config['protocol'];
$public_directory = $config['public_directory'];
$fee = $config['pool_fee_payout_address'];
$fancy_secret = $config['fancy_withdraw_hub'];

while(1) {
  $m = new Memcached();
  $m->addServer('localhost', 11211);
  $df++;
  $start_time = time();
  clog("Fetching data...",'stats');
  $mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
  //Get first 100 biggst voters and cache it in memory
  $task = "SELECT balance,address FROM miners WHERE address!='$delegate' ORDER BY balance DESC LIMIT 100;";
  $tresult = mysqli_query($mysqli,$task)or die("Database Error");
  $forged_voters = array();
  while ($row=mysqli_fetch_row($tresult)){
    $balance = floatval($row[0]/100000000);
    $address = $row[1];
    array_push($forged_voters, array('balance' => array('lsk' => $balance, 'raw' => $row[0]),'address' => $address));
  }
  $m->set('internal_voters_balance', $forged_voters, 3600*365);
  //Get last blocks
  $task = "SELECT blockid FROM blocks ORDER BY id DESC LIMIT 50;";
  $tresult = mysqli_query($mysqli,$task)or die("Database Error");
  $last_blocks = array();
  while ($row=mysqli_fetch_row($tresult)){
    array_push($last_blocks, $row[0]);
  }
  $m->set('last_blocks', $last_blocks, 3600*365);
  //Read all forgers balance
  $task = "SELECT balance,address FROM miners ORDER BY balance DESC LIMIT 50000;";
  $result = mysqli_query($mysqli,$task)or die("Database Error");
  $data = array();
  while ($row=mysqli_fetch_row($result)){
    $balance = $row[0];
    $address = $row[1];
    if ($address != $fee) {
      $balanceinlsk = floatval($balance/100000000);
      $balance_ar = array('lsk' => number_format($balanceinlsk, 8),'raw' => $balance);
      $tmp = array('address' => $address,'balance' => $balance_ar);
      array_push($data, $tmp);
    }
  }
  $m->set('forgers_balance', $data, 3600*365);
  //Retrive Public Key
  $json = AccountForAddress($delegate,$server);
  $m->set('delegate_account', $json, 3600*365);
  $publicKey = $json['data'][0]['publicKey'];
  $pool_balance = $json['data'][0]['balance'];
  //get forging delegate info
  $d_data = GetDelegateInfo($publicKey,$server);
  $m->set('d_data', $d_data, 3600*365);
  $rank = $d_data['data'][0]['rank'];
  $approval = $d_data['data'][0]['approval'];
  $pool_productivity = $d_data['data'][0]['productivity'];
  //Retrive voters
  $voters_array = null;
  clog("[".$df."]Reading voters list from cache...",'processing');
  $cached = ReadCache('voters_list');
  $voters_array = json_decode($cached,true);
  $voters_count = count($voters_array);
  clog("[".$df."]Voters List Count:".$voters_count,'processing');
  $total_voters_power = 0;
  $cur_time = time();
  $cur_voters = array();
  foreach ($voters_array as $key => $value) {
    $balance = $value['balance'];
    $total_voters_power = $total_voters_power + $balance;
    $balanceinlsk = floatval($balance/100000000);
    $address = $value['address'];
    $cur_voters["'$address'"] = $address; 
    AppendChartData('voters/balance',$balanceinlsk,$cur_time,$address,$public_directory);
  }
  //Add Likstats contributors balances
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
      if (isset($cur_voters["'$object'"])) {
      } else {  
        $lscon = AccountForAddress($object,$server);
        if ($lscon) {
          $lscon_balance = $lscon['account']['balance'];
          $balanceinlsk = floatval($lscon_balance/100000000);
          AppendChartData('voters/balance',$balanceinlsk,$cur_time,$object,$public_directory);
        }
      }
    }
  }
  if ($voters_count != 0 && $total_voters_power) {
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
    $pool_lsk_reserve = getCurrentBalance($delegate,$server,false)-getCurrentDBUsersBalance($mysqli,false);
    if ($fancy_secret) {
      $output = getKeysFromSecret($fancy_secret,true);
      $fancy_address = getAddressFromPublicKey($output['public']);
      $pool_lsk_reserve+=getCurrentBalance($fancy_address,$server,false);
    }
    AppendChartData(false,$pool_lsk_reserve,$cur_time,'reserve',$public_directory);
    //handle pool reserve
    if ($pool_lsk_reserve > 10) {
      $tmp = round(($pool_lsk_reserve-10)*100000000);
      $task = "UPDATE miners SET balance=balance+'$tmp' WHERE address='$fee';"; 
      $query = mysqli_query($mysqli,$task) or die("Database Error");
    }
    AppendChartData(false,$pool_productivity,$cur_time,'productivity',$public_directory);
    $end_time = time();
    $took = $end_time - $start_time;
    $time_sleep = 60-$took;
    if ($time_sleep < 1) {
      $time_sleep = 1;
    }
    clog("[".$df."] Statistics Update\nTook -> ".$took."s\nActive voters -> ".$voters_count."\nApproval -> ".$approval."\nVotepower -> ".$total_voters_power." \nBalance -> ".$balanceinlsk_p."\nRank -> ".$rank."\nBalance Reserve -> ".$pool_lsk_reserve."\nProductivity -> ".$pool_productivity,'stats');
    clog("Sleeping ".$time_sleep."s...",'stats');
    csleep($time_sleep);
  } else {
    //Can't get data, dont mess chart
    $end_time = time();
    $took = $end_time - $start_time;
    $time_sleep = 60-$took;
    if ($time_sleep < 1) {
      $time_sleep = 1;
    }
    csleep($time_sleep);
    clog("Can't get data...",'stats');
  }
}
?>