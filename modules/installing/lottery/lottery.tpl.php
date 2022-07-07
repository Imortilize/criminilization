<?php

    class lotteryTemplate extends template {

         public $options = '

            <form method="post" action="#">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Cost of lottery ticket ($)</label>
                            <input type="text" class="form-control" name="lotteryCost" value="{lotteryCost}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Hour of day to draw lottery (1-24)</label>
                            <input type="text" class="form-control" name="lotteryTime" value="{lotteryTime}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Lottery Tax ($)</label>
                            <input type="text" class="form-control" name="lotteryTax" value="{lotteryTax}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Max Lottery Tickets</label>
                            <input type="text" class="form-control" name="lotteryMax" value="{lotteryMax}" />
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>

            </form>
        ';

        public $lottery = '

            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Next Lottery Draw
                        </div>
                        <div class="panel-body">

                            <h3><strong>Jackpot:</strong> {#money jackpot}</h3>

                            <p>
                                Next draw in <span data-reload-when-done data-timer-type="inline" data-timer="{nextDraw}"></span>
                            </p>
                            <hr />
                            <p>
                                <u>Previous Winner</u>
                            </p>

                            <small>
                                {#if prev}
                                    {>userName} won {#money prev}
                                {/if}
                                {#unless prev}
                                    No winner
                                {/unless}
                            </small>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Buy Tickets
                        </div>
                        <div class="panel-body">
                            <h4>You have {number_format tickets} tickets</h4>
                            <p>
                                <form action="?page=lottery&action=buy" method="post">
                                    <input class="form-control form-control-inline" type="number" placeholder="Qty. to buy" name="tickets" />
                                    <button class="btn btn-default">
                                        Buy 
                                    </button>
                                </form>
                            </p>
                            <p>
                                <small> {#money cost} per ticket</small>
                            </p>

                        </div>
                    </div>
                </div>
            </div>

        ';
    }

?>