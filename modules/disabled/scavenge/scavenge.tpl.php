<?php

    class scavengeTemplate extends template {

         public $scavengeOptions = '

            <form method="post" action="?page=admin&module=scavenge&action=options">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Number of rounds to scavenge</label>
                            <input type="text" class="form-control" name="scavenge_rounds" value="{scavenge_rounds}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Chance of failing</label>
                            <input type="text" class="form-control" name="scavenge_chance_failed" value="{scavenge_chance_failed}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Chance of finding money</label>
                            <input type="text" class="form-control" name="scavenge_chance_money" value="{scavenge_chance_money}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Minimum money to find ($)</label>
                            <input type="text" class="form-control" name="scavenge_reward_min_money" value="{scavenge_reward_min_money}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Maximum money to find ($)</label>
                            <input type="text" class="form-control" name="scavenge_reward_max_money" value="{scavenge_reward_max_money}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Chance of finding bullets</label>
                            <input type="text" class="form-control" name="scavenge_chance_bullets" value="{scavenge_chance_bullets}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Minimum bullets to find</label>
                            <input type="text" class="form-control" name="scavenge_reward_min_bullets" value="{scavenge_reward_min_bullets}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Maximum bullets to find</label>
                            <input type="text" class="form-control" name="scavenge_reward_max_bullets" value="{scavenge_reward_max_bullets}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Chance of finding {_setting "pointsName"}</label>
                            <input type="text" class="form-control" name="scavenge_chance_points" value="{scavenge_chance_points}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Minimum {_setting "pointsName"} to find</label>
                            <input type="text" class="form-control" name="scavenge_reward_min_points" value="{scavenge_reward_min_points}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Maximum {_setting "pointsName"} to find</label>
                            <input type="text" class="form-control" name="scavenge_reward_max_points" value="{scavenge_reward_max_points}" />
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>

            </form>
        ';
         
         public $scavenge = '
            <div class="panel panel-default gang-home">
                <div class="panel-heading text-center"> Scavenge The Streets </div>
                <div class="panel-body text-left">
                    <ul class="list-group text-left">
                    	{#each actions}
	                        <li class="list-group-item">
	                        	<span class="text-{class}">{text}</span>
	                        </li>
                        {/each}
                  </ul>
               </div>
            </div>
        ';
    }

?>