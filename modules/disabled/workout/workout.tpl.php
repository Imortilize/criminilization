<?php

    class workoutTemplate extends template {

        public $options = '

            <form method="post" action="?page=admin&module=workout&action=options">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Cost to reset energy ({_setting "pointsName"})</label>
                            <input type="number" class="form-control" name="workoutResetEnergy" value="{workoutResetEnergy}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Cost to reset will ({_setting "pointsName"})</label>
                            <input type="number" class="form-control" name="workoutResetWill" value="{workoutResetWill}" />
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>

            </form>
        ';
        
        public $trainSuccess = '
            <p> 
                You trained your {stat} {times} times!
            </p>
            <p> 
                You gained {gain} {stat}!
            </p>
        ';
        public $workout = '
            <div class="row">
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Train Stats
                        </div>
                        <div class="panel-body">
                            <form method="post" action="?page=workout&action=train">
                                <p>
                                    How many times do you want to train?
                                </p>

                                <p>
                                    <div class="input-group input-group-inline train-select">
                                        <input name="times" type="number" class="form-control" aria-label="Amount (to the nearest dollar)" value="{energy}">
                                        <span class="input-group-addon">/{maxEnergy}</span>
                                    </div>
                                </p>

                                <p>
                                    What stat would you like to train?
                                </p>

                                <div class="row">
                                    <div class="col-md-3">
                                        <button name="stat" value="1" class="btn btn-success btn-block">
                                            Strength
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button name="stat" value="2" class="btn btn-warning btn-block">
                                            Agility
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button name="stat" value="3" class="btn btn-info btn-block">
                                            Guard
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button name="stat" value="4" class="btn btn-danger btn-block">
                                            Labour
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="?page=workout&action=resetEnergy" class="btn btn-sm btn-block btn-success">
                                        Reset Energy to 100%<br />
                                        <small> Costs {energyResetCost} {_setting "pointsName"}</small>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="?page=workout&action=resetWill" class="btn btn-sm btn-block btn-info">
                                        Reset Will to 100%<br />
                                        <small> Costs {willResetCost} {_setting "pointsName"}</small>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Stats
                        </div>
                        <div class="panel-body">
                            <ul class="list-group">
                                <li class="list-group-item text-left">
                                    <strong class="text-success">Strength</strong>
                                    <span class="badge">{floor stats.strength}</span>
                                </li>
                                <li class="list-group-item text-left">
                                    <strong class="text-warning">Agility</strong>
                                    <span class="badge">{floor stats.agility}</span>
                                </li>
                                <li class="list-group-item text-left">
                                    <strong class="text-info">Guard</strong>
                                    <span class="badge">{floor stats.guard}</span>
                                </li>
                                <li class="list-group-item text-left">
                                    <strong class="text-danger">Labour</strong>
                                    <span class="badge">{floor stats.labour}</span>
                                </li>
                            </ul>
                            <ul class="list-group total-stats">
                                <li class="list-group-item text-left">
                                    <strong>Total</strong>
                                    <span class="badge">{floor stats.total}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        ';
        
    }

?>