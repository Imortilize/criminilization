<?php

    class garageTemplate extends template {

         public $garageOptions = '

            <form method="post" action="?page=admin&module=garage&action=options">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="pull-left">Bullets per $1,000 of car value</label>
                            <input type="text" class="form-control" name="crushBullets" value="{crushBullets}" />
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>

            </form>
        ';
    
        public $garage = '
            <form action="#" method="post">
                <div class="panel panel-default">
                    <div class="panel-heading">Filters</div>
                    <div class="panel-body text-left">

                        <div class="row">
                            <div class="col-md-5">
                                <strong>Location</strong>
                                <select class="form-control" name="filterLocation">
                                    <option value="*">All</option>
                                    {#each locations}
                                        <option value="{id}" {#if selected}selected{/if}>{name}</option>
                                    {/each}
                                </select>
                            </div>
                            <div class="col-md-5">
                                <strong>Damage</strong>
                                <select class="form-control" name="filterDamage">
                                    <option value="*">All</option>
                                    {#each damageFilters}
                                        <option value="{id}" {#if selected}selected{/if}>{name}</option>
                                    {/each}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-default btn-block" name="action" value="filter">
                                    Apply Filters
                                </button> 
                            </div>
                        </div>

                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Garage</div>
                    <div class="panel-body garage-cars">
                        <div class="text-right">
                            <small> [<a href="#" class="select-all">Select All</a>] </small>
                        </div>
                        {#unless cars}
                            <div class="text-center">
                                <em>
                                    There are no cars here
                                </em>
                            </div>
                        {/unless}
                        {#each cars}
                            <div class="crime-holder">
                                <p>
                                    <span class="action">
                                        <input name="id[]" type="checkbox" value="{id}" /> 
                                        <img src="modules/installed/cars/images/{type}.png" class="img-thumbnail item" alt="{name}" title="{name}" />
                                        {name} 
                                    </span> 
                                    <span class="cooldown">
                                        {location}
                                    </span> 
                                    <span class="cooldown cost">
                                        ${value}
                                    </span>
                                </p>
                                <div class="crime-perc">
                                    <div class="perc" style="width:calc(100% - {damage});"></div>
                                </div>
                            </div>
                        {/each}
                        <div class="text-right">
                            <small> [<a href="#" class="select-all">Select All</a>] </small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">With Selected</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button class="btn btn-block btn-warning" name="action" value="sell">
                                            Sell
                                        </button> 
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-block btn-danger" name="action" value="crush">
                                            Crush
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-block btn-success" name="action" value="repair">
                                            Repair
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">Move Car</div>
                            <div class="panel-body">

                                <select class="form-control" name="location">
                                    {#each locations}
                                        <option value="{id}">{name} {#money cost}</option>
                                    {/each}
                                </select>

                                <button class="btn btn-block btn-info" name="action" value="move">Move</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        ';
        
    }

?>