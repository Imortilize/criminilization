<?php

    class escrowTemplate extends template {
    
        public $cancleNotification = '
            {>userName} has cancled their escrow invite! {#if return}Your items were returned to you.{/if}
        ';

        public $inviteNotification = '
            {>userName} has invited you to join their escrow! To accept or reject their offer click <a href="?page=escrow">here</a>.
        ';

        public $acceptNotification = '
            {>userName} has joined your escrow, click <a href="?page=escrow">here</a> to view the escrow.
        ';

        public $declineNotification = '
            {>userName} has declined your escrow request!.
        ';

        public $inviteUser = '
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Request Escrow
                        </div>
                        <div class="panel-body">
                            <p> 
                                Escrow is a method of exchanging items between players. This reduces the possiblity of scams. 
                            </p>
                            <p> 
                                Once both players have agreed on the exchange, the items are transferred simultaneously.
                            </p>

                            <form method="post" action="?page=escrow&action=invite">
                                <input type="text" class="form-control form-control-inline" name="user" placeholder="Username ... " />
                                <button class="btn btn-default">
                                    Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Pending Escrows
                        </div>
                        <div class="list-group text-left">
                            {#if user}
                                <div class="list-group-item">
                                    <strong>You</strong> invited {>userName}</strong>
                                    <a href="?page=escrow&action=cancle" class="pull-right">
                                        Cancle
                                    </a>
                                </div>
                            {/if}

                            {#each invites}
                                <div class="list-group-item">
                                    {>userName} invited you
                                    <a href="?page=escrow&action=decline&user={id}" class="pull-right">
                                        Decline
                                    </a>
                                    <span class="pull-right">&nbsp;&nbsp;</span>
                                    <a href="?page=escrow&action=accept&user={id}" class="pull-right">
                                        Accept
                                    </a>
                                </div>
                            {/each}

                            {#unless user}
                                {#unless invites}
                                    <div class="list-group-item text-center">
                                        <em>You have no pending requests</em>
                                    </div>
                                {/unless}
                            {/unless}

                        </div>
                    </div>
                </div>
            </div>
        
        ';

        public $escrow = '
            <div class="row">
                {#each users}
                    <div class="col-md-6">
                        {>items}
                    </div>
                {/each}
            </div>
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-8">
                    <div class="panel panel-default">
                        <div class="panel-heading">Actions</div>
                        <div class="panel-body text-right">
                            <a href="?page=escrow&action=cancle" class="btn btn-sm btn-danger pull-left">
                                Cancle Escrow
                            </a>
                            <a href="?page=escrow" class="btn btn-sm btn-info">
                                Reload
                            </a>
                            <a href="?page=escrow&action=addItem" class="btn btn-sm btn-success">
                                Add Item
                            </a>
                            <a href="?page=escrow&action=submit" class="btn btn-sm btn-warning">
                                Submit Offer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        ';

        public $submitOffer = '
            <div class="row">
                <div class="col-md-3">
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Submit Offer</div>
                        <div class="panel-body">
                            <p>
                                To submit your offer please enter your password!
                            </p>
                            <p>
                                If there are any changed to the offer you will have to re-submit your offer!
                            </p>


                            <form method="post" action="?page=escrow&action=submit">
                                <input type="password" class="form-control form-control-inline" name="password" placeholder="Password ... " />
                                <button class="btn btn-default">
                                    Submit
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        ';

        public $items = '
            <div class="panel panel-default">
                <div class="panel-heading">{>userName} Offers</div>
                <div class="panel-body">
                    {#if status}
                        <div class="alert alert-success">
                            {>userName} has submitted their offer
                        </div>
                    {/if}
                    {#unless status}
                        <div class="alert alert-info">
                            {>userName} is preparing their offer
                        </div>
                    {/unless}
                    {#each items}
                        <div class="crime-holder">
                            <p>
                                <span class="action">
                                    {#if isUser}
                                        {#if qty.qty}
                                            [ <a href="?page=escrow&action=remove&remove={type}&qty={qty.qty}&item={qty.item}">
                                                Remove
                                            </a> ]
                                        {else}
                                            [ <a href="?page=escrow&action=remove&remove={type}&qty={qty}">
                                                Remove
                                            </a> ]
                                        {/if}
                                    {/if}
                                    {name} 
                                </span>
                                <span class="cooldown" style="padding: 0px;">
                                    {info}
                                </span>
                            </p>
                        </div>
                    {/each}
                </div>
            </div>
        ';

        public $addItem = '
            <div class="panel panel-default">
                <div class="panel-heading">{>userName} Offers</div>
                <div class="panel-body text-left">
                    <{items}>
                </div>
            </div>
        ';
        
        public $listItem = '
            <select name="qty" class="form-control form-control-inline">
                {#each info.options}
                    <option value="{id}">{name}</option>
                {/each}
            </select> 
        ';

        public $item = '
            <div class="crime-holder escrow-item">
                <form action="?page=escrow&action=add" method="post"> 
                    <p> 
                        <span class="action"> {info.desc} </span>
                        <span class="cooldown"> 
                            <input type="hidden" name="type" value="{id}" />
                            {#if isListQty}
                                <input type="number" name="qty[qty]" class="form-control form-control-inline" placeholder="Qty." /> 
                                <select name="qty[item]" class="form-control form-control-inline">
                                    {#each info.options}
                                        <option value="{id}">{name}</option>
                                    {/each}
                                </select> 
                            {/if}
                            {#if isQty}
                                <input type="number" name="qty" class="form-control form-control-inline" placeholder="Qty." /> 
                            {/if}
                            {#if isList}
                                {>listItem}
                            {/if}
                        </span> 
                        <button name="item" value="2" class="btn btn-default"> 
                            Add 
                        </button> 
                    </p> 
                </form> 
            </div>
        ';

    }

?>