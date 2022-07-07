<?php

    class rouletteTemplate extends template {

        public $newBet = '
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-default roulette-table max-width">
                        <div class="panel-heading">Place a Bet</div>
                        <div class="panel-body place-bet">

                            <form action="#table" method="post">

                                Bet on {text}

                                <br />

                                <input type="number" name="bet" class="form-control form-control-inline" placeholder="Bet amount" value="{bet}" />

                                <button class="btn btn-success">
                                    Place Bet
                                </button>

                            </form>
                            <br />
                            <a href="?page=roulette" class="btn btn-danger">
                                Cancel
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        ';

        public $rouletteTable = '


            {#if closed}
                <div class="alert alert-danger">
                    This property is currently closed!
                </div>
            {/if}


            <div class="row" id="table">
                <div class="col-md-6">
                    <div class="panel panel-default roulette-table max-width">
                        <div class="panel-heading">{location} Roulette</div>
                        <div class="panel-body">
                            <table class="table table-condensed table-responsive table-bordered table-striped text-center">

                                {#each table.rows}
                                    <tr>
                                        {#each cols}
                                            <td id="{text}" {#if colspan}colspan="{colspan}"{/if} {#if rowspan}rowspan="{rowspan}"{/if} {#if class}class="{class}"{/if}>
                                                <a href="?page=roulette&action=bet&on={text}">
                                                {text}
                                                </a>
                                            </td>
                                        {/each}
                                    </tr>
                                {/each}
                            </table>

                            <div class="key">
                                <strong>Key</strong>

                                <div class="row">
                                    <div class="col-xs-4 has-bet">
                                        Has a bet
                                    </div>
                                    <div class="col-xs-4 bet-on">
                                        Placing a bet
                                    </div>
                                    <div class="col-xs-4 has-bet bet-on">
                                        Edit Bet
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <em>Click on the table to place a bet</em>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                    <div class="panel panel-default roulette bets">
                        <div class="panel-heading">Actions</div>
                        <div class="panel-body">

                            <form method="post" action="?page=roulette&action=bet">

                                <h3 class="text-center">Bets on the table</h3>

                                {#unless table.bets}
                                    <p class="text-center">
                                        <em>There are no bets on the table</em>
                                    </p>
                                {/unless}

                                <div class="current-bets">
                                    <table class="table table-condensed table-responsive table-bordered table-striped text-center">
                                    {#each table.bets}
                                        <tr>
                                            <td width="70px" class="bg-green {class}">
                                                <a href="?page=roulette&action=bet&on={text}">{text}</a>
                                            </td>
                                            <td class="text-left">
                                                &nbsp;{#money bet}

                                                {#if winnings}
                                                    {#if won}
                                                        <span class="pull-right won">
                                                            {#money winnings}&nbsp;
                                                        </span>
                                                    {/if}
                                                    {#unless won}
                                                        <span class="pull-right lost">
                                                            {#money bet}&nbsp;
                                                        </span>
                                                    {/unless}
                                                {/if}
                                            </td>
                                            <td class="text-center" width="40px">
                                                <a href="?page=roulette&action=bet&on={text}">Edit</a>
                                            </td>
                                            <td class="text-center" width="60px">
                                                <a href="?page=roulette&action=remove&on={text}">Remove</a>
                                            </td>
                                        </tr>
                                    {/each}
                                    </table>
                                </div>

                                {#if table.totalBet}
                                    <h4 class="text-center">
                                        Total Stake: {#money table.totalBet}
                                    </h4>
                                {/if}

                                {#unless closed}
                                    <a href="?page=roulette&action=spin" class="btn btn-default">
                                        Spin Roulette Wheel
                                    </a>
                                {/unless}
                                {#if table.bets}
                                    <a href="?page=roulette&action=reset" class="btn btn-danger">
                                        Reset Bets
                                    </a>
                                {/if}
                            </form>
                            <hr />
                            <small> Min: $100 Max: {#money maxBet}</small> <br />
                            <small>{>propertyOwnership}</small>
                        </div>
                    </div>
                </div>
            </div>

        ';

    }

?>
