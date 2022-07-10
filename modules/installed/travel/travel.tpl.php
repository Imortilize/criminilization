<?php

    class travelTemplate extends template {

        public $locationHolder = '

        <div class="main-panel-container">
			<div class="main-panel">
				<div class="main-heading-background">
					<div class="main-panel-heading">Travel</div>        
				</div>

				<div class="main-panel-body">
					<div class="main-panel-background">
                        <div class="main-tabs">
                            <ul class="nav nav-tabs main-nav-tabs">
                                <li class="active"><a class="tab" data-toggle="tab" href="#reachable">Reachable</a></li>
                                <li><a class="tab" data-toggle="tab" href="#unreachable">Unreachable</a></li>
                            </ul>
                        </div>     
                        <div class="main-panel-header-holder">
							<p>
                                <span class="info-header-city">
                                    City
								</span> 

								<span class="info-header-cost">
									Cost
								</span> 

								<span class="info-header-distance">
									Distance
								</span> 

								<span class="info-header-select">
								</span> 
							</p>
						</div>

                        <div class="tab-content">
                            <div id="reachable" class="tab-pane fade in active">
                                {#each reachableLocations}
                                    <div class="location-holder">
                                        <div class="location-holder-container">
                                            <div class="location-text">
                                                {location} 
                                            </div> 

                                            <div class="location-cost-text">
                                                {#money cost}
                                            </div> 

                                            <div class="location-distance-text">
                                                {number_format distance} Km
                                            </div> 

                                            <div class="location-select">
                                                <input type="radio" class ="input" id="location{id}" name="location-select">
                                            </div> 
                                        </div>
                                    </div>
                                {/each} 
                            </div>

                            <div id="unreachable" class="tab-pane fade">
                                {#each unreachableLocations}
                                    <div class="location-holder">
                                        <div class="location-holder-container">
                                            <div class="location-text">
                                                {location} 
                                            </div> 

                                            <div class="location-cost-text">
                                                {#money cost}
                                            </div> 

                                            <div class="location-distance-text">
                                                {number_format distance} Km
                                            </div> 

                                            <div class="location-select">
                                                <input type="radio" class ="input" id="location{id}" name="location-select">
                                            </div> 
                                        </div>
                                    </div>
                                {/each} 
                            </div>
                        </div>
                    </div>
                    {#if locations}
                        <div class="button-commit-background">
                            <div class="button-commit-holder">
                                <a class="btn" id="commit-btn" href="?page=travel&action=fly">Travel</a>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
        ';

        public $locationsList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Locations</th>
                        <th width="120px">Cost ($)</th>
                        <th width="120px">Bullets</th>
                        <th width="120px">Cost per Bullet ($)</th>
                        <th width="120px">Cooldown (sec)</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each locations}
                        <tr>
                            <td>{name}</td>
                            <td>{#money cost}</td>
                            <td>{bullets}</td>
                            <td>${bulletCost}</td>
                            <td>{cooldown} seconds</td>
                            <td>
                                [<a href="?page=admin&module=travel&action=edit&id={id}">Edit</a>]
                                [<a href="?page=admin&module=travel&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $locationsDelete = '
            <form method="post" action="?page=admin&module=travel&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this location?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this location</button>
                </div>
            </form>

        ';
        public $locationsForm = '
            <form method="post" action="?page=admin&module=travel&action={editType}&id={id}">
                <div class="form-group">
                    <label class="pull-left">Location Name</label>
                    <input type="text" class="form-control" name="name" value="{name}">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Latitude</label>
                            <input type="text" class="form-control" name="latitude" value="{latitude}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">Longitude</label>
                            <input type="text" class="form-control" name="longitude" value="{longitude}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="pull-left">Cost ($)</label>
                    <input type="number" class="form-control" name="cost" value="{cost}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Bullets</label>
                    <input type="number" class="form-control" name="bullets" value="{bullets}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Bullet Cost ($)</label>
                    <input type="number" class="form-control" name="bulletCost" value="{bulletCost}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Cooldown (sec)</label>
                    <input type="number" class="form-control" name="cooldown" value="{cooldown}">
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

    }
