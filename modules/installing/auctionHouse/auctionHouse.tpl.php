<?php

    class auctionHouseTemplate extends template {
        
        public $sellDurations = '
            <option value="1">1 Hour</option>
            <option value="2" selected>2 Hours</option>
            <option value="3">3 Hours</option>
            <option value="4">4 Hours</option>
            <option value="5">5 Hours</option>
            <option value="6">6 Hours</option>
            <option value="12">12 Hours</option>
            <option value="24">1 Day</option>
        ';

        public $sellQuantity = '
            <div class="panel panel-default">
                <div class="panel-heading">Start a Auction</div>
                <div class="panel-body">
                    <form method="post" action"#">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="id" placeholder="Quantity to auction" />
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="price" class="form-control" placeholder="Starting Price" />
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="buyNow" aria-label="...">
                                    </span>
                                    <input type="text" name="buyNowPrice" class="form-control" placeholder="Buy Now Price" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" name="length">
                                    {>sellDurations}
                                </select>
                            </div>
                        </div>

                        <br />

                        <div class="text-center">
                            <button class="btn btn-default">
                                Start Auction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        ';

        public $sellItem = '
            <div class="panel panel-default">
                <div class="panel-heading">Start a Auction</div>
                <div class="panel-body">
                    <form method="post" action"#">
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control" name="id">
                                    {#each items}
                                        <option value="{id}">{name}</option>
                                    {/each}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="price" class="form-control" placeholder="Starting Price" />
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="buyNow" aria-label="...">
                                    </span>
                                    <input type="text" name="buyNowPrice" class="form-control" placeholder="Buy Now Price" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" name="length">
                                    {>sellDurations}
                                </select>
                            </div>
                        </div>
                        <br />
                        <div class="text-center">
                            <button class="btn btn-default">
                                Start Auction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        ';

        public $auctionItem = '
            <form action="#" method="post">
                <div class="crime-holder text-left">
                    <div class="row">
                        <div class="col-md-5">
                            <p class="text-left">
                                {#unless item.name}
                                    <strong>{item}</strong>
                                {/unless}
                                {#if item.name}
                                    {#if item.extra}
                                        <span class="glyphicon glyphicon-map-marker" data-toggle="tooltip" data-placement="top" title="{item.extra}"></span>
                                    {/if} 
                                    <strong>{item.name}</strong>
                                {/if} <br />
                                <span title="{#if pp}{#money pp} each{/if}">
                                    {#money currentBid}
                                </span>
                                <span class="text-muted">Current Bid </span> <br />
                                {#if user}
                                    {>userName}
                                {/if}
                                {#unless user}
                                    No Bids
                                {/unless}

                            </p>
                        </div>
                        <div class="col-md-3">
                            <p>
                                <span data-timer="{end}" data-timer-type="inline">&nbsp;</span>
                            </p>
                        </div>
                        <div class="col-md-4">
                                {#if isOwner}
                                    <p class="text-left">
                                        <a href="?page=auctionHouse&action=remove&id={id}" class="btn btn-block btn-xs btn-danger">
                                            Remove
                                        </a>
                                    </p>
                                {/if}
                                
                                {#unless isOwner}
                                    {#if buyNow}
                                        <p class="small-padding">
                                            <a href="?page=auctionHouse&action=buy&auction={id}" class="btn btn-default btn-block btn-xs" title="{#if ppBuy}{#money ppBuy} each{/if}">
                                                Buy it Now {#money buyNow}
                                            </a>
                                        </p>
                                    {/if}

                                    <div class="row">
                                        <div class="col-md-8">
                                            <p class="small-padding">
                                                <input type="hidden" name="auction" value="{id}" />
                                                <input type="text" name="bid" class="form-control" />
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="small-padding">
                                                <button class="btn btn-default btn-block">
                                                    Bid
                                                </button>
                                            </p>
                                        </div>
                                    </div>
                                {/unless}
                        </div>
                    </div>
                </div> 
            </form>
        ';

        public $auctionItemOLD = '

            <form action="#" method="post">
                <div class="crime-holder">
                    <p>
                        <span class="action time">
                            <span data-timer="{end}" data-timer-type="inline">&nbsp;</span>
                        </span>
                        <span class="action seller">
                            {#unless item.name}
                                {item}
                            {/unless}
                            {#if item.name}
                                {item.name} 
                                {#if item.extra}
                                    <span class="game-icon game-icon-position-marker" title="{item.extra}"></span>
                                {/if} 
                            {/if}
                        </span> 
                        <span class="cooldown bidder ellipsis">
                            {#if user}
                                {>userName}
                            {/if}
                            {#unless user}
                                No Bids
                            {/unless}
                        </span> 
                        <span class="cooldown current-bid">
                            <span title="{#if pp}{#money pp} each{/if}">
                                {#money currentBid}
                            </span>
                        </span>
                        <span class="cooldown buttons">

                            {#if isOwner}
                                <a href="?page=auctionHouse&action=remove&id={id}" class="btn btn-xs btn-danger">
                                    Remove
                                </a>
                            {/if}
                            
                            {#unless isOwner}
                                {#if buyNow}
                                    <a href="?page=auctionHouse&action=buy&auction={id}" class="btn btn-default btn-xs" title="{#if ppBuy}{#money ppBuy} each{/if}">
                                        {#money buyNow}
                                    </a>
                                {/if}

                                <input type="hidden" name="auction" value="{id}" />
                                <input type="text" name="bid" class="form-control" />
                                <button class="btn btn-default btn-xs">
                                    Bid
                                </button>
                            {/unless}
                        </span>
                    </p>
                </div>
            </form>
        ';

        public $sellWhat = '
            <div class="panel panel-default">
                <div class="panel-heading">Start a Auction</div>
                <div class="panel-body">
                    <a class="btn btn-default" href="?page=auctionHouse&action=sell&type=bullets">
                        Bullets
                    </a>
                    <a class="btn btn-default" href="?page=auctionHouse&action=sell&type=points">
                        {_setting "pointsName"}
                    </a>
                    <a class="btn btn-default" href="?page=auctionHouse&action=sell&type=car">
                        Cars
                    </a>
                    <a class="btn btn-default" href="?page=auctionHouse&action=sell&type=property">
                        Properties
                    </a>
                    <a class="btn btn-default" href="?page=auctionHouse&action=sell&type=item">
                        Items
                    </a>
                </div>
            </div>
        ';

        public $auctions = '

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="pull-left">
                        Current Auctions
                    </div>

                    <small class="pull-right">

                        <a href="?page=auctionHouse">All</a> |
                        <a href="?page=auctionHouse&type=bullets">Bullets</a> |
                        <a href="?page=auctionHouse&type=points">{_setting "pointsName"}</a> |
                        <a href="?page=auctionHouse&type=car">Cars</a> |
                        <a href="?page=auctionHouse&type=property">Property</a> |
                        <a href="?page=auctionHouse&type=item">Items</a>
                        <a href="?page=auctionHouse&action=sell" class="btn btn-xs btn-success">Sell Something</a>

                    </small>
                </div>
                <div class="panel-body">


                    {#each auctions}
                        {>auctionItem}
                    {/each}

                    {#unless auctions}
                        <div class="crime-holder">
                            <p>
                                <span class="action">
                                    <em>There are no auctions</em>
                                </span>
                                <a class="commit" href="?page=auctionHouse&action=sell">Sell Something</a>
                            </p>
                        </div>
                    {/unless}
                    {#if auctions}
                        <div class="text-center">
                            <a class="btn btn-default" href="?page=auctionHouse&action=sell">Sell Something</a>
                        </div>
                    {/if}
                </div>
            </div>



        ';
        
    }

?>