<?php
error_reporting(error_reporting() & ~E_NOTICE);
$height = $_GET['id'];
if (!is_numeric($height)) {
	die('No id provided');
}
$ch = curl_init("https://explorer.lisk.io/api/search?id=".$height);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$json = json_decode(curl_exec($ch),true);
$blockid = $json['result']['id']; 
curl_close($ch);
echo $blockid;
header("Location: https://explorer.lisk.io/block/".$blockid);
die();
?>
