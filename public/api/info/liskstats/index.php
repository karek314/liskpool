<?php
error_reporting(error_reporting() & ~E_NOTICE);
$m = new Memcached();
$m->addServer('localhost', 11211);
$data = $m->get('liskstats');
$response = array('data' => $data,'success' => true);
if ($_GET['type'] == 'html') {
	output_html($data);
} else if ($_GET['type'] == 'json') {
	header('Content-Type: application/json');
	die(json_encode($response));
} else {
	output_html($data);
}

function output_html($json){
	echo "<center>";
	$latest = $json['latest_lisk_core_version'];
	$info = $json['info'];
	$nodes_count = $json['nodes_count'];
	$nodes = $json['nodes'];
	echo "<br>Latest lisk core version: ".$latest;
	echo "<br>".$info;
	echo "<br>Updated once per 60s";
	echo "<br><br>All nodes: ".$nodes_count['all'];
	echo "<br>Good nodes: ".$nodes_count['good'];
	echo "<br>Bad nodes: ".$nodes_count['bad'];
	echo '<br><br><font size="5">Good nodes</font><br><table style="width:70%"><tr><th>ID</th><th>Version</th><th>Address</th><th>Os</th><th>Height</th><th>Info</th><th>Contact</th></tr>';
	foreach ($nodes['good'] as $key => $node) {
		echo '<tr><th>'.$node['object'].'</th><th>'.$node['version'].'</th><th>'.str_replace('f', '', str_replace(':', '', $node['ip'])).':'.$node['port'].'</th><th>'.$node['os'].'</th><th>'.$node['height'].'</th><th>'.$node['info'].'</th><th>'.$node['contact'].'</th>';
	}
	echo "</table>";
	echo '<br><br><br><br><font size="5" color="red">Bad nodes</font><br><font size="2" color="blue">Node stucked?<br><b>bash lisk.sh rebuild -u https://snapshot.thepool.io</b></font><br><br><table style="width:70%"><tr><th>ID</th><th>Version</th><th>Address</th><th>Os</th><th>Height</th><th>Info</th><th>Contact</th></tr>';
	foreach ($nodes['bad'] as $key => $node) {
		echo '<tr><th>'.$node['object'].'</th><th>'.$node['version'].'</th><th>'.str_replace('f', '', str_replace(':', '', $node['ip'])).':'.$node['port'].'</th><th>'.$node['os'].'</th><th>'.$node['height'].'</th><th>'.$node['info']['details'].'</th><th>'.$node['contact'].'</th>';
	}
	echo "</table></center>";
	die();
}


?>
