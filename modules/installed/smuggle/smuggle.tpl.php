<?php

    class smuggleTemplate extends template {
        
        public $drugMarket = '

                <div class="panel panel-default">
                    <div class="panel-heading">
                        Trades
                    </div>


                    <div class="panel-body">
                        <p class="text-center">
                            <small>
                                You can smuggle a maximum of {number_format max} items each trip.
                            </small>
                        </p>
                    </div>

                </div>

                <div class="row">
                    {#each drugs}
                        {>drug}
                    {/each}
                </div>

        ';

        public $drug = '
            <div class="col-md-3">
                <form method="POST" action="?page=smuggle&action=process">
                    <div class="panel panel-default smuggle-item">
                        <div class="panel-heading text-left">
                            {name}
                            <span class="label label-success pull-right" data-owned="{owned}">
                                {owned} Owned
                            </span>
                        </div>

                        <div class="smuggle-image" style="background-image:url(\'modules/installed/smuggle/images/{id}.png\')">
                        </div>

                        <div class="panel-body">
                            <div class="col-md-12">
                                <p>
                                    <small>
                                        <strong>Price:</strong> {#money price} ea.
                                    </small>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="table-input" name="drug[ {id} ]" value="0" />
                            </div>
                            <div class="col-md-4">
                                <button name="type" value="buy" class="btn btn-xs btn-success btn-block text-center" style="margin-bottom: 5px !important;">
                                    Buy
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button name="type" value="sell" class="btn btn-xs btn-danger btn-block text-center">
                                    Sell
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        ';

        public $drugMarketGraph = '
            <div class="keys">{keys}</div>
            <div class="history">{history}</div>
            <div class="ct-chart"></div>
        ';  

        public $drugMarketList = '
            <table class="table table-condensed table-responsive table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Stock</th>
                        <th width="160px">Min.</th>
                        <th width="160px">Max.</th>
                        <th width="120px">Options</th>
                    </tr>
                </thead>
                <tbody>
                    {#each drugMarket}
                        <tr>
                            <td>{name}</td>
                            <td>{#money min}</td>
                            <td>{#money max}</td>
                            <td>
                                [<a href="?page=admin&module=smuggle&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=smuggle&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $drugMarketDelete = '
            <form method="post" action="?page=admin&module=smuggle&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this item?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this item</button>
                </div>
            </form>
        
        ';


        public $drugMarketNewForm = '
            <form method="post" action="?page=admin&module=smuggle&action={editType}&id={id}" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="pull-left">Item Name</label>
                            <input type="text" class="form-control" name="name" value="{name}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Image <small>(Leave blank to stay the same)</small></label>
                            <input type="file" class="form-control" name="image" value="">
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Minimum Price ($)</label>
                            <input type="text" class="form-control" name="min" value="{min}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Maximum Price ($)</label>
                            <input type="text" class="form-control" name="max" value="{max}">
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