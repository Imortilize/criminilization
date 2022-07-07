<?php

    class stocksTemplate extends template {

        public $stock = '
            <div class="col-md-6">
                <form method="POST" action="?page=stocks&action=process">
                    <div class="panel panel-default stock-item">
                        <div class="panel-heading text-left">
                            {name}
                            <span class="label label-success pull-right" data-owned="{owned}">
                                {owned} Owned
                            </span>
                        </div>

                        <div class="stock-chart">
                            <div data-chart="{history}" class="ct-chart chart-{id}"></div>
                        </div>

                        <div class="panel-body">
                            <div class="col-md-12">
                                <p>
                                    {#if price.goingUp}
                                        <i class="glyphicon glyphicon-chevron-up"></i>
                                    {/if}
                                    {#unless price.goingUp}
                                        <i class="glyphicon glyphicon-chevron-down"></i>
                                    {/unless}
                                    {#money price.value} ea. 
                                </p>
                            </div>
                            <div class="col-xs-4">
                                <input type="text" class="table-input" name="stock[ {id} ]" value="0" />
                            </div>
                            <div class="col-xs-4">
                                <button name="type" value="buy" class="btn btn-xs btn-success btn-block text-center" style="margin-bottom: 5px !important;">
                                    Buy
                                </button>
                            </div>
                            <div class="col-xs-4">
                                <button name="type" value="sell" class="btn btn-xs btn-danger btn-block text-center">
                                    Sell
                                </button>
                            </div>
                            <div class="col-xs-12 text-left">
                                
                                <small>
                                    {#money total}
                                </small>
                                <small class="pull-right">
                                    {#if owned}
                                        {#if profit.goingUp}
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        {/if}
                                        {#unless profit.goingUp}
                                            <i class="glyphicon glyphicon-chevron-down"></i>
                                        {/unless}
                                        {profit.value}%
                                    {/if}
                                    {#unless owned}
                                        <i class="glyphicon glyphicon-minus"></i>
                                        0%
                                    {/unless}
                                </small>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        ';
        


        public $stockMarket = '

            <div class="panel panel-default no-border">
                <div class="panel-heading">
                    Stock Markets
                </div>
                <div class="panel-body">
                    <small>
                        You can buy a maximum of {number_format max} stocks per company.
                    </small>
                </div>
            </div>

            <div class="row">
                {#each stocks}
                    {>stock}
                {/each}
            </div>
        ';
  

        public $stockMarketList = '
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
                    {#each stockMarket}
                        <tr>
                            <td>{name}</td>
                            <td>{#money min}</td>
                            <td>{#money max}</td>
                            <td>
                                [<a href="?page=admin&module=stocks&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=stocks&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $stockMarketDelete = '
            <form method="post" action="?page=admin&module=stocks&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this news post?</p>

                    <p><em>"{gntitle}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this news post</button>
                </div>
            </form>
        
        ';


        public $stockMarketNewForm = '
            <form method="post" action="?page=admin&module=stocks&action={editType}&id={id}">
                <div class="form-group">
                    <label class="pull-left">Stock Name</label>
                    <input type="text" class="form-control" name="name" value="{name}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Stock Description</label>
                    <textarea rows="8" type="text" class="form-control" name="desc">{desc}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-4 col-xs-4">
                        <div class="form-group">
                            <label class="pull-left">Minimum Price ($)</label>
                            <input type="text" class="form-control" name="min" value="{min}">
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-4">
                        <div class="form-group">
                            <label class="pull-left">Maximum Price ($)</label>
                            <input type="text" class="form-control" name="max" value="{max}">
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-4">
                        <div class="form-group">
                            <label class="pull-left">Volitility</label>
                            <input type="range" min="3" max="250" step="1" class="form-control" name="vol" value="{vol}">
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