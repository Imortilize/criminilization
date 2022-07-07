<?php

    class pointsTemplate extends template {
        
        public $packages = '
            <div class="panel panel-default"> 
                <div class="panel-heading">Purchase {_setting "pointsName"}</div> 
                <div class="panel-body"> 
                    {#unless packages}
                        <div class="text-center">
                            <em>There are no packages available</em>
                        </div>
                    {/unless}
                    {#each packages}
                        <div class="crime-holder"> 
                            <form class="paypal" action="modules/installed/points/payments.php" method="post" id="paypal_form">
                                <p> 
                                    <span class="action"> 
                                        {desc}
                                    </span> 
                                    <span class="cooldown"> 
                                        {number_format points} {_setting "pointsName"}
                                    </span>
                                    <span class="cooldown tag"> 
                                        {#if tag}
                                            {tag}
                                        {/if}
                                    </span> 
                                    <span class="cooldown buy-button"> 
                                        <a href="?page=theft&amp;action=commit&amp;id=1">
                                            
                                        </a> 

                                        <input type="hidden" name="cmd" value="_xclick" />
                                        <input type="hidden" name="no_note" value="1" />
                                        <input type="hidden" name="lc" value="UK" />
                                        <input type="hidden" name="first_name" value="{user.U_name}" />
                                        <input type="hidden" name="payer_email" value="{user.U_email}" />
                                        <input type="hidden" name="item_number" value="{id}" / >
                                        <input type="hidden" name="item_name" value="{desc}" / >
                                        <input type="hidden" name="amount" value="{decimalFormattedCost}" / >
                                        <input type="hidden" name="user_id" value="{user.U_id}" / >
                                        <input type="hidden" name="invoice" value="{user.U_id}-{time}" / >
                                        <input type="hidden" name="currency_code" value="{_setting "currency"}" / >
                                        <input type="submit" class="btn btn-default btn-sm btn-xs" name="submit" value="Buy {_setting "currencySymbol"}{formattedCost} {_setting "currency"}"/>
                                    </span> 
                                </p> 
                            </form>
                        </div> 
                    {/each}
                </div> 
            </div>';
        
        public $transactions = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th width="120px">Paid ({_setting "currencySymbol"}{_setting "currency"})</th>
                        <th width="120px">{_setting "pointsName"}</th>
                        <th width="120px">User</th>
                    </tr>
                </thead>
                <tbody>
                    {#each transactions}
                        <tr>
                            <td>{date}</td>
                            <td>{paid}</td>
                            <td>
                                {#if points}
                                    {number_format points}
                                {/if}
                                {#unless points}
                                    Package Removed
                                {/unless}
                            </td>
                            <td>
                                <a href="?page=admin&module=users&action=edit&id={uid}">{user}</a>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
            ';


        public $storeHolder = '
            {#each store}
            <div class="crime-holder">
                <p>
                    <span class="action">
                        {name} 
                    </span>
           </div>
           {/each}
        ';
        

        public $storeList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Package</th>
                        <th width="120px">Tag</th>
                        <th width="120px">Cost</th>
                        <th width="120px">{_setting "pointsName"}</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each store}
                        <tr>
                            <td>{desc}</td>
                            <td>{tag}</td>
                            <td>{_setting "currencySymbol"}{cost} {_setting "currency"}</td>
                            <td>{number_format points}</td>
                            <td>
                                [<a href="?page=admin&module=points&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=points&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $storeDelete = '
            <form method="post" action="?page=admin&module=points&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this package?</p>

                    <p><em>"{desc}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this package</button>
                </div>
            </form>
        
        ';
        public $storeForm = '
            <form method="post" action="?page=admin&module=points&action={editType}&id={id}">
                <div class="form-group">
                    <label class="pull-left">Package Name</label>
                    <input type="text" class="form-control" name="desc" value="{desc}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Tagline</label>
                    <input type="text" class="form-control" name="tag" value="{tag}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Cost Of Package ({_setting "currency"})</label>
                    <input type="text" class="form-control" name="cost" value="{cost}">
                </div>
                <div class="form-group">
                    <label class="pull-left">{_setting "pointsName"}</label>
                    <input type="number" class="form-control" name="points" value="{points}">
                </div>
                
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

        public $paypalSettings = '

            <form method="post" action="?page=admin&module=points&action=settings">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="pull-left">PayPal Email address</label>
                            <input type="text" class="form-control" name="paypalEmail" value="{paypalEmail}" />
                        </div>
                        <div class="form-group">
                            <label class="pull-left">Currency Code (e.g. GBP or USD)</label>
                            <input type="text" class="form-control" name="currency" value="{currency}" />
                        </div>
                        <div class="form-group">
                            <label class="pull-left">Currency Symbol</label>
                            <input type="text" class="form-control" name="currencySymbol" value="{currencySymbol}" />
                        </div>
                        <div class="form-group">
                            <label class="pull-left">Currency Decimal Seperator</label>
                            <input type="text" class="form-control" name="currencyDecimalSeperator" value="{currencyDecimalSeperator}" />
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