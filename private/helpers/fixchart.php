<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
$file = $argv[1];

$array = json_decode(file_get_contents($file));
$count = count($array);
echo "\nCount:".$count;
$NewArray = array();
for ($i=0; $i < $count; $i++) { 
	$object = $array[$i];
	$item = $object[1];
	if ($item > -50) {
		array_push($NewArray, $object);
	}
	$counter++;
	if ($counter >= $fragmentation) {
		$counter=0;
	}
}
echo "\nNew Count:".count($NewArray);
echo "\n";
file_put_contents($file, json_encode($NewArray));
?>