<?php

    class mainTemplate {

        public function __construct() {
            global $db, $page;


            $usersOnline = $db->prepare("
                SELECT COUNT(*) as 'count' FROM userTimers WHERE UT_desc = 'laston' AND UT_time > ".(time()-900)."
            ");
            $usersOnline->execute();
            $users = $db->prepare("
                SELECT COUNT(*) as 'count' FROM users
            ");
            $users->execute();

            $page->addToTemplate("usersOnlineNow", number_format($usersOnline->fetch(PDO::FETCH_ASSOC)["count"]));
            $page->addToTemplate("registeredUsers", number_format($users->fetch(PDO::FETCH_ASSOC)["count"]));

        }
        
        public $pageMain =  '<!doctype html>
<html>
<head>
	<!-- fontawesome kit url -->
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>{game_name} - {page}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1">
    <link rel="stylesheet" type="text/css" href="themes/{_theme}/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="themes/{_theme}/css/style.css">	
    <link rel="stylesheet" type="text/css" href="themes/{_theme}/css/login.css">	
	<link rel="shortcut icon" href="themes/{_theme}/images/icon.png" />
	<link href="https://fonts.googleapis.com/css?family=roboto" rel="stylesheet">
</head>
<body>
	<div class="header">
		<div class="login-logo">
			<img src="themes/{_theme}/images/logo.png" alt="{game_name}" />
		</div>
	</div>
	<div class="sub-header">
		<div class="container text-center nav_wraper">
			<ul class="nav">
				<li class="active"><a href="?page=login"><i class="fa fa-sign-in"></i><span class="hidden-xs">login</span></a></li>
				<li><a href="?page=register"><i class="fa fa-check-square"></i><span class="hidden-xs">register</span></a></li>
				<li><a href="?page=forgotPassword"><i class="fa fa-unlock-alt"></i><span class="hidden-xs">forgot pass</span></a></li>
				<li><a href="?page=news"><i class="fa fa-newspaper-o"></i><span class="hidden-xs">News</span></a></li>
			</ul>
		</div>
	</div>
	<div class="container">
		<div class="content"> 
			<div class="pagearea">
				<div class="row">
					<div class="col-sm-6 col-sm-push-1">
						<div class="panel login_panel">
							<div class="panel-heading">{page}</div>
							<div class="panel-body">
								<{game}>
							</div>
						</div>
					</div>
					<div class="col-sm-4 col-sm-push-1">
						<div class="panel">
							<div class="panel-heading">
								Stats
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-7"><b>Gangsters:</b></div>
									<div class="col-md-3">{registeredUsers}</div>
								</div>
								<div class="row">
									<div class="col-md-7"><b>Gangsters Online:</b></div>
									<div class="col-md-3">{usersOnlineNow}</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="themes/{_theme}/js/jquery.js"></script>
	<!--<script src="themes/{_theme}/js/bootstrap.min.js"></script>-->
	<script src="themes/{_theme}/js/timer.js"></script>
	<script src="themes/{_theme}/js/mobile.js"></script>
</body>
</html>';
    
    }
?>