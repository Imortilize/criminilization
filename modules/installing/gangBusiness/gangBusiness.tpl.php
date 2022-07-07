<?php

   class gangBusinessTemplate extends template {

        public $buyOpts = '

            <form method="post" action="?page=gangBusiness&action=buy">
                <div class="panel panel-default">
                    <div class="panel-heading">Buy Businesses</div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">

                                {#unless buyOpts.users}
                                    <div class="text-center">
                                        <em>
                                            There are no members who can manage any businesses!
                                        </em>
                                    </div>
                                {/unless}
                                {#each buyOpts.users}
                                    <label class="option">
                                        <div class="crime-holder">
                                            <p>
                                                <span class="action">
                                                    <input type="radio" name="user" value="{user.id}" /"> {>userName} 
                                                </span> 

                                                <span class="cooldown">
                                                    {owned}/{max}
                                                </span> 

                                            </p>
                                        </div>
                                    </label>
                                {/each}
                            </div>
                            <div class="col-md-8">

                                {#each buyOpts.businesses}
                                    <label class="option">
                                        <div class="crime-holder">
                                            <p>
                                                <span class="action">
                                                    <input type="radio" name="business" value="{id}" /"> {name} 
                                                </span> 

                                                <span class="cooldown">
                                                    {#money payout} every {cooldown}
                                                </span> 
                                                
                                                <span class="commit">
                                                    {#money cost}
                                                </span> 
                                                
                                            </p>
                                        </div>
                                    </label>
                                {/each}
                            </div>
                        </div>
                        <div class="text-right">

                            <button class="btn btn-default" type="submit">
                                Buy
                            </button>

                        </div>
                    </div>
                </div>
            </form>
        ';

        public $businesses = '

            {>buyOpts}

            <div class="panel panel-default">
                <div class="panel-heading">{_setting "gangName"} Businesses</div>
                <div class="panel-body">

                    <p>
                        Your {_setting "gangName"} has given you {businessCount}/{max} businesses to look after!
                    </p> 

                    <hr />

                    {#each businesses}
                        <div class="crime-holder">
                            <p>
                                <span class="action">
                                    {name} 
                                </span> 
                                <span class="cooldown">
                                    {#money payout} in <span data-timer="{nextPayout}" data-timer-type="inline" class="timer-active"></span>
                                </span> 
                                <span class="commit">
                                    <a href="?page=gangBusiness&action=collect&id={id}">
                                        Collect
                                    </a>
                                </span>
                            </p>
                        </div>
                    {/each}
                    {#unless businesses}
                        <div class="text-center"><em>You are not in charge of any businesses yet!</em></div>
                    {/unless}
                </div>
            </div>
        ';
        

        public $businessesList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Business</th>
                        <th width="120px">Cost ($)</th>
                        <th width="120px">Payout ($)</th>
                        <th width="170px">Payout Time (Seconds)</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each businesses}
                        <tr>
                            <td>{name}</td>
                            <td>{#money cost}</td>
                            <td>{#money payout}</td>
                            <td>{payoutTime}</td>
                            <td>
                                [<a href="?page=admin&module=gangBusiness&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=gangBusiness&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $businessesDelete = '
            <form method="post" action="?page=admin&module=gangBusiness&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this business?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this business</button>
                </div>
            </form>
        
        ';
        public $businessesForm = '
            <form method="post" action="?page=admin&module=gangBusiness&action={editType}&id={id}" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="pull-left">Business Name</label>
                            <input type="text" class="form-control" name="name" value="{name}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Minimum gang level to buy</label>
                            <input type="number" class="form-control" name="rank" value="{rank}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Payout ($)</label>
                            <input type="number" class="form-control" name="payout" value="{payout}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Time to payout (seconds)</label>
                            <input type="number" class="form-control" name="payoutTime" value="{payoutTime}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Cost To Buy ($)</label>
                            <input type="number" class="form-control" name="cost" value="{cost}">
                        </div>
                    </div>
                </div>
                
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';
    }

?>
