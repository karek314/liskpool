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
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
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
                        echo '<div id="container"></div><br>';
                        echo '<br><br><div id="container_rank"></div><br>';
                        echo '<br><br><div id="container_balance"></div><br>';
                        echo '<br><br><div id="container_miners"><center></div><br>';
                        echo '<br><br><div id="container_reserve"></div><br>';
                        echo '<br><br><div id="container_productivity"></div><br>';
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
    '; ?>
<script type="text/javascript">
Plotly.d3.json("/data/approval.json", function(err, rows){
    var trace1 = {
        type: "scatter",
        mode: "lines",
        name: 'Community Approval (%)',
        x: [],
        y: [],
        line: {color: '#17BECF'}
    }
    for (var i=0; i<rows.length; i++) {
        var row = rows[i];
        trace1.x.push(row[0]);
        trace1.y.push(row[1]);
    }
    var data = [trace1];
    var layout = {
        title: 'Community Approval (%)',
        plot_bgcolor: 'rgba(124, 1, 1, 0)',
        paper_bgcolor: 'rgba(125,1,1,0)',
        xaxis: {
            autorange: true,
            rangeselector: {buttons: [{
                count: 1,
                label: '1h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 12,
                label: '12h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1d',
                step: 'day',
                stepmode: 'backward'
            },{
                count: 3,
                label: '3d',
                step: 'day',
                stepmode: 'backward',
            },{
                count: 1,
                label: '1w',
                step: 'week',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 6,
                label: '6m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1y',
                step: 'year',
                stepmode: 'backward'
            },{
                step: 'all'
            }]},
            rangeslider: {},type: 'date'
        },
        yaxis: {
            autorange: true,
            type: 'linear'
        }
    };
    Plotly.newPlot('container', data, layout);
});
Plotly.d3.json("/data/rank.json", function(err, rows){
    var trace1 = {
        type: "scatter",
        mode: "lines",
        name: 'Rank',
        x: [],
        y: [],
        line: {color: '#17BECF'}
    }
    for (var i=0; i<rows.length; i++) {
        var row = rows[i];
        trace1.x.push(row[0]);
        trace1.y.push(row[1]);
    }
    var data = [trace1];
    var layout = {
        title: 'Rank',
        plot_bgcolor: 'rgba(124, 1, 1, 0)',
        paper_bgcolor: 'rgba(125,1,1,0)',
        xaxis: {
            autorange: true,
            rangeselector: {buttons: [{
                count: 1,
                label: '1h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 12,
                label: '12h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1d',
                step: 'day',
                stepmode: 'backward'
            },{
                count: 3,
                label: '3d',
                step: 'day',
                stepmode: 'backward',
            },{
                count: 1,
                label: '1w',
                step: 'week',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 6,
                label: '6m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1y',
                step: 'year',
                stepmode: 'backward'
            },{
                step: 'all'
            }]},
            rangeslider: {},type: 'date'
        },
        yaxis: {
            autorange: true,
            type: 'linear'
        }
    };
    Plotly.newPlot('container_rank', data, layout);
});
Plotly.d3.json("/data/balance.json", function(err, rows){
    var trace1 = {
        type: "scatter",
        mode: "lines",
        name: 'Account balance',
        x: [],
        y: [],
        line: {color: '#17BECF'}
    }
    for (var i=0; i<rows.length; i++) {
        var row = rows[i];
        trace1.x.push(row[0]);
        trace1.y.push(row[1]);
    }
    var data = [trace1];
    var layout = {
        title: 'Account balance',
        plot_bgcolor: 'rgba(124, 1, 1, 0)',
        paper_bgcolor: 'rgba(125,1,1,0)',
        xaxis: {
            autorange: true,
            rangeselector: {buttons: [{
                count: 1,
                label: '1h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 12,
                label: '12h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1d',
                step: 'day',
                stepmode: 'backward'
            },{
                count: 3,
                label: '3d',
                step: 'day',
                stepmode: 'backward',
            },{
                count: 1,
                label: '1w',
                step: 'week',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 6,
                label: '6m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1y',
                step: 'year',
                stepmode: 'backward'
            },{
                step: 'all'
            }]},
            rangeslider: {},type: 'date'
        },
        yaxis: {
            autorange: true,
            type: 'linear'
        }
    };
    Plotly.newPlot('container_balance', data, layout);
});
Plotly.d3.json("/data/reserve.json", function(err, rows){
    var trace1 = {
        type: "scatter",
        mode: "lines",
        name: 'Reserve balance',
        x: [],
        y: [],
        line: {color: '#17BECF'}
    }
    for (var i=0; i<rows.length; i++) {
        var row = rows[i];
        trace1.x.push(row[0]);
        trace1.y.push(row[1]);
    }
    var data = [trace1];
    var layout = {
        title: 'Reserve balance',
        plot_bgcolor: 'rgba(124, 1, 1, 0)',
        paper_bgcolor: 'rgba(125,1,1,0)',
        xaxis: {
            autorange: true,
            rangeselector: {buttons: [{
                count: 1,
                label: '1h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 12,
                label: '12h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1d',
                step: 'day',
                stepmode: 'backward'
            },{
                count: 3,
                label: '3d',
                step: 'day',
                stepmode: 'backward',
            },{
                count: 1,
                label: '1w',
                step: 'week',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 6,
                label: '6m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1y',
                step: 'year',
                stepmode: 'backward'
            },{
                step: 'all'
            }]},
            rangeslider: {},type: 'date'
        },
        yaxis: {
            autorange: true,
            type: 'linear'
        }
    };
    Plotly.newPlot('container_reserve', data, layout);
});
Plotly.d3.json("/data/productivity.json", function(err, rows){
    var trace1 = {
        type: "scatter",
        mode: "lines",
        name: 'Forged blocks (% lifetime)',
        x: [],
        y: [],
        line: {color: '#17BECF'}
    }
    for (var i=0; i<rows.length; i++) {
        var row = rows[i];
        trace1.x.push(row[0]);
        trace1.y.push(row[1]);
    }
    var data = [trace1];
    var layout = {
        title: 'Forged blocks (% lifetime)',
        plot_bgcolor: 'rgba(124, 1, 1, 0)',
        paper_bgcolor: 'rgba(125,1,1,0)',
        xaxis: {
            autorange: true,
            rangeselector: {buttons: [{
                count: 1,
                label: '1h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 12,
                label: '12h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1d',
                step: 'day',
                stepmode: 'backward'
            },{
                count: 3,
                label: '3d',
                step: 'day',
                stepmode: 'backward',
            },{
                count: 1,
                label: '1w',
                step: 'week',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 6,
                label: '6m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1y',
                step: 'year',
                stepmode: 'backward'
            },{
                step: 'all'
            }]},
            rangeslider: {},type: 'date'
        },
        yaxis: {
            autorange: true,
            type: 'linear'
        }
    };
    Plotly.newPlot('container_productivity', data, layout);
});




