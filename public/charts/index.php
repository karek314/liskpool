<?php
echo '<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->  
<head>
    <title>Charts - LISK Delegate Pool</title>
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
                            <li class="nav-item"><a href="../stats">Stats</a></li> 
                            <li class="active nav-item"><a href="../charts">Charts</a></li>
                            <li class="nav-item"><a href="../stats/miner/">Forger Stats</a></li>               
                            <li class="nav-item last"><a href="mailto:mail@mail.com">Support</a></li>
                        </ul><!--//nav-->
                    </div><!--//navabr-collapse-->
                </nav><!--//main-nav-->
            </div><!--//container-->
        </header><!--//header-->   
        
    
    <!-- ******Contact Section****** --> 
    <section class="contact-section section">
        <div class="container">
            <h2 class="title text-center"><br>Charts</h2>
            <p class="intro text-left"></p>
             <p class="intro text-left"><font color="F22613"></p></font>
            <form id="contact-form" class="contact-form form" method="post" action="push.php">                    
                <div class="row text-left">
                    <div class="contact-form-inner col-md-8 col-sm-12 col-xs-12 col-md-offset-2 col-sm-offset-0 xs-offset-0">
                        <div class="row"> ';
                        echo '<div id="container"><center><img height="54" width="54" src="../assets/images/loading.gif"/><br>Loading approval chart</center></div><br>';
                        echo '<br><br><div id="container_rank"><center><img height="54" width="54" src="../assets/images/loading.gif"/><br>Loading rank chart</center></div><br>';
                        echo '<br><br><div id="container_balance"><center><img height="54" width="54" src="../assets/images/loading.gif"/><br>Loading balance chart</center></div><br>';
                        echo '<br><br><div id="container_miners"><center><img height="54" width="54" src="../assets/images/loading.gif"/><br>Loading vote count chart</center></div><br>';
                        echo '</div><!--//row-->
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
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script src="./js/highstock.js"></script>
    <script src="./js/modules/exporting.js"></script> 
    '; ?>
    <script type="text/javascript">
$(function () {
    $.getJSON("/data/approval.json", function (data) {
        $("#container").highcharts("StockChart", {
            rangeSelector: {
            buttons: [{
                type: 'hour',
                count: 1,
                text: '1h'
            },{
                type: 'hour',
                count: 12,
                text: '12h'
            },{
                type: 'day',
                count: 1,
                text: '1d'
            },{
                type: 'day',
                count: 3,
                text: '3d'
            }, {
                type: 'week',
                count: 1,
                text: '1w'
            }, {
                type: 'month',
                count: 1,
                text: '1m'
            }, {
                type: 'month',
                count: 6,
                text: '6m'
            }, {
                type: 'year',
                count: 1,
                text: '1y'
            }, {
                type: 'all',
                text: 'All'
            }],
            selected: 3
        },
            chart: {
                backgroundColor: "#F5F5F5",
                polar: true,
                type: "area"
            },
            colors: ["#000000", "#000000", "#000000"],
            title : {
                text : "Community Approval (%)"
            },
            yAxis: {
                reversed: false,
                showFirstLabel: false,
                showLastLabel: true
            },

            series : [{
                name : "Community Approval (%)",
                data : data,
                threshold: null,
                fillColor : "#26C281",
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    });
    setTimeout(rank, 0);
    setTimeout(balance, 0);
    setTimeout(miners, 0);
});

function rank() {
    $.getJSON("/data/rank.json", function (data) {
        $("#container_rank").highcharts("StockChart", {
            rangeSelector: {
            buttons: [{
                type: 'hour',
                count: 1,
                text: '1h'
            },{
                type: 'hour',
                count: 12,
                text: '12h'
            },{
                type: 'day',
                count: 1,
                text: '1d'
            },{
                type: 'day',
                count: 3,
                text: '3d'
            }, {
                type: 'week',
                count: 1,
                text: '1w'
            }, {
                type: 'month',
                count: 1,
                text: '1m'
            }, {
                type: 'month',
                count: 6,
                text: '6m'
            }, {
                type: 'year',
                count: 1,
                text: '1y'
            }, {
                type: 'all',
                text: 'All'
            }],
            selected: 3
        },
            chart: {
                backgroundColor: "#F5F5F5",
                polar: true,
                type: "area"
            },
            colors: ["#000000", "#000000", "#000000"],
            title : {
                text : "Rank"
            },

            yAxis: {
                reversed: false,
                showFirstLabel: false,
                showLastLabel: true
            },

            series : [{
                name : "Rank",
                data : data,
                threshold: null,
                fillColor : "#26C281",
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    });

};

function balance() {
    $.getJSON("/data/balance.json", function (data) {
        $("#container_balance").highcharts("StockChart", {
            rangeSelector: {
            buttons: [{
                type: 'hour',
                count: 1,
                text: '1h'
            },{
                type: 'hour',
                count: 12,
                text: '12h'
            },{
                type: 'day',
                count: 1,
                text: '1d'
            },{
                type: 'day',
                count: 3,
                text: '3d'
            }, {
                type: 'week',
                count: 1,
                text: '1w'
            }, {
                type: 'month',
                count: 1,
                text: '1m'
            }, {
                type: 'month',
                count: 6,
                text: '6m'
            }, {
                type: 'year',
                count: 1,
                text: '1y'
            }, {
                type: 'all',
                text: 'All'
            }],
            selected: 3
        },
            chart: {
                backgroundColor: "#F5F5F5",
                polar: true,
                type: "area"
            },
            colors: ["#000000", "#000000", "#000000"],
            title : {
                text : "Balance (LSK)"
            },

            yAxis: {
                reversed: false,
                showFirstLabel: false,
                showLastLabel: true
            },

            series : [{
                name : "Balance (LSK)",
                data : data,
                threshold: null,
                fillColor : "#26C281",
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    });

};

function miners() {
    $.getJSON("/data/voters.json", function (data) {
        $("#container_miners").highcharts("StockChart", {
            rangeSelector: {
            buttons: [{
                type: 'hour',
                count: 1,
                text: '1h'
            },{
                type: 'hour',
                count: 12,
                text: '12h'
            },{
                type: 'day',
                count: 1,
                text: '1d'
            },{
                type: 'day',
                count: 3,
                text: '3d'
            }, {
                type: 'week',
                count: 1,
                text: '1w'
            }, {
                type: 'month',
                count: 1,
                text: '1m'
            }, {
                type: 'month',
                count: 6,
                text: '6m'
            }, {
                type: 'year',
                count: 1,
                text: '1y'
            }, {
                type: 'all',
                text: 'All'
            }],
            selected: 3
        },
            chart: {
                backgroundColor: "#F5F5F5",
                polar: true,
                type: "area"
            },
            colors: ["#000000", "#000000", "#000000"],
            title : {
                text : "Votes Count"
            },

            yAxis: {
                reversed: false,
                showFirstLabel: false,
                showLastLabel: true
            },

            series : [{
                name : "Votes Count",
                data : data,
                threshold: null,
                fillColor : "#26C281",
                tooltip: {
                    valueDecimals: 2
                }
            }]
        });
    });

};


function zip(a, b) {
    return a.map(function(x, i) {
    return [x, b[i]];
    });
}
</script>



</body>
</html> 
