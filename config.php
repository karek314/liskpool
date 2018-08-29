<?php
$lisk_nodes = array('localhost');
$lisk_ports = array('8000');
return array(
    'host' => 'localhost',
    'username' => 'root',
	'password' => 'liskdbpool',
	'bdd' => 'lisk',
	'lisk_host' => $lisk_nodes,
	'lisk_port' => $lisk_ports,
	'protocol' => 'http',
	'pool_fee' => '25.0%',
	'pool_fee_payout_address' => '',
	'delegate_address' => '',
	'payout_threshold' => '0.2',
	'withdraw_interval_in_sec' => '604800',
	'secret' => 'passphrase1',
	'secondSecret' => '',
	'fancy_withdraw_hub' => '',
	'public_directory' => 'public',
	'cap_balance' => '150000000000000',
	'support_standby_delegates' => '5',	
	'support_standby_delegates_amount' => '5000000000000',
	'slow_withdraw' => true,
	'withdraw_message' => 'Thank you for supporting THIS delegate!'
);
?>
