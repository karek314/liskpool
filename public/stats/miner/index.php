<?php
error_reporting(error_reporting() & ~E_NOTICE);
$config = include('../../../config.php');
require_once('../../../utils.php');
             
$miner = $_GET['address'];
if (!$miner) {
	die('<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>Forger Stats - LISK Delegate Pool</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="karek314">
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content="Lisk.io"/>
    <meta property="og:description"        content="Lisk.io"/>
    <link rel="shortcut icon" href="../favicon.ico">  
    <meta name="keywords" content="">
    <link href="http://fonts.googleapis.com/css?family=Merriweather+Sans:700,300italic,400italic,700italic,300,400" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Russo+One" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
    <!-- Global CSS -->
    <link rel="stylesheet" href="/assets/plugins/bootstrap/css/bootstrap.min.css">   
    <!-- Plugins CSS -->    
    <link rel="stylesheet" href="/assets/plugins/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/assets/plugins/elegant_font/css/style.css">
    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="/assets/css/styles-2.css">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
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
                            <li class="nav-item"><a href="../../">Home</a></li>
                            <li class="nav-item"><a href="../../stats">Stats</a></li>
                            <li class="nav-item"><a href="../../charts">Charts</a></li>
                            <li class="active av-item"><a href="/stats/miner/">Forger Stats</a></li>              
                            <li class="nav-item last"><a href="mailto:mail@mail.com">Support</a></li>
                        </ul><!--//nav-->
                    </div><!--//navabr-collapse-->
                </nav><!--//main-nav-->
            </div><!--//container-->
        </header><!--//header-->   
        
    
    <!-- ******Contact Section****** --> 
    <section class="contact-section section">
        <div class="container">
            <h2 class="title text-center"><br>Forger Statistics</h2>
            <p class="intro text-left"></p>
             <p class="intro text-left"><font color="F22613"></p></font>
            <form id="contact-form" class="contact-form form" method="get" action="index.php">                    
                <div class="row text-left">
                    <div class="contact-form-inner col-md-8 col-sm-12 col-xs-12 col-md-offset-2 col-sm-offset-0 xs-offset-0">
                        <div class="row">   <br><br>                                                                                    
                            <input type="text" class="form-control" id="address" name="address" placeholder="Put here your LISK address" minlength="15" required>
                        </div><br><br><br><br><br>
                        <center>    <button type="submit" class="btn btn-block btn-cta btn-cta-primary" style="width:30%; height:120%;">Go</button>
                        <br><br><br><br></center>
 
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
                                <li><a href="../../">Home</a></li>
                                <li><a href="../../stats">Pool statistics</a></li>
                                <li><a href="../../charts">Charts</a></li>
                                <li><a href="/stats/miner/">Forger statistics</a></li>                         
                                <li><a href="mailto:mail@mail.com">Support</a></li>
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
    <script  type="text/javascript" src="/assets/plugins/jquery-1.11.2.min.js"></script>
    <script  type="text/javascript" src="/assets/plugins/jquery-migrate-1.2.1.min.js"></script>
    <script  type="text/javascript" src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script> 
    <script  type="text/javascript" src="/assets/plugins/bootstrap-hover-dropdown.min.js"></script>       
    <script  type="text/javascript" src="/assets/plugins/back-to-top.js"></script>             
    <script  type="text/javascript" src="/assets/plugins/jquery-placeholder/jquery.placeholder.js"></script>                                                                  
    <script  type="text/javascript" src="/assets/plugins/jquery-match-height/jquery.matchHeight-min.js"></script>     
    <script  type="text/javascript" src="/assets/plugins/FitVids/jquery.fitvids.js"></script>
    <script  type="text/javascript" src="/assets/js/main.js"></script>     
    <script  type="text/javascript" src="/assets/plugins/jquery.validate.min.js"></script> 
    <script  type="text/javascript" src="/assets/js/form-validation-custom.js"></script> 
    <script  type="text/javascript" src="/assets/plugins/isMobile/isMobile.min.js"></script>
    <script  type="text/javascript" src="/assets/js/form-mobile-fix.js"></script>     
</body>
</html>');
}

echo '<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>Statistics</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="eth">
    <meta property="og:type"               content="website" />
    <meta property="og:title"              content=""/>
    <meta property="og:description"        content=""/>
    <link rel="shortcut icon" href="../favicon.ico">  
    <meta name="keywords" content="">
    <link href="http://fonts.googleapis.com/css?family=Merriweather+Sans:700,300italic,400italic,700italic,300,400" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Russo+One" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
    <!-- Global CSS -->
    <link rel="stylesheet" href="/assets/plugins/bootstrap/css/bootstrap.min.css">   
    <!-- Plugins CSS -->    
    <link rel="stylesheet" href="/assets/plugins/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/assets/plugins/elegant_font/css/style.css">
    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="/assets/css/styles-2.css">
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
                            <li class="nav-item"><a href="../../">Home</a></li>
                            <li class="nav-item"><a href="../../stats">Stats</a></li>
                            <li class="nav-item"><a href="../../charts">Charts</a></li>
                            <li class="active nav-item"><a href="stats/miner/">Miner Stats</a></li>              
                            <li class="nav-item last"><a href="mailto:mail@mail.com">Support</a></li>
                        </ul><!--//nav-->
                    </div><!--//navabr-collapse-->
                </nav><!--//main-nav-->
            </div><!--//container-->
        </header><!--//header-->   
        
    
    <!-- ******Contact Section****** --> 
    <section class="contact-section section">
        <div class="container">
            <h2 class="title text-center"><br>Miner Statistics</h2>
            <p class="intro text-left"></p>
             <p class="intro text-left"><font color="F22613"></p></font>
            <form id="contact-form" class="contact-form form" method="post" action="push.php">                    
                <div class="row text-left">
                    <div class="contact-form-inner col-md-8 col-sm-12 col-xs-12 col-md-offset-2 col-sm-offset-0 xs-offset-0">
                        <div class="row">
                        ';                                                                                   

$miner = mysql_fix_escape_string($miner);
$mysqli=mysqli_connect($config['host'], $config['username'], $config['password'], $config['bdd']) or die("Database Error");
$queryy = "SELECT balance FROM miners WHERE address = '$miner'";
$result = mysqli_query($mysqli,$queryy)or die("Database Error");
$row = mysqli_fetch_array($result);
$balance = $row[0];
$balanceinlsk = floatval($balance/100000000);

echo '<center>';
echo '<a href="https://explorer.lisk.io/address/'.$miner.'" target="_blank"><div class="button-fill grey" style="width:94%"><div class="button-text">'.$miner.'</b></div><div class="button-inside"><div class="inside-text"><font size="1.5">https://explorer.lisk.io/address/'.$miner.'</font></div></div></div></a>';
echo '<a href="#"><div class="button-fill grey" style="width:94%"><div class="button-text">'.$balanceinlsk.'</b></div><div class="button-inside"><div class="inside-text">Current Pending Balance</div></div></div></a>';
echo '</center>';

echo '<br><div id="container_balance"><center>Loading balance chart...</center></div>';


$existQuery = "SELECT balance,time,txid,fee FROM payout_history WHERE address='$miner' ORDER BY id DESC LIMIT 50;";
$existResult = mysqli_query($mysqli,$existQuery)or die("Database Error");

echo '<table border="1" style="width:100%">';
$r = 0;
$count_r = mysqli_num_rows($existResult);
while ($row=mysqli_fetch_row($existResult)){
    $balance = $row[0];
    $balanceinlsk = floatval($balance/100000000);
    $r++;
    if ($r == $count_r) {
        echo '<b><br>Last 50 Payments</b><tr><td>'.$balanceinlsk.' LISK<br>When: '.gmdate("Y-M-d  h:i:s", $row[1]).'<br>Network fee deduced:'.$row[3].'<br>TXID:<a href="https://explorer.lisk.io/tx/'.$row[2].'" target="_blank">'.$row[2].'</a>';
    } else {
        echo '<tr><td>'.$balanceinlsk.' LISK<br>When: '.gmdate("Y-M-d  h:i:s", $row[1]).'<br>Network fee deduced:'.$row[3].'<br>TXID:<a href="https://explorer.lisk.io/tx/'.$row[2].'" target="_blank">'.$row[2].'</a>';
    }
    echo '</td></tr>';
}



    echo '</table>             <br><br>
                        </div><!--//row-->
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
                                <li><a href="../../">Home</a></li>
                                <li><a href="../../stats">Pool statistics</a></li>
                                <li><a href="../../charts">Charts</a></li>
                                <li><a href="/stats/miner/">Miner statistics</a></li>                            
                                <li><a href="mailto:mail@mail.com">Support</a></li>
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
    <script  type="text/javascript" src="/assets/plugins/jquery-1.11.2.min.js"></script>
    <script  type="text/javascript" src="/assets/plugins/jquery-migrate-1.2.1.min.js"></script>
    <script  type="text/javascript" src="/assets/plugins/bootstrap/js/bootstrap.min.js"></script> 
    <script  type="text/javascript" src="/assets/plugins/bootstrap-hover-dropdown.min.js"></script>       
    <script  type="text/javascript" src="/assets/plugins/back-to-top.js"></script>             
    <script  type="text/javascript" src="/assets/plugins/jquery-placeholder/jquery.placeholder.js"></script>                                                                  
    <script  type="text/javascript" src="/assets/plugins/jquery-match-height/jquery.matchHeight-min.js"></script>     
    <script  type="text/javascript" src="/assets/plugins/FitVids/jquery.fitvids.js"></script>
    <script  type="text/javascript" src="/assets/js/main.js"></script>     
    
    <!-- Form Validation -->
    <script  type="text/javascript" src="/assets/plugins/jquery.validate.min.js"></script> 
    <script  type="text/javascript" src="/assets/js/form-validation-custom.js"></script> 
    
    <!-- Form iOS fix -->
    <script  type="text/javascript" src="/assets/plugins/isMobile/isMobile.min.js"></script>
    <script  type="text/javascript" src="/assets/js/form-mobile-fix.js"></script>     
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="/charts/js/highstock.js"></script>
    <script src="/charts/js/modules/exporting.js"></script> 
    
        <script>
        $(".button-fill").hover(function () {
        $(this).children(".button-inside").addClass("full");
        }, function() {
        $(this).children(".button-inside").removeClass("full");
        });
    </script>
    <script type="text/javascript">
$(function () {
    $.getJSON("/api/index.php?data=_miner_balance&range=max&rr=1&dtx='.$miner.'", function (data) {
        $("#container_balance").highcharts("StockChart", {
            rangeSelector: {
            buttons: [{
                type: "hour",
                count: 1,
                text: "1h"
            },{
                type: "hour",
                count: 12,
                text: "12h"
            },{
                type: "day",
                count: 1,
                text: "1d"
            }, {
                type: "week",
                count: 1,
                text: "1w"
            }, {
                type: "month",
                count: 1,
                text: "1m"
            }, {
                type: "month",
                count: 6,
                text: "6m"
            }, {
                type: "year",
                count: 1,
                text: "1y"
            }, {
                type: "all",
                text: "All"
            }],
            selected: 1
        },
            chart: {
                backgroundColor: "#F5F5F5",
                polar: true,
                type: "area"
            },
            title : {
                text : "balance"
            },

            yAxis: {
                reversed: false,
                showFirstLabel: false,
                showLastLabel: true
            },

            series : [{
                name : "balance",
                data : data,
                threshold: null,
                fillColor : {
                    linearGradient : {
                        x1: 0,
                        y1: 1,
                        x2: 0,
                        y2: 0
                    },
                    stops : [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get("rgba")]
                    ]
                },
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    });
});

function zip(a, b) {
    return a.map(function(x, i) {
    return [x, b[i]];
    });
}
</script>



</body>
</html>
';
?>
