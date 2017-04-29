<?php
error_reporting(error_reporting() & ~E_NOTICE);
$data = $_GET['data'];
$type = $_GET['range'];
$miner = $_GET['dtx'];
$worker = $_GET['wrk'];
$lol = $_GET['rr'];

function mysql_fix_escape_string($text){
    if(is_array($text)) 
        return array_map(__METHOD__, $text); 
    if(!empty($text) && is_string($text)) { 
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), 
                           array('', '', '', '', "", '', ''),$text); 
    } 
    $text = str_replace("'","",$text);
    $text = str_replace('"',"",$text);
    return $text;
}

$data = mysql_fix_escape_string($data);
$type = mysql_fix_escape_string($type);
$miner = mysql_fix_escape_string($miner);
$worker = mysql_fix_escape_string($worker);
$lol = mysql_fix_escape_string($lol);

if ($data == '_miner_balance' && $miner && $lol) {
	$config = include('../../config.php');
	$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
	$existQuery = "SELECT value,var_timestamp FROM miner_balance WHERE miner='$miner' ORDER BY id ASC";
	$existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");
	$count = mysqli_num_rows($existResult);
	$x++;
	$miner_payouts = array();
	echo '[';
	while ($row=mysqli_fetch_row($existResult)){
		$stamp = $row[1]*1000;
		$real = $row[0];
		$x++;
	    echo '['.$stamp.','.$real.']';
    	if ($x-1 != $count) {
    		echo ',';
    	}
	}
	echo ']';
}

?>
