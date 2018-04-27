<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../../config.php');
$m = new Memcached();
$m->addServer('localhost', 11211);
$lisk_host = $m->get('lisk_host');
$lisk_port = $m->get('lisk_port');
$delegate = $config['delegate_address'];
$protocol = $config['protocol'];
$fee = $config['pool_fee_payout_address'];
$pool_fee = floatval(str_replace('%', '', $config['pool_fee']));
$pool_fee_payout_address = $config['pool_fee_payout_address'];
$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
$minedblocks = $m->get('minedblocks');

//get cached delegate acc
$delegate_account = $m->get('delegate_account');
$publicKey = $delegate_account['data'][0]['publicKey'];
$pool_balance = $delegate_account['data'][0]['balance'];
$username = $delegate_account['data'][0]['delegate']['username'];
$balanceinlsk_p = floatval($pool_balance/100000000);

//get cached d_data
$d_data = $m->get('d_data');
$rank = $d_data['data'][0]['rank'];
$approval = $d_data['data'][0]['approval'];
$productivity = $d_data['data'][0]['productivity'];
$missedblocks = $d_data['data'][0]['missedBlocks'];

//get cached voters
$voters_array = $m->get('d_voters');
$voters_count = count($voters_array);
$total_voters_power = 0;
foreach ($voters_array as $key => $value) {
  $balance = $value['balance'];
  $balanceinlsk = floatval($balance/100000000);
  $balance_summary += $balanceinlsk;
  $username = $value['username'];
  $address = $value['address'];
  $total_voters_power = $total_voters_power + $balance;
  if ($address != $fee) {
    if (!$username) {
      $new_array[] = '&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="https://explorer.lisk.io/address/'.$address.'">'.$address.'</a> balance: <b>'.number_format($balanceinlsk, 8).'</b> LSK';
    } else {
      $new_array[] = '&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="https://explorer.lisk.io/address/'.$address.'">'.$address.'</a> with name: <b>'.$username.'</b> balance: <b>'.$balanceinlsk.'</b> LSK';
    }
  }
}

$forged_voters = $m->get('forged_voters');
foreach ($forged_voters as $key => $value) {
    $balance = $value['balance'];
    $address = $value['address'];
    if ($address != $fee) {
      $balanceinlsk = floatval($balance/100000000);
      $activeminers = $activeminers.'<br>&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="/stats/miner/?address='.$address.'">'.$address.'</a> forged: <b>'.number_format($balanceinlsk, 8).'</b> LSK';
    }
}

