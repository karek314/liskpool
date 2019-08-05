# Lisk Pool
This is first and fully open source Lisk delegate forging pool (also known as delegate reward sharing). Written in PHP.

### Tokens and blockchain apps
In further updates LiskPool will be adapated to work with custom apps and issuance of tokens.

# Requirements
<a href="https://mariadb.org" target="_blank">MariaDB server</a><br>
<a href="https://memcached.org" target="_blank">Memcached</a><br>
<a href="http://nginx.org" target="_blank">Nginx</a><br>
<a href="https://lisk.io/" target="_blank">Lisk Node</a><br>

## Important
Only <b>public</b> directory must be served with web server. While <b>config.php</b> and <b>private</b> should not be served. This repository contains only background core code and <b>public</b> api endpoints. Web interface is available in separate repository: https://github.com/thepool-io/liskpool-html Development of open source HTML interface version will not be continued.

# Installation
Liskpool now fully relies on [Lisk-PHP](https://github.com/thepool-io/lisk-php) to interact with Lisk node, including transaction signing.
```sh
cd liskpool
git submodule update --init --recursive
cd lisk-php
bash setup.sh
cd ..
apt-get install nginx mariadb-server memcached php-memcached php php-curl
```

Optionally, basic html interface
```sh
cd liskpool
cd public
git clone https://github.com/thepool-io/liskpool-html .
```

Setup mariadb server, nginx and import database scheme <pre>lisk_pool_scheme_db.sql</pre>

Navigate to config.php

<b>lisk_nodes & lisk_ports</b>
You can add here more independent nodes which are used to determine node with best height. It keeps pool updated with most recent state of network, prevents messing up charts in case of forks and other issues.

```php

<?php
$lisk_nodes = array('localhost','123.123.123.123');
$lisk_ports = array('5000','5000');
return array(
    'host' => 'localhost',
	'username' => 'root',     //<- Database user
	'password' => 'dbpass',  //<- Database Password
	'bdd' => 'lisk', //<- Database name
	'lisk_host' => $lisk_nodes,
	'lisk_port' => $lisk_ports,
	'protocol' => 'http',
	'pool_fee' => '25.0%', //<- adjustable pool fee as float for ex. "25.0%"
	'pool_fee_payout_address' => '', //<- Pool revenue address
	'delegate_address' => '', //<- Delegate address - must be valid forging delegate address
	'payout_threshold' => '0.2', //<- Payout threshold in LSK
	'withdraw_interval_in_sec' => '604800', //<- Withdraw script interval represented in seconds
	'secret' => 'passphrase1', //<- Main passphrase the same your as in your forging delegate
	'secondSecret' => '', //<- Second passphrase, leave empty if only one enabled
	'fancy_withdraw_hub' => '', //<- Put here passphrase to withdraw from different account
	'public_directory' => 'public', //<- directory name of public dir served with webserver
	'cap_balance' => '150000000000000', //<- balance to cap voter votepower, default - anything over 1.5m LSK will be reduced to 1.5m
	'support_standby_delegates' => '5',	//<- automatically donate standby delegates
	'support_standby_delegates_amount' => '5000000000000',
	'slow_withdraw' => true //<- With payouts >1k lisk tx pool limit problem, it withdraws slower when true
);
?>
```
<b>pool_fee_payout_address</b> address specified for pool fee should be voting for delegate or should be manually added to table "miners".

# Usage

(Managing and starting liskpool will be moved under one shell script)

Navigate to <b>/private/</b> directory and start background scripts:<br>
<br><b>Node height checker</b>, necessary even there is only one defined
<pre>screen -dmS bestnode php bestnode.php</pre>
<br><b>Updating cache</b> - this script updates and cache data
<pre>screen -dmS cacher php cacher.php</pre>
<br><b>Block Processing</b> - this script checks if delegate forged new block, if so it will split as defined in config
<pre>screen -dmS processing php processing.php</pre>
<br><b>Updating charts</b> - this script updates data to keep charts up to date.
<pre>screen -dmS stats php stats.php</pre>
<br><b>Withdraw script</b> - this script withdraws revenue as defined in config. It features multithreaded withdraw processing if your cpu has multiple cores or supports htt. Technically, it's more like forking rather threading, however it was simplier to implement and saves some time building php with zts enabled.
<pre>screen -dmS withdraw php withdraw.php</pre>
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

## Forging
Liskpool works great along with [Lisk-forging-failover](https://github.com/thepool-io/Lisk-forging-failover), it's compatible with shared library.
Just pull it in main directory. Then follow instruction from [Lisk-forging-failover](https://github.com/thepool-io/Lisk-forging-failover) repository.
```sh
git clone https://github.com/thepool-io/Lisk-forging-failover
cd Lisk-forging-failover
bash setup.sh
```

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
api/info/forged/?by=ADDRESS
</pre>

# Logs
As soon any of background scripts gets excuted, <b>logs</b> directory will appear in <b>private</b> directory. It will store all logs of all background scripts.

# Contributing
If you want to contribute, fork and pull request or open issue.

# License
Liskpool - MIT License,<br>
<b>Opensource libraries used</b><br>
Lisk-PHP | MIT License<br>
