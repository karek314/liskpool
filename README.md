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
apt-get install nginx mariadb-server memcached php-memcached php php-curl
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
'password' => 'dbpass',  //<- Database Password
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
'secondSecret' => 'passphrase2', //<- Second passphrase, if you dont have one leave it empty ex. ""
'public_directory' => 'private', //<- directory name of public dir
'cap_balance' => '150000000000000' //balance to cap voter votepower, default - anything over 1.5m LSK will be reduced to 1.5m
```

# Usage
Start LISK node as usual, and set up it to forging. But please note that you can forge with different node that one used for hosting pool.

Navigate to <b>/private/</b> directory and start background scripts:<br>
<br><b>Node height checker</b>, necessary even there is only one defined
<pre>screen -dmS bestnode php bestnode.php</pre>
<br><b>Block Processing</b> - this script checks if delegate has forged new block, if yes it will split as defined in config
<pre>screen -dmS processing php processing.php</pre>
<br><b>Updating charts</b> - this script updates data to keep charts up to date.
<pre>screen -dmS stats php stats.php</pre>
<br><b>Withdraw script</b> - this script withdraws revenue as defined in config.
<pre>screen -dmS withdraw php withdraw.php</pre>
<br>If you want to support Liskstats contributors and Liskstats itself use also script below. This script connects to Liskstats.net and retrieve all current contributors. Every contributor is added to split with "fake" votepower which is defined in <b>processing.php</b>.
<pre>screen -dmS liskstats php liskstats.php</pre>
<br>

<b>Optional Balance checker</b> - Simple script to compare total LISK value stored in database in reference to actual LISK stored on delegate account.
<pre>cd helpers
php check.php</pre>

<br>
All background scripts can be easily accessed with
<pre>
NAME = "processing" or "stats" or "withdraw" or "bestnode" or "liskstats"
screen -x NAME
</pre>
Example
<pre>
screen -x processing
</pre>

## Forging productivity
Optionally you can use [lisk-best-forger](https://github.com/karek314/lisk-best-forger) background script to improve forging productivity.
<pre>
git submodule update --init --recursive
cd private/forging
nano config.php
</pre>
In private/config.php you need to add trusted nodes and it's ports. Each specified server needs to have whitelisted IP address of server which will be used to run this script. As described [here](https://docs.lisk.io/docs).
Passphrase will be taken from main configuration file. For more details visit main [lisk-best-forger](https://github.com/karek314/lisk-best-forger) repository.

#### Usage
<pre>
screen -dmS bestforger php daemon.php
</pre>
This script should be used along with trusted servers only via SSL.

# Public API
<b>Specified voter balance data for balance chart, respectively entitled pool balance, network balance and withdraw amount.</b>
<pre>
data/voters/ADDRESS.json
data/voters/balance/ADDRESS.json
data/voters/withdraw/ADDRESS.json
</pre>
<b>General data for charts</b>
<pre>
data/approval.json
data/balance.json
data/rank.json
data/voters.json
data/reserve.json
data/productivity.json
</pre>
<b>General pool info</b>
<pre>
api/info/
</pre>
<b>Current forged balance for each voter / contributor</b>
<pre>
api/info/forged/
</pre>

# Migration from older version of pool
In past all chart data was stored in database tables, however with millions of rows and cheap vps it could have been possible bottleneck with thousands of voters. If you are pool operator and you want to keep all statistics history.
1. Stop all background scripts
2. Navigate to <b>/helpers/</b> directory in <b>/private/</b>
3. Execute ```php db2files.php all``` or ```screen -dmS dump php db2files.php all```
4. Wait until it finish, it can take hours for huge database. If your connection might drop, possibly execute this as background job choosing second command.
5. Start updated background scripts.
6. Tables <b>pool_xxx</b> and <b>miner_balance</b> can be deleted.

# Contributing
If you want to contribute, fork and pull request or open issue.

# License
Everything is under MIT License (MIT) except [Front-end (site theme) which is paid.](http://themes.3rdwavemedia.com/website-templates/responsive-bootstrap-theme-web-development-agencies-devstudio/)<br>
I do own license, so better buy license or use your own front-end. In future it will be rewritten from scratch.
