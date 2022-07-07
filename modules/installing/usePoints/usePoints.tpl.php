<?php

   class usePointsTemplate extends template {

        public $shop = '

            <div class="panel panel-default">
                <div class="panel-heading">{_setting "pointsName"} Store</div>
                <div class="panel-body">
                    {#each items}
                        <div class="crime-holder">
                            <form action="?page=usePoints&action=buy" method="post">
                                <p>
                                    <span class="action">
                                        {name} 
                                    </span> 
                                    <span class="cooldown">
                                        {number_format cost} {_setting "pointsName"}
                                    </span> 
                                    <span class="cooldown ">
                                        <input type="number" name="qty" class="form-control" placeholder="Qty." {#if max}max="{max}"{/if} />
                                    </span> 
                                    <button name="item" value="{id}" class="btn btn-default" href="?page=crimes&action=commit&crime={id}">
                                            Buy
                                    </button>
                                </p>
                            </form>
                        </div>
                    {/each}
                </div>
            </div>

            <form method="post" action="?page=usePoints&action=transfer">
                <div class="panel panel-default">
                    <div class="panel-heading">Transfer {_setting "pointsName"}</div>
                    <div class="panel-body">
                        <div class="row">

                            <div class="col-md-4">
                                <p>
                                <input type="text" class="form-control" name="user" placeholder="Username" />
                                </p>
                            </div>
        
                            <div class="col-md-4">
                                <p>
                                <input type="number" class="form-control" name="points" placeholder="{_setting "pointsName"} to transfer" />
                                </p>
                            </div>

                            <div class="col-md-4">
                                <p>
                                <input type="password" class="form-control" name="password" placeholder="Password" />
                                </p>
                            </div>

                        </div>
                        <div class="text-center">
                            <button class="btn btn-default" name="submit" type="submit" value="1">
                                Transfer
                            </button>
                        </div>
                    </div>
                    </div>
            </form>
        ';

        public $settings = '

            <form method="post" action="#">

                <h3>Items</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">{_setting "pointsName"} to buy $50,000</label>
                            <input type="text" class="form-control" name="pointsCashCost" value="{pointsCashCost}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">{_setting "pointsName"} to buy 250 bullets</label>
                            <input type="text" class="form-control" name="pointsBulletsCost" value="{pointsBulletsCost}" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">{_setting "pointsName"} to get full health</label>
                            <input type="text" class="form-control" name="pointsHealthCost" value="{pointsHealthCost}" />
                        </div>
                    </div>
                </div>
                <h3>Cars</h3>
                <div class="row">

                    {#each cars}

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="pull-left">{_setting "pointsName"} to get a {name}</label>
                                <input type="text" class="form-control" name="car-{id}" value="{cost}" />
                            </div>
                        </div>
                    {/each}

                </div>
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

    }
?>  