<?php

    class policeChaseTemplate extends template {
    
        public $carSelect = '
            {#each cars}
                <option value="{id}">{label}</option>
            {/each}
        ';

        public $policeChaseSelect = '
            <div class="panel panel-default">
                <div class="panel-heading">What direction do you want to drive?</div>
                <div class="panel-body">
                    <div class="alert alert-warning">
                        Your mate is just about to be busted, he needs a getaway driver!
                    </div>
                    <form method="post" action="?page=policeChase&action=select">
                        <div style="text-align:center;">
                            <p>
                                <strong>Select a car to drive:</strong><br />
                                <select class="form-control form-control-inline" name="car">
                                    {>carSelect}
                                </select>
                            </p>
                            <p>
                            <input type="submit" class="button-fixed-width btn btn-default" value="Drive" /></a></p>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        ';
        public $policeChase = '

            <div class="panel panel-default">
                <div class="panel-heading">What direction do you want to drive?</div>
                <div class="panel-body">

                    <p class="alert alert-info">
                        You are driving a {car.CA_name} with {car.GA_damage}% damage!
                    </p>

                    <form method="post" action="?page=policeChase&action=move">
                        <!--<input type="hidden" name="_CSFR" value="{_CSFRToken}" />-->
                        <div style="text-align:center;">
                            <input type="submit" class="button-fixed-width btn btn-default" value="Foward" />
                            <br />
                            <input type="submit" class="button-fixed-width btn btn-default" value="Left" />
                            <span class="button-fixed-width move-icon">
                                <i class="glyphicon glyphicon-fullscreen"></i>
                            </span>
                            <input type="submit" class="button-fixed-width btn btn-default" value="Right" />
                            <br />
                            <input type="submit" class="button-fixed-width btn btn-default" value="U-Turn" /></a></p>
                        </div>
                    </form>
                </div>
            </div>
        ';

         public $cars = '

            <form method="post" action="?page=admin&module=policeChase&action=cars">

                {#each cars}
                    <h5>{name} ({#money value})</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="pull-left">Chance Of Success</label>
                                <input type="text" class="form-control" name="cars[ {id} ][success]" value="{success}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="pull-left">Chance Of Continue</label>
                                <input type="text" class="form-control" name="cars[ {id} ][continue]" value="{continue}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="pull-left">Chance Of Fail</label>
                                <input type="text" class="form-control" name="cars[ {id} ][fail]" value="{fail}" />
                            </div>
                        </div>
                    </div>
                {/each}

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>

            </form>
        ';
         public $options = '

            <form method="post" action="?page=admin&module=policeChase&action=options">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Minimum Reward (per rank)</label>
                            <input type="text" class="form-control" name="minReward" value="{minReward}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Maximum Reward (per rank)</label>
                            <input type="text" class="form-control" name="maxReward" value="{maxReward}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Minimum damage per turn</label>
                            <input type="text" class="form-control" name="minDamage" value="{minDamage}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Maximum damage per turn</label>
                            <input type="text" class="form-control" name="maxDamage" value="{maxDamage}" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Cooldown</label>
                            <input type="text" class="form-control" name="cooldown" value="{cooldown}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Jail Time</label>
                            <input type="text" class="form-control" name="jailTime" value="{jailTime}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">EXP Gain</label>
                            <input type="text" class="form-control" name="expGain" value="{expGain}" />
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>

            </form>
        ';
        
    }

