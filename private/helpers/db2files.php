<?php
error_reporting(E_ALL);
$config = include('../../config.php');
$public_directory = $config['public_directory'];
$parm = strtolower($argv[1]);
$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die(mysqli_error($mysqli));
echo "\nPeforming db dump to public files\n";


if ($parm == 'all') {
	DumpGeneral($mysqli,$public_directory);
	DumpVoters($mysqli,$public_directory);
} else if ($parm == 'voters') {
	DumpVoters($mysqli,$public_directory);
} else if ($parm == 'general') {
	DumpGeneral($mysqli,$public_directory);
} else {
	die("\nRun with parm 'all', 'voters' or 'general' to perform db dump to public files\n");
}
echo "\nDone.\n";


function DumpVoters($mysqli_handle,$public_directory){
	echo "\nDumping voters";
	$voters_task = "SELECT address FROM miners";
    $task_result = mysqli_query($mysqli_handle,$voters_task)or die(mysqli_error($mysql_handle));
    while ($row=mysqli_fetch_row($task_result)){
      $voter_address = $row[0];
      DumpTable('voters',$voter_address,'miner_balance',$public_directory,$mysqli_handle);
    } 
}


function DumpGeneral($mysqli_handle,$public_directory){
	echo "\nDumping general";
	DumpTable(false,'approval','pool_votepower',$public_directory,$mysqli_handle);
	DumpTable(false,'rank','pool_rank',$public_directory,$mysqli_handle);
	DumpTable(false,'balance','pool_balance',$public_directory,$mysqli_handle);
	DumpTable(false,'voters','pool_voters',$public_directory,$mysqli_handle);
}


function DumpTable($subdir,$name,$table,$public_directory,$mysql_handle){
 	if (!$subdir) {
    	$real_path = realpath('../../'.$public_directory.'/data').'/'.$name.'.json';
    	if (strpos($table, 'votepower') !== false) {
    		$task = "SELECT votepower,val_timestamp FROM ".$table." ORDER BY id ASC";
    	} else {
    		$task = "SELECT value,var_timestamp FROM ".$table." ORDER BY id ASC";
    	}
  	} else {
   		$real_path = realpath('../../'.$public_directory.'/data/'.$subdir).'/'.$name.'.json';
   		$task = "SELECT value,var_timestamp FROM ".$table." WHERE miner='$name' ORDER BY id ASC";
  	}
  	echo "\nDumping[".$table."] -> ".$real_path;
  	$fh = fopen($real_path, 'wa+');
    $result = mysqli_query($mysql_handle,$task)or die(mysqli_error($mysql_handle));
    $count = 0;
    $count = mysqli_num_rows($result);
    $x=0;
    $x++;
    $json_output = '[';
    while ($row=mysqli_fetch_row($result)){
      $stamp = $row[1]*1000;
      $real = $row[0];
      $x++;
      $json_output .= '['.$stamp.','.$real.']';
      if ($x-1 != $count) {
        $json_output .= ',';
      }
    }
    $json_output .= ']';
  	fwrite($fh, $json_output);
  	fclose($fh); 
  	chmod($real_path, 0664);
}


?>