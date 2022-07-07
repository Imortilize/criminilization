<?php

    class kenoTemplate extends template {

        public $kenoTable = '

            {#if closed}
                <div class="alert alert-danger">
                    This property is currently closed!
                </div>
            {/if}

            <div class="panel panel-default keno-table max-width">
                <div class="panel-heading">{location} Keno</div>

                <table class="table table-condensed table-responsive table-bordered table-striped">
                    {#each rows}
                        <tr>
                            {#each cols}
                                <td class="{class} text-center">{num}</td>
                            {/each}
                        </tr>
                    {/each}
                </table>
            </div>
            <div class="panel panel-default keno-table-key max-width">
                <div class="panel-heading">Key</div>

                <table class="table table-condensed table-responsive table-bordered table-striped">
                    <tr>
                        <td width="20px" class="chosen">&nbsp;</td>
                        <td>Picked</td>
                        <td width="20px" class="selected">&nbsp;</td>
                        <td>Drawn Number</td>
                        <td width="20px" class="selected chosen">&nbsp;</td>
                        <td>Correct Pick</td>
                </table>
            </div>
            {#unless closed}
                <div class="panel panel-default keno max-width">
                    <div class="panel-heading">Actions</div>
                    <div class="panel-body">

                        <form method="post" action="?page=keno&action=bet">
                            <input type="hidden" name="numbers" value="{numbers}" />
                            <input class="form-control form-control-inline bet" name="bet" value="{bet}" placeholder="Bet" />
                            <button class="btn btn-success">
                                Bet
                            </button>
                            <a class="btn btn-danger random">
                                Randomize
                            </a>
                        </form>
                        <hr />
                        <small> Min: $100 Max: {#money maxBet}</small> <br />
                        <small>{>propertyOwnership}</small>
                    </div>
                </div>
            {/unless}
        ';

    }

?>
