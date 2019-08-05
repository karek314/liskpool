<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../../../config.php');
require_once('../../../lisk-php/main.php');
$lsk = new Math_BigInteger(LSK_BASE);
$delegate = $config['delegate_address'];
$protocol = $config['protocol'];
$m = new Memcached();
$m->addServer('localhost', 11211);
$lisk_host = $m->get('lisk_host');
$lisk_port = $m->get('lisk_port');

//Read voters count
$voters_count = $m->get('voters_count');
//Read internal voters from memory
$internal_voters = $m->get('internal_voters_balance');
//Read external voters balance from memory
$external_voters = $m->get('external_voters_balance');
$voters_list = array('internal' => $internal_voters, 'external' => $external_voters, 'total_count' => $voters_count);
//Read last 50 blocks from memory
$last_blocks = $m->get('last_blocks');
//Account info
$delegate_account = $m->get('delegate_account');
$publicKey = $delegate_account['data'][0]['publicKey'];
$pool_balance = $delegate_account['data'][0]['balance'];
$username = $delegate_account['data'][0]['delegate']['username'];

$BBalance = new Math_BigInteger($pool_balance);
list($Bbalance_quotient, $Bbalance_reminder) = $BBalance->divide($lsk);
$BalancevalueAsFloat = floatval($Bbalance_quotient->toString().".".$Bbalance_reminder->toString());

//Delegate specfic info
$d_data = $m->get('d_data');
$rank = $d_data['data'][0]['rank'];
$approval = $d_data['data'][0]['approval'];
$productivity = $d_data['data'][0]['productivity'];
$missedblocks = $d_data['data'][0]['missedBlocks'];
$forged_blocks = $d_data['data'][0]['producedBlocks'];
$supportingBalance = $d_data['data'][0]['vote'];
$BsupportingBalance = new Math_BigInteger($supportingBalance);
list($Tbalance_quotient, $Tbalance_reminder) = $BsupportingBalance->divide($lsk);
$SupportingvalueAsFloat = floatval($Tbalance_quotient->toString().".".$Tbalance_reminder->toString());
$supportingBalance = array('lsk' => $SupportingvalueAsFloat, 'raw' => $supportingBalance);
$pool_balance_array = array('lsk' => $BalancevalueAsFloat, 'raw' => $pool_balance);
$blocks = array('forged' => $forged_blocks, 'missed' => $missedblocks, 'last50' => $last_blocks);

$response = array('pool_fee' => $config['pool_fee'],
				  'delegate_address' => $delegate,
				  'payout_threshold' => $config['payout_threshold'],
				  'withdraw_interval_in_sec' => $config['withdraw_interval_in_sec'],
				  'public_key' => $publicKey,
				  'pool_balance' => $pool_balance_array,
				  'rank' => $rank,
				  'approval' => $approval,
				  'productivity' => $productivity,
				  'supporting_balance' => $supportingBalance,
				  'blocks' => $blocks,
				  'username' => $username,
				  'voters' => $voters_list,
				  'success' => true);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
die(json_encode($response));
?>
