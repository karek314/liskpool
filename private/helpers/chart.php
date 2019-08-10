<?php
error_reporting(error_reporting() & ~E_NOTICE & ~E_WARNING);
$file = $argv[1];

$array = json_decode(file_get_contents($file));
$count = count($array);
echo "\nCount:".$count;
$fragmentation = 360;
$counter=0;
$NewArray = array();
for ($i=0; $i < $count; $i++) { 
	$object = $array[$i];
	if ($counter == 0) {
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