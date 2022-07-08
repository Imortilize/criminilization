<?php

class vehiclesTemplate extends template {

    public $vehicles = '
    <div class="panel panel-default">
        <div class="panel-heading">Vehicles</div>
        <div class="panel-body">
            <table class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr>
                        <th class="text-center" width="35px">#</th>
                        <th>Name</th>
                        <th>Km/S</th>
                        <th>Distance Range (KM)</th>
                        <th>Fuel Cost</th>
                        <th>Price</th>
                        <th>Trade Units</th>
                        <th>Rank</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    {#each vehicles}
                        <tr>
                            <td class="text-center">{id}</td>
                            <td>{name}</td>
                            <td>{range}/s</td>
                            <td>{max}</td>
                            <td>{#money fuel}</td>
                            <td>{#money cost}</td>
                            <td>{units}</td>
                            <td>{rank}</td>
                            <td class="text-center">
                                {#unless own}<a href="?page=vehicles&action=buy&id={id}">Buy</a>{/unless}
                                {#if own}<span class="text-danger">OWNED</span>{/if}
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    </div>
    ';

    public $vehiclesList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>vehicles</th>
                        <th width="120px">Cost ($)</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each vehicles}
                        <tr>
                            <td>{name}</td>
                            <td>{#money cost}</td>
                            <td>
                                [<a href="?page=admin&module=vehicles&action=edit&id={id}">Edit</a>]
                                [<a href="?page=admin&module=vehicles&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

    public $vehiclesDelete = '
            <form method="post" action="?page=admin&module=travel&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this vehicle?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this vehicle</button>
                </div>
            </form>

        ';
    public $vehiclesForm = '
            <form method="post" action="?page=admin&module=vehicles&action={editType}&id={id}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Vehicle Name</label>
                            <input type="text" class="form-control" name="name" value="{name}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Vehicle Km/s</label>
                            <input type="number" class="form-control" name="range" value="{range}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Vehicle Range</label>
                            <input type="number" class="form-control" name="max" value="{max}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Vehicle Fuel</label>
                            <input type="number" class="form-control" name="fuel" value="{fuel}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Vehicle Price</label>
                            <input type="number" class="form-control" name="cost" value="{cost}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Vehicle Units</label>
                            <input type="number" class="form-control" name="units" value="{units}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Vehicle Rank</label>
                            <input type="number" class="form-control" name="rank" value="{rank}">
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

}
