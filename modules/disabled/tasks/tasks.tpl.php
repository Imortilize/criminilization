<?php

/**
* This module gives the user new missions to do during the day
*
* @package Missions
* @author Chris Day
* @version 1.0.0
*/

class tasksTemplate extends template {

         public $options = '

            <form method="post" action="?page=admin&module=tasks&action=options">

                <div class="row">
                    <div class="col-md-4">
                    	<h5>EXP Reward</h5>
                    </div>
                    <div class="col-md-4">
                    	<h5>Money Reward</h5>
                    </div>
                    <div class="col-md-4">
                    	<h5>Bullets Reward</h5>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Min</label>
                            <input type="text" class="form-control" name="missionMinExp" value="{missionMinExp}" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Max</label>
                            <input type="text" class="form-control" name="missionMaxExp" value="{missionMaxExp}" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Min</label>
                            <input type="text" class="form-control" name="missionMinMoney" value="{missionMinMoney}" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Max</label>
                            <input type="text" class="form-control" name="missionMaxMoney" value="{missionMaxMoney}" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Min</label>
                            <input type="text" class="form-control" name="missionMinBullets" value="{missionMinBullets}" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Max</label>
                            <input type="text" class="form-control" name="missionMaxBullets" value="{missionMaxBullets}" />
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>

            </form>
        ';

	public $missionSuccess = '
		<p class="text-left">
			You sucessfully completed the mission, your reward was:
		</p>
		<ul class="text-left">
			<li>
				<strong>Exp:</strong> {number_format exp}
			</li>
			<li>
				<strong>Money:</strong> ${number_format money}
			</li>
			<li>
				<strong>Bullets:</strong> {number_format bullets}
			</li>
		</ul>
	';

	public $missions = '
		<div class="panel panel-default">
			<div class="panel-heading">
				Task
			</div>
			<div class="panel-body">
				{#if inMission}
					<p>
						{desc}
					</p>

					<div class="row">
						<div class="col-md-3">
						</div>
						<div class="col-md-6">
							<div class="progress">
								<div class="progress-bar" style="width: {_missionProgress mission}%"></div>
							</div>
						</div>
					</div>

					{#unless mission.done}
						<p>	
							Current Progress: {mission.progress}/{mission.count}
						</p>	
					{/unless}

					{#if mission.done}
						<p>	
							<a href="?page=tasks&action=claim" class="btn btn-success">
								Claim Reward
							</a>
						</p>
					{/if}
					
					<p>	
						<small>
							<a href="?page=tasks&action=skip">
								Skip Mission
							</a>
						</small>
					</p>
				{else}
					<p>
						Click below to start a new task, you will gain a reward for completing it!
					</p>
					<p>
						Once started you will have 8 hours to finish!
					</p>
					<p>
						<a href="?page=tasks&action=start" class="btn btn-success">
							Start Task
						</a>
					</p>
				{/if}
			</div>
		</div>
	';

}