Plotly.d3.json("/data/voters.json", function(err, rows){
    var trace1 = {
        type: "scatter",
        mode: "lines",
        name: 'Votes Count',
        x: [],
        y: [],
        line: {color: '#17BECF'}
    }
    for (var i=0; i<rows.length; i++) {
        var row = rows[i];
        trace1.x.push(row[0]);
        trace1.y.push(row[1]);
    }
    var data = [trace1];
    var layout = {
        title: 'Votes Count',
        plot_bgcolor: 'rgba(124, 1, 1, 0)',
        paper_bgcolor: 'rgba(125,1,1,0)',
        xaxis: {
            autorange: true,
            rangeselector: {buttons: [{
                count: 1,
                label: '1h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 12,
                label: '12h',
                step: 'hour',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1d',
                step: 'day',
                stepmode: 'backward'
            },{
                count: 3,
                label: '3d',
                step: 'day',
                stepmode: 'backward',
            },{
                count: 1,
                label: '1w',
                step: 'week',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 6,
                label: '6m',
                step: 'month',
                stepmode: 'backward'
            },{
                count: 1,
                label: '1y',
                step: 'year',
                stepmode: 'backward'
            },{
                step: 'all'
            }]},
            rangeslider: {},type: 'date'
        },
        yaxis: {
            autorange: true,
            type: 'linear'
        }
    };
    Plotly.newPlot('container_miners', data, layout);
});
</script>



</body>
</html> 