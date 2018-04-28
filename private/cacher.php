<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
require_once('priv_utils.php');
require_once('../lisk-php/main.php');
require_once('logging.php');
$config = include('../config.php');
$df = 0;
$delegate = $config['delegate_address'];

while(1) {
  $df++;
  $start_time = time();
  clog("Fetching data...",'cacher');
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
  $rvoters_array = null;
  clog("[".$df."]Getting initial voters list...",'cacher');
  $voters = GetVotersFor($publicKey,$server);
  $voters_count = $voters['data']['votes'];
  $voters_array = $voters['data']['voters'];
  clog("[".$df."]Count:".$voters_count,'cacher');
  $offset = 100;
  $mem=0;
  while ($offset <= $voters_count+100) {
    if ($offset > $voters_count) {
      $effective_offset = $voters_count;
    } else {
      $effective_offset = $offset;
    }
    clog("[".$df."]Getting voters at offset:".$effective_offset."/".$voters_count,'cacher');
    $voters = GetVotersFor($publicKey,$server,$effective_offset);
    $tmp = $voters['data']['voters'];
    $voters_array = array_merge($voters_array,$tmp);
    $offset+=100;
  }
  $voters_count = count($voters_array);
  clog("[".$df."]Voters Final:".$voters_count,'cacher');
  WriteCache('voters_list',json_encode($voters_array));
  $cached = ReadCache('voters_list');
  $rvoters_array = json_decode($cached,true);
  $rvoters_count = count($rvoters_array);
  clog("[".$df."]Read Count:".$rvoters_count,'cacher');
  $end_time = time();
  $took = $end_time - $start_time;
  $time_sleep = 180-$took;
  if ($time_sleep < 1) {
    $time_sleep = 1;
  }
  clog("[".$df."] Cacher Update Took -> ".$took."s",'cacher');
  clog("Sleeping ".$time_sleep."s...",'cacher');
  csleep($time_sleep);
}
?>