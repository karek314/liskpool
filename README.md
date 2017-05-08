# Lisk Pool
This is first and fully open-sourced Lisk delegate forging pool (also known as delegate reward sharing). Written in PHP.

# Requirements
<a href="https://mariadb.org" target="_blank">MariaDB server</a><br>
<a href="https://memcached.org" target="_blank">Memcached</a><br>
<a href="http://nginx.org" target="_blank">Nginx</a><br>
<a href="https://lisk.io/documentation" target="_blank">Lisk Node</a><br>
<a href="http://www.highcharts.com" target="_blank">Highcharts (included in project)</a><br>

## Important
Only <b>public</b> directory must be served with webserver. While <b>config.php</b> and <b>private</b> cannot be served.
 
# Installation
<pre>
apt-get install nginx mariadb-server memcached
</pre>
If you are using PHP5
<pre>
apt-get install php5-memcached
</pre>
If you are using PHP7
<pre>
apt-get install php7-memcached
</pre>
Setup your mysql server, nginx and import database scheme <pre>lisk_pool_scheme_db.sql</pre>

Navigate to config.php

<b>lisk_nodes & lisk_ports</b>
You can add here more independent nodes, first one should be localhost, withdraws will be processed only from first node specified here for security reasons as passphrase are being sent out currently to specified node. Other nodes are used to determine node which is currently at latest height to keep pool updated with most recent state of network.

```php
$lisk_nodes = array(0 => 'localhost',1 => '123.123.123.123');
$lisk_ports = array(0 => '8000',1 => '8000');

'host' => 'localhost',    //<- dont change if mariadb is running on the same machine
'username' => 'root',     //<- Database user
'password' => 'SQL_PASSWORD',  //<- Database Password
'bdd' => 'lisk',    //<- Database Name
'lisk_host' => $lisk_nodes,
'lisk_port' => $lisk_ports,
'protocol' => 'http', //<-pick http or https
'pool_fee' => '25.0%',     //<- adjustable pool fee as float for ex. "25.0%"
'pool_fee_payout_address' => '17957303129556813956L',   //<- Payout address if fee > 0.0
'delegate_address' => '17957303129556813956L',    //<- Delegate address - must be valid forging delegate address
'payout_threshold' => '1',    //<- Payout threshold in LISK
'fixed_withdraw_fee' => '0.1',    //<- Fixed Withdraw fee in LISK
'withdraw_interval_in_sec' => '43200',   //<- Withdraw script interval represented in seconds
'secret' => 'passphrase1',    //<- Main passphrase the same your as in your forging delegate
'secondSecret' => 'passphrase2' //<- Second passphrase, if you dont have one leave it empty ex. ""
```

# Usage
Start LISK node as usual, and set up it to forging. But please note that you can forge with different node that one used for hosting pool.

Navigate to <pre>/private/</pre> directory and start background scripts:<br>
<br>Node height checker, necessary even there is only one defined
<pre>screen -dmS bestnode php bestnode.php</pre>
<br>Block Processing - this script checks if delegate has forged new block, if yes it will be split as defined in config
<pre>screen -dmS processing php processing.php</pre>
<br>Updating charts - this script updates data to keep charts up to date.
<pre>screen -dmS stats php stats.php</pre>
<br>Withdraw script - this script withdraws revenue as defined in config.
<pre>screen -dmS withdraw php withdraw.php</pre>
<br>
Optional
Balance checker - Simple script to compare total LISK value stored in database in reference to actual LISK stored on delegate account.
<pre>php check.php</pre>

<br>
All background scripts can be easily accessed with
<pre>
screen -x processing/stats/withdraw/bestnode
</pre>

## Forging productivity
Optionally you can use [lisk-best-forger](https://github.com/karek314/lisk-best-forger) background script to improve forging productivity.
<pre>
git submodule update --init --recursive
cd private/forging
nano config.php
</pre>
In private/config.php you need to add trusted nodes and it's ports. Each specified server needs to have whitelisted IP address of server which will be used to run this script. As described [here](https://lisk.io/documentation?i=lisk-docs/BinaryInstall).
Passphrase will be taken from main configuration file. For more details visit main [lisk-best-forger](https://github.com/karek314/lisk-best-forger) repository.

#### Usage
<pre>
screen -dmS bestforger php daemon.php
</pre>
This script should be used along with trusted servers only via SSL.

# Contributing
If you want to contribute, fork and pull request or open issue.

# License
Entire PHP is under The MIT License (MIT)<br>
Front-end(site theme) is used from http://themes.3rdwavemedia.com/website-templates/responsive-bootstrap-theme-web-development-agencies-devstudio/<br>
Personally i own license, so better buy license or use your own front-end.
