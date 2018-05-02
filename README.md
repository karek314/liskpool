# Lisk Pool
This is first and fully open source Lisk delegate forging pool (also known as delegate reward sharing). Written in PHP.

### Tokens and Dapps
In further updates LiskPool will allow to prepare or pregenerate genesis blocks to custom dapps allowing to distribute own tokens to voters, exact details yet to be announced by LiskHQ.

# Requirements
<a href="https://mariadb.org" target="_blank">MariaDB server</a><br>
<a href="https://memcached.org" target="_blank">Memcached</a><br>
<a href="http://nginx.org" target="_blank">Nginx</a><br>
<a href="https://lisk.io/" target="_blank">Lisk Node</a><br>

## Important
Only <b>public</b> directory must be served with webserver. While <b>config.php</b> and <b>private</b> should not be served.
 
# Installation
Liskpool now fully relies on [Lisk-PHP](https://github.com/karek314/lisk-php) to interact with Lisk node, including transaction signing.
```sh
cd liskpool
git submodule update --init --recursive
cd lisk-php
bash setup.sh
cd ..
apt-get install nginx mariadb-server memcached php-memcached php php-curl
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

## Forging
Liskpool works great along with [Lisk-forging-failover](https://github.com/karek314/Lisk-forging-failover), it's compatible with shared library.
Just pull it in main directory. Then follow instruction from [Lisk-forging-failover](https://github.com/karek314/Lisk-forging-failover) repository.
```sh
git clone https://github.com/karek314/Lisk-forging-failover
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
<b>Liskstats.net good and bad nodes for processing.php</b>
<pre>
api/info/liskstats/?type=html
api/info/liskstats/?type=json
</pre>

# Logs
As soon any of background scripts gets excuted, <b>logs</b> directory will appear in <b>private</b> directory. It will store all logs of all background scripts.

# Migration from Lisk Core 0.9.X branch to 1.0.0 / Master
Soon.


# Migration from older version of pool
In past all chart data was stored in database tables, however with millions of rows and cheap vps it could have been possible bottleneck with thousands of voters and more. If you are pool operator and you want to keep all statistics history.
1. Stop all background scripts
2. Navigate to <b>/helpers/</b> directory in <b>/private/</b>
3. Execute ```php db2files.php all``` or ```screen -dmS dump php db2files.php all```
4. Wait until it finish, it can take hours for huge database. If your connection might drop, possibly execute this as background job choosing second command.
5. Start updated background scripts.
6. Tables <b>pool_xxx</b> and <b>miner_balance</b> can be deleted.

# Contributing
If you want to contribute, fork and pull request or open issue.

# License
Liskpool - MIT License,<br>
<b>Opensource libraries used</b><br>
HTML5 Boilerplate v5.0 | MIT License<br>
Plotly.js | MIT License<br>
Lisk-PHP | MIT License<br>
Modernizr 2.8.3 (Custom Build) | MIT License<br>
Respond.js v1.4.2 | MIT License<br>
jquery | MIT License<br>
normalize.css v3.0.2 | MIT License<br>