<?php

    class usersOnlineTemplate extends template {
    
        public $usersOnline = '
            <div class="panel panel-default">
                <div class="panel-heading">Users Online</div>
                <div class="panel-body">
                    {#each users}
                        <div class="user-online">
                            <img class="user-online-avatar img-thumbnail" src="{user.profilePicture}" alt="{user.name} profile picture" />  
                            <div class="user-online-info">
                                {>userName} <small class="text-muted pull-right" title="{date}">{_ago laston} ago</small> <br />
                                <small>
                                    {rank} <br />
                                    <a href="?page=gangs&action=view&id={gang.id}"> {gang.name} </a>
                                </small> 

                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-default user-online-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {#each profileLinks}
                                            <li>
                                                <a href="{url}">
                                                    {text}
                                                </a>
                                            </li>
                                        {/each}
                                    </ul>
                                </div>

                            </div>
                        </div>
                    {/each}
                </div>
            </div>
        ';
        
    }
