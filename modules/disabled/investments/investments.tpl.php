<?php

    class investmentsTemplate extends template {
        
        
        public $investment = '
            {#unless cantBuy}
                <div class="panel panel-default weapon text-center">
                    <div class="panel-heading">
                        {name}
                    </div>
                    
                    <img class="img-responsive" src="modules/installed/investments/images/{id}.jpg" />

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                {#if cost} {#money cost} {/if}
                                {#if points}
                                    {#if cost} + {/if}
                                    {number_format points} {_setting "pointsName"}
                                {/if}
                            </div>
                            <div class="col-xs-12">
                                        {#if owned}
                                            <span class="btn btn-success btn-xs">Owned</a>
                                        {/if}
                                        {#unless owned}
                                            <a class="btn btn-default btn-xs" href="?page=investments&action=buy&investment={id}">
                                                Buy
                                            </a>
                                {/unless}
                            </div>
                        </div>
                    </div>
                </div>
            {/unless}
        ';

        public $investmentList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Investment</th>
                        <th width="150px">Payout</th>
                        <th width="120px">Max Investment</th>
                        <th width="170px">Time</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each investments}
                        <tr>
                            <td>{name}</td>
                            <td>{min}% -> {max}%</td>
                            <td>{#money maxInvest}</td>
                            <td>{_ago timeMinusNow}</td>
                            <td>
                                [<a href="?page=admin&module=investments&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=investments&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $investmentDelete = '
            <form method="post" action="?page=admin&module=investments&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this investment?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this investment</button>
                </div>
            </form>
        
        ';
        
        public $investmentForm = '
            <form method="post" action="?page=admin&module=investments&action={editType}&id={id}"  enctype="multipart/form-data">
                <div class="form-group">
                    <label class="pull-left">Investment Name</label>
                    <input type="text" class="form-control" name="name" value="{name}">
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Minimum Payout (%)</label>
                            <input type="number" class="form-control" name="min" value="{min}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Maximum Payout (%)</label>
                            <input type="number" class="form-control" name="max" value="{max}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Maximum Investment ($)</label>
                            <input type="text" class="form-control" name="maxInvest" value="{maxInvest}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Investment Time (Seconds)</label>
                            <input type="number" class="form-control" name="time" value="{time}">
                            <small>86,400 seconds in a day</small>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

        
        public $bank = '

            {#if investment}

                <div class="alert alert-info">
                    <p>
                        <strong>Current Investment!</strong>
                    </p>
                    <p>
                        You have invested {#money invested} in {investment.name}!
                    </p>
                    <p>
                        Time remaining: <span data-remove-when-done data-timer-type="inline" data-timer="{time}"></span>
                    </p>
                </div>

            {/if}


            <form action="?page=investments&action=invest" method="post">
                <div class="panel panel-default">
                    <div class="panel-heading">Investments</div>
                    <div class="panel-body">

                        <div class="crime-holder transparent">
                            <p> 
                                <span class="action">
                                    Investment
                                </span> 
                                <span class="cooldown min">
                                    Min/Max Payout
                                </span> 
                                <span class="cooldown time">
                                    Time
                                </span> 
                                <span class="cooldown maxInvest">
                                    Max. Investment
                                </span>
                            </p> 
                        </div>

                        {#each investments}
                            <div class="crime-holder">
                                <p> 
                                    <span class="action">
                                        <input type="radio" value="{id}" name="investment" /> {name}
                                    </span> 
                                    <span class="cooldown min">
                                        {min}%
                                    </span> 
                                    <span class="cooldown max">
                                        {max}%
                                    </span> 
                                    <span class="cooldown time">
                                        {_ago time}
                                    </span> 
                                    <span class="cooldown maxInvest">
                                        {#money maxInvest}
                                    </span>
                                </p> 
                            </div>
                        {/each}

                        {#unless investment}
                            <br />
                            <p class="text-left">
                                <strong>How much do you want to invest?</strong> <br />
                                <input type="number" class="form-control form-control-inline" name="invest" />
                                <button class="btn btn-default">
                                    Invest
                                </button>
                            </p>

                        {/unless}

                    </div>
                </div>
            </form>

   
        ';
        
    }

?>
