<?php

    class russianRouletteTemplate extends template {

        public $options = '

            <form method="post" action="?page=admin&module=russianRoulette&action=options">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Prize per rank ($)</label>
                            <input type="number" class="form-control" name="russianRoulettePrize" value="{russianRoulettePrize}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Minimum rank to play</label>
                            <select class="form-control" name="russianRouletteRank" data-value="{russianRouletteRank}" />
                                {#each ranks}
                                    <option value="{id}">
                                        {name}
                                    </option>
                                {/each}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>

            </form>
        ';

        public $russianRoulette = '
            <div class="panel panel-default">
                <div class="panel-heading">
                    Russian Roulette
                </div>
                <div class="panel-body text-center">

                    <p>
                        To play russian roulette you must be a {rank} or higher!
                    </p>
                    <p>
                        You will have a 1 in 6 chance of killing yourself but you also have a 5/6 chance of winning ${number_format prize}!
                    </p>

                    <p>
                        <form method="POST" action="?page=russianRoulette&action=pull">
                            <button name="csfr" value="{csfr}" class="btn btn-danger">
                                Pull The Trigger
                            </button>
                        </form>
                    </p>

                </div>
            </div>
        ';
        
    }

?>