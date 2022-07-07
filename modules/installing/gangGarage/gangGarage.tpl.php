<?php

    class gangGarageTemplate extends template {
    
        public $garage = '

            <div class="panel panel-default">
                <div class="panel-heading">Park Car</div>
                <div class="panel-body">

                    <form method="post" action="?page=gangGarage&action=park">
                        
                        <select name="car" class="form-control form-control-inline">
                            {#each cars}
                                <option value="{id}">
                                    {name} - {location} - {damage}%
                                </option>
                            {/each}
                            {#unless cars}
                                <option disabled selected>
                                    You dont own any cars
                                </option>
                            {/unless}
                        </select>

                        <button type="submit" class="btn btn-default">
                            Park in garage
                        </button>

                    </form>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">{_setting "gangName"} Garage</div>
                <div class="panel-body">

                    <p>
                        Your {_setting "gangName"} garage has {usedSpace}/{number_format max} cars!
                    </p> 

                    {#unless gangCars}
                        <div class="text-center">
                            <em>Your garage is empty</em>
                        </div>
                    {/unless}
                    {#each gangCars}
                        <div class="crime-holder">
                            <p>
                                <span class="action">
                                    {name} ({#money value})
                                </span> 
                                <span class="cooldown">
                                    {location}
                                </span> 
                                {#if canTake}
                                    <span class="commit">
                                        <a href="?page=gangGarage&action=take&car={id}">
                                            Take
                                        </a>
                                    </span>
                                {/if}
                            </p>
                            <div class="crime-perc">
                                <div class="perc" style="width:{damage}%;"></div>
                            </div>
                        </div>
                    {/each}

                </div>
            </div>
        ';
        
    }

?>