<?php

   class theftTemplate extends template {


        public $theftSuccess = '
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">You stole a {name}</div>
                        <img class="img-responsive" src="modules/installed/cars/images/{id}.png" />
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Damage:</strong> {damage}%
                                </div>
                                <div class="col-md-6">
                                    <strong>Value:</strong> {#money value}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        ';

        public $theftHolder = '
            <div class="panel panel-default">
                <div class="panel-heading">Theft</div>
                <div class="panel-body">
                    {#each theft}
                        <div class="crime-holder">
                            <p>
                                <span class="action">
                                    {name} 
                                </span>
                                <span class="commit">
                                    <a href="?page=theft&action=commit&id={id}">Steal</a>
                                </span>
                            </p>
                            <div class="crime-perc">
                                <div class="perc" style="width:{percent}%;"></div>
                            </div>
                        </div>
                    {/each}
                </div>
            </div>
        ';
        

        public $theftList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Theft</th>
                        <th width="70px">Chance</th>
                        <th width="120px">Damage</th>
                        <th width="120px">Min car value</th>
                        <th width="120px">Max car value</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each theft}
                        <tr>
                            <td>{name}</td>
                            <td>{chance}%</td>
                            <td>{minDamage} to {maxDamage}%</td>
                            <td>${worstCar}</td>
                            <td>${bestCar}</td>
                            <td>
                                [<a href="?page=admin&module=theft&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=theft&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $theftDelete = '
            <form method="post" action="?page=admin&module=theft&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this theft?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this theft</button>
                </div>
            </form>
        
        ';
        public $theftForm = '
            <form method="post" action="?page=admin&module=theft&action={editType}&id={id}">
                <div class="form-group">
                    <label class="pull-left">Theft Name</label>
                    <input type="text" class="form-control" name="name" value="{name}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Chance (percent)</label>
                    <input type="number" class="form-control" name="chance" value="{chance}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Minimum car value for successful theft</label>
                    <input type="number" class="form-control" name="worstCar" value="{worstCar}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Maximum car value for successful theft</label>
                    <input type="number" class="form-control" name="bestCar" value="{bestCar}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Maximum damage of successful theft</label>
                    <input type="number" class="form-control" name="maxDamage" value="{maxDamage}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Minimum damage of successful theft</label>
                    <input type="number" class="form-control" name="minDamage" value="{minDamage}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Cooldown (seconds)</label>
                    <input type="number" class="form-control" name="cooldown" value="{cooldown}">
                </div>
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';
    }

?>