echo '<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>Stats - LISK Delegate Pool</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="karek314">
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="Lisk.io"/>
    <meta property="og:description"        content="Lisk.io"/>
    <link rel="shortcut icon" href="../assets/images/favicon.ico"> 
    <meta name="keywords" content="">
    <link href="http://fonts.googleapis.com/css?family=Merriweather+Sans:700,300italic,400italic,700italic,300,400" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Russo+One" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
    <!-- Global CSS -->
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.min.css">   
    <!-- Plugins CSS -->    
    <link rel="stylesheet" href="../assets/plugins/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="../assets/plugins/elegant_font/css/style.css">
    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="../assets/css/styles-2.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
    .button-fill {
  text-align: center;
  background: #ccc;
  display: inline-block;
  position: relative;
  text-transform: uppercase;
  margin: 8px;
}
.button-fill.grey {
  background: #444B54;
  color: white;
}
.button-fill.orange .button-inside {
  color: #f26b43;
}
.button-fill.orange .button-inside.full {
  border: 1px solid #f26b43;
}
.button-text {
  padding: 0 25px;
  line-height: 56px;
  letter-spacing: .1em;
}
.button-inside {
  width: 0px;
  height: 54px;
  margin: 0;
  float: left;
  position: absolute;
  top: 1px;
  left: 50%;
  line-height: 54px;
  color: #445561;
  background: #fff;
  text-align: center;
  overflow: hidden;
  -webkit-transition: width 0.5s, left 0.5s, margin 0.5s;
  -moz-transition: width 0.5s, left 0.5s, margin 0.5s;
  -o-transition: width 0.5s, left 0.5s, margin 0.5s;
  transition: width 0.5s, left 0.5s, margin 0.5s;
}
.button-inside.full {
  width: 100%;
  left: 0%;
  top: 0;
  margin-right: -50px;
  border: 1px solid #445561;
}
.inside-text {
  text-align: center;
  position: absolute;
  right: 50%;
  letter-spacing: .1em;
  text-transform: uppercase;
  -webkit-transform: translateX(50%);
  -moz-transform: translateX(50%);
  -ms-transform: translateX(50%);
  transform: translateX(50%);
}
</style>
</head> 
<body class="blog-home-page">   
    <div class="header-wrapper header-wrapper-blog-home">
        <!-- ******HEADER****** --> 
        <header id="header" class="header navbar-fixed-top">  
            <div class="container">       
                <h1 class="logo">
                    <a href="../"><span class="highlight">Lisk</span>Pool</a>
                </h1><!--//logo-->
                <nav class="main-nav navbar-right" role="navigation">
                    <div class="navbar-header">
                        <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button><!--//nav-toggle-->
                    </div><!--//navbar-header-->
                    <div id="navbar-collapse" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li class="nav-item"><a href="..">Home</a></li>
                            <li class="active nav-item"><a href="../stats">Stats</a></li>
                            <li class="nav-item"><a href="../charts">Charts</a></li>
                            <li class="nav-item"><a href="../stats/miner/">Forger Stats</a></li>              
                            <li class="nav-item last"><a href="https://liskstats.net" target="_blank">Lisk Network Status</a></li>
                        </ul><!--//nav-->
                    </div><!--//navabr-collapse-->
                </nav><!--//main-nav-->
            </div><!--//container-->
        </header><!--//header-->   
        
    <!-- ******Contact Section****** --> 
    <section class="contact-section section">
        <div class="container">
        <h2 class="title text-center"><br>Statistics</h2>
            <p class="intro text-left"></p>
             <p class="intro text-left"><font color="F22613"></p></font>
            <form id="contact-form" class="contact-form form" method="post" action="push.php">                    
                <div class="row text-left">
                    <div class="contact-form-inner col-md-8 col-sm-12 col-xs-12 col-md-offset-2 col-sm-offset-0 xs-offset-0">';                                                                                   

    echo '<center>';
    echo '<a href="#"><div class="button-fill grey" style="width:94%"><div class="button-text">#'.$rank.'</b></div><div class="button-inside"><div class="inside-text"><font size="2">Rank</font></div></div></div></a>';
    
    echo '<a href="/charts"><div class="button-fill grey" style="width:94%"><div class="button-text">'.number_format((float)$approval, 3, '.', '').'%</b></div><div class="button-inside"><div class="inside-text">Approval</div></div></div></a>';

    echo '<a href="https://explorer.lisk.io/address/'.$delegate.'" target="_blanklank"><div class="button-fill grey" style="width:94%"><div class="button-text">'.$balanceinlsk_p.'</b></div><div class="button-inside"><div class="inside-text">Pool Balance in LISK</div></div></div></a>';
    
    echo '<a href="https://explorer.lisk.io/address/'.$delegate.'" target="_blanklank"><div class="button-fill grey" style="width:94%"><div class="button-text">'.$delegate.'</b></div><div class="button-inside"><div class="inside-text">Delegate address</div></div></div></a>';
    
    echo '<a href="#"><div class="button-fill grey" style="width:94%"><div class="button-text">'.$minedblocks.'</b></div><div class="button-inside"><div class="inside-text"><font size="2">Forged Blocks</font></div></div></div></a>';
    
    echo '<a href="#"><div class="button-fill grey" style="width:94%"><div class="button-text">'.$voters_count.'</b></div><div class="button-inside"><div class="inside-text"><font size="1.5">Active votes</font></div></div></div></a>';

    echo '<a href="#"><div class="button-fill grey" style="width:94%"><div class="button-text">'.$missedblocks.'</b></div><div class="button-inside"><div class="inside-text"><font size="2">Missed Blocks</font></div></div></div></a>';

    echo '<a href="#"><div class="button-fill grey" style="width:94%"><div class="button-text">'.$productivity.'</b></div><div class="button-inside"><div class="inside-text"><font size="2">Productivity</font></div></div></div></a>';

    echo '<a href="#"><div class="button-fill grey" style="width:94%"><div class="button-text">'.number_format((float)$balance_summary, 2, '.', ',').' LSK</b></div><div class="button-inside"><div class="inside-text"><font size="2">Total balance supporting us</font></div></div></div></a>';

    echo '</center>';

  echo '<b><br>&nbsp;&nbsp;&nbsp;&nbsp;Voters forging results (Limit 100):</b>';
  if ($activeminers == '') {
    echo ' Pool did not processed any blocks yet so nothing to display here!<br>';
  }
  echo $activeminers;
  echo '<b><br><br>&nbsp;&nbsp;&nbsp;&nbsp;Active Voters (Limit 100):</b>';
  $new_array = array_reverse($new_array);
  $xm=0;
  foreach ($new_array as $key => $value) {
    $xm++;
    if ($xm<101) {
      echo '<br>'.$value;
    }
  }
  echo '<br><br><b>Forged Blocks (last 50):</b><br>';
  $last_blocks = $m->get('last_blocks');
  foreach ($last_blocks as $key => $value) {
      echo '<a href="https://login.lisk.io/api/blocks?height='.$value.'" target="_blank">'.$value.'</a>, ';
      $newline_counter++;
      if ($newline_counter == 9) {
        echo '<br>';
        $newline_counter = 0;
      }
  }
  echo '        <br><br>
            <!--//row-->
                    </div>
                </div><!--//row-->
                <div id="form-messages"></div>
            </form><!--//contact-form-->
        </div><!--//container-->
    </section><!--//contact-section-->
    
            
   <!-- ******FOOTER****** --> 
    <footer class="footer">
        <div class="footer-content">
            <div class="container">
                <div class="row">
                    <div class="footer-col col-md-3 col-sm-4 links-col">
                        <div class="footer-col-inner">
                            <h3 class="sub-title">Quick Links</h3>
                            <ul class="list-unstyled">
                                <li><a href="..">Home</a></li>
                                <li><a href="../stats">Pool statistics</a></li>
                                <li><a href="../charts">Charts</a></li>
                                <li><a href="../stats/miner/">Forger statistics</a></li>                            
                                <li><a href="https://liskstats.net" target="_blank">Lisk Network Status</a></li>
                            </ul>
                        </div><!--//footer-col-inner-->
                    </div><!--//foooter-col-->
                     <div class="footer-col col-md-6 col-sm-8 blog-col">
                                <br>
                            </div><!--//foooter-col--> 
                    <div class="footer-col col-md-3 col-sm-12 contact-col">
                        <div class="footer-col-inner">
                            <h3 class="sub-title"></h3>
                            <p class="intro"></p>
                            <div class="row">
                                <p class="adr clearfix col-md-12 col-sm-4">
                                    <span class="adr-group">
                                    </span>
                                </p>
                            </div> 
                        </div><!--//footer-col-inner-->            
                    </div><!--//foooter-col-->   
                </div>   
            </div>        
        </div><!--//footer-content-->
    
 
    <!-- Main Javascript -->          
    <script  type="text/javascript" src="../assets/plugins/jquery-1.11.2.min.js"></script>
    <script  type="text/javascript" src="../assets/plugins/jquery-migrate-1.2.1.min.js"></script>
    <script  type="text/javascript" src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script> 
    <script  type="text/javascript" src="../assets/plugins/bootstrap-hover-dropdown.min.js"></script>       
    <script  type="text/javascript" src="../assets/plugins/back-to-top.js"></script>             
    <script  type="text/javascript" src="../assets/plugins/jquery-placeholder/jquery.placeholder.js"></script>                                                                  
    <script  type="text/javascript" src="../assets/plugins/jquery-match-height/jquery.matchHeight-min.js"></script>     
    <script  type="text/javascript" src="../assets/plugins/FitVids/jquery.fitvids.js"></script>
    <script  type="text/javascript" src="../assets/js/main.js"></script>     
    
    <!-- Form Validation -->
    <script  type="text/javascript" src="../assets/plugins/jquery.validate.min.js"></script> 
    <script  type="text/javascript" src="../assets/js/form-validation-custom.js"></script> 
    
    <!-- Form iOS fix -->
    <script  type="text/javascript" src="../assets/plugins/isMobile/isMobile.min.js"></script>
    <script  type="text/javascript" src="../assets/js/form-mobile-fix.js"></script>  
    <script>
        $(".button-fill").hover(function () {
        $(this).children(".button-inside").addClass("full");
        }, function() {
        $(this).children(".button-inside").removeClass("full");
        });
    </script>

</body>
</html>';


?>
