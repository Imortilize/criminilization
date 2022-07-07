<?php

   class levelsTemplate extends template {

        public $rankUp = '
            <div class="panel panel-default">
                <div class="panel-header">
                    Rank Up
                </div>
                <div class="panel-body">

                    <p>
                        You are currently
                    </p>
                    <h1>{rank.R_name}</h1>

                    {#if maxRank}
                        <h4>You are maxed out!</h4>
                    {/if}

                    {#unless maxRank}

                        <div class="progress">
                            <div class="progress-bar" style="width:{expperc}%"></div>
                        </div>
                        <small>{expperc}% to the next level</small>

                        {#if rankup}
                            <a href="?page=levels&action=rankup" class="btn btn-success">Rank Up!</a>
                        {/if}
                    {/unless}

                </div>
            </div>
        ';

        public $options = '
            <form method="post" action="?page=admin&module=levels&action=options">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level Structure</label>
                            <input type="text" class="form-control" name="structure" value="{structure}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">EXP Multiplier</label>
                            <input type="text" class="form-control" name="exp" value="{exp}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Money Per Level</label>
                            <input type="text" class="form-control" name="money" value="{money}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Bullets Per Level</label>
                            <input type="text" class="form-control" name="bullets" value="{bullets}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Base Health</label>
                            <input type="text" class="form-control" name="healthBase" value="{healthBase}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Health Per Level</label>
                            <input type="text" class="form-control" name="health" value="{health}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Max. Level</label>
                            <input type="text" class="form-control" name="qty" value="{qty}">
                        </div>
                    </div>
                </div>

                <button class="btn btn-success">
                    Update Options
                </button>
            </form>
            <hr />
            <form method="post" action="?page=admin&module=levels&action=options">
                <button class="btn btn-primary" name="generateLevels">
                    Generate Levels
                </button>
            </form>';

        public $table = '
            {#if rows}
            <hr />
                <table class="table table-condensed table-striped table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Name</th>
                            <th>EXP</th>
                            <th>Exp Needed</th>
                            <th>Money</th>
                            <th>Bullets</th>
                            <th>Health</th>
                        </tr>
                    </thead>
                    <tbody>
                        {#each rows}
                            <tr>
                                <td>{:level}</td>
                                <td>{:name}</td>
                                <td>{number_format :exp}</td>
                                <td>{number_format :diff}</td>
                                <td>${number_format :money}</td>
                                <td>{number_format :bullets}</td>
                                <td>{number_format :health}</td>
                            </tr>
                        {/each}
                    </tbody>
                </table>

            {/if}
        ';
    }

?>
