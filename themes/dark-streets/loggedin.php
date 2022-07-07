<?php


    if (!class_exists("mainTemplate")) {
        class mainTemplate {

            public $globalTemplates = array();

            public function __construct() {
     
                $this->globalTemplates["success"] = '<div class="alert alert-success">
                    <button type="button" class="close">
                        <span>&times;</span>
                    </button>
                    <{text}>
                </div>';
                $this->globalTemplates["error"] = '<div class="alert alert-danger">
                    <button type="button" class="close">
                        <span>&times;</span>
                    </button>
                    <{text}>
                </div>';
                $this->globalTemplates["info"] = '<div class="alert alert-info">
                    <button type="button" class="close">
                        <span>&times;</span>
                    </button>
                    <{text}>
                </div>';
                $this->globalTemplates["warning"] = '<div class="alert alert-warning">
                    <button type="button" class="close">
                        <span>&times;</span>
                    </button>
                    <{text}>
                </div>';

            }
        
            public $pageMain =  '<!DOCTYPE html>
    <html>
        <head>
			<!-- fontawesome kit url -->
            <link href="themes/{_theme}/css/bootstrap.min.css" rel="stylesheet" />
            <link href="themes/{_theme}/css/style.css" rel="stylesheet" />
            {#if moduleCSSFile}
                <link href="{moduleCSSFile}" rel="stylesheet" />
            {/if}
            <link rel="shortcut icon" href="themes/{_theme}/images/icon.png" />
            <meta name="timestamp" content="{timestamp}">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>{game_name} - {page}</title>
        </head>
        <body class="user-status-{userStatus}">

            <div class="mobile-menu">
                <div class="close-mobile-menu">Close</div>
                <div class="wraper">
                    {#each menus}
                        <div class="title">{title}</div>
						<ul class="nav nav-pills nav-stacked">
                        {#each items}
                            {#if seperator}
                                <li class="sep"></li>
                            {/if}
                            {#unless seperator}
                                {#unless hide}
                                    <li class="col-xs-4"><a href="{url}">{text}</a></li>
                                {/unless}
                            {/unless}
                        {/each}
					</ul>
                    {/each}
				</div>
			</div>

            <div class="header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 logo-container text-left">
							<button type="button" class="mobile-toggle"><i class="fas fa-bars" title="Toggle navigation"></i></button>
                            <a href="?page=loggedin"><img src="themes/{_theme}/images/logo.png" alt="{game_name}" /></a>
                        </div>
                        <div class="col-md-2 col-xs-12">
                            <div class="row">
                                <div class="col-md-12 col-xs-6">
                                    <a href="?page=mail">
                                        <i class="fas fa-envelope"></i> Mail{#if mail} ({mail}){/if}<br />
                                    </a>
                                </div>
                                <div class="col-md-12 col-xs-6">
                                    <a href="?page=notifications">
                                        <i class="fas fa-bell"></i> Notifications{#if notificationCount} ({notificationCount}){/if}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                                <i class="fas fa-dollar-sign"></i> <strong class="hidden-xs">Money:</strong> {money} <br />
                                <div class="hidden-xs">
                                    <i class="fas fa-circle"></i> <strong>Bullets:</strong> {bullets}
                                </div>
                        </div>
                        <div class="col-md-3 col-xs-6">
                            <i class="fas fa-level-up-alt"></i> <strong class="hidden-xs">Rank:</strong> {rank} {#unless maxRank}({exp_perc}%){/unless}<br />
                            <div class="hidden-xs">
                                <i class="fas fa-users"></i> <strong>Family:</strong> {gang.name}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		<div class="sub-header">
			<div class="container">
                <div class="row">
                    <div class="col-md-12>
                        <div class="row">
                            <div class="col-xs-2 col-md-2" data-timer-type="name" data-timer="{crime_timer}">
                                <a href="?page=crimes">
                                    <span><i class="fas fa-fire"></i><font class="hidden-xs hidden-sm"> Crime</font></span><span></span>
                                </a>
                            </div>
                            <div class="col-xs-2 col-md-2" data-timer-type="name" data-timer="{theft_timer}">
                                <a href="?page=theft">
                                    <span><i class="fas fa-car"></i><font class="hidden-xs hidden-sm"> Theft</font></span><span></span>
                                </a>
                            </div>
                            <div class="col-xs-2 col-md-2" data-timer-type="name" data-timer="{chase_timer}">
                                <a href="?page=policeChase">
                                    <span><i class="fas fa-user-tie"></i><font class="hidden-xs hidden-sm"> Police Chase</font></span><span></span>
                                </a>
                            </div>
                            <div class="col-xs-2 col-md-2" data-timer-type="name" data-timer="{jail_timer}">
                                <a href="?page=jail">
                                    <span><i class="fas fa-bars fa-rotate-90"></i><font class="hidden-xs hidden-sm"> Jail</font></span><span></span>
                                </a>
                            </div>
                            <div class="col-xs-2 col-md-2" data-timer-type="name" data-timer="{bullet_timer}">
                                <a href="?page=bullets">
                                    <span><i class="fas fa-industry"></i><font class="hidden-xs hidden-sm"> Bullet Factory</font></span><span></span>
                                </a>
                            </div>
                            <div class="col-xs-2 col-md-2" data-timer-type="name" data-timer="{travel_timer}">
                                <a href="?page=travel">
                                    <span><i class="fas fa-plane"></i><font class="hidden-xs hidden-sm"> Travel</font></span><span></span>
                                </a>
                            </div>
                        </div>
                        
                    </div>
                </div>
			</div>
		</div>

            <div class="layout-container">                
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3 col-md-2 navigation text-center"> 
                            {#each menus}
                                {#if items}
                                    <div class="panel hidden-xs">
                                        <div class="panel-heading">
                                          {title}
                                        </div>
                                        <div class="panel-body menu">
											<ul class="navigation">
												{#each items}
													{#if seperator}
														<li class="sep">
													{/if}
													{#unless seperator}
														{#unless hide}
															{#if url}
																<li><a href="{url}">{text}</a></li>
															{/if}
														{/unless}
													{/unless}
												{/each}
											</ul>
                                        </div>
                                    </div>
                                {/if}
                            {/each}

                        </div>
                        
                        <div class="col-sm-9 col-md-10 game-container text-center">
                            <div data-ajax-element="alerts" data-ajax-type="html">
                                <{alerts}>
                            </div>
                            <div data-ajax-element="game" data-ajax-type="html">
                                <{game}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="themes/{_theme}/js/jquery.js"></script>
            <!--<script src="themes/{_theme}/js/bootstrap.min.js"></script>-->
            <!--<script src="themes/{_theme}/js/ajax.js"></script>-->
            <script src="themes/{_theme}/js/timer.js"></script>
            <script src="themes/{_theme}/js/mobile.js"></script>
            {#if moduleJSFile}
                <script src="{moduleJSFile}"></script>
            {/if}
        </body>
    </html>';
            
        }
    }
?>
