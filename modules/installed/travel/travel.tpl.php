<?php

    class travelTemplate extends template {

        public $locationChanged = '
			<div class="main-panel-container-top-padded">
				<div class="main-panel">
					<div class="main-heading-background">
						<div class="main-panel-heading">Travel</div>
					</div>
					<!--<img class="img-responsive" src="modules/installed/crimes/images/{id}.png" />-->
					<div class="main-panel-body">
						<div class="location-changed-background">
							<div class="main-panel-message-holder">
                                <p class="location-changed-header-text">Travel</p>
                                <p class="location-changed-text">
                                    You successfully travelled to&nbsp;<span class="location-changed-city-text">{location}</span>&nbsp;and this cost&nbsp;<span class="location-changed-money-text">${number_format cost}</span>&nbsp;.
                                </p>
							</div>
						</div>
                        <div class="button-continue-background">
                            <div class="button-commit-holder">
                                <a class="btn" id="continue-btn" href="?page=travel">Continue</a>
                            </div>
                        </div>
					</div>
				</div>
			</div>
        ';

        public $locationHolder = '

        <div class="main-panel-container">
			<div class="main-panel">
				<div class="main-heading-background">
					<div class="main-panel-heading">Travel</div>    
                    <p class="main-panel-info">
                        <span>
                            You are currently in
                            <span class="main-panel-info-current-location">{currentLocation}</span>.
                            Your current vehicle is
                            <span class="main-panel-info-vehicle-name">{vehicleName}</span>
                            and you can travel
                            <span class="main-panel-info-vehicle-distance">{number_format vehicleDistance} Km</span>
                            with it.
                        </span>
                    </p>
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
                                            <div class="location-image-holder">
                                                <img src="modules/installed/travel/images/{id}.jpg" class="img-fluid location-image" alt="Responsive image">
                                            </div> 

                                            {#if canAffordToTravel}
                                                <div class="location-cost-text">
                                                    {#money cost}
                                                </div> 
                                            {/if}
                                            {#unless canAffordToTravel}
                                                <div class="location-cost-text-no-money">
                                                    {#money cost}
                                                </div> 
                                            {/unless}

                                            <div class="location-distance-text">
                                                {number_format distance} Km
                                            </div> 

                                            <div class="location-select">
                                                <input type="radio" class ="input" id="location{id}" name="location-select">
                                            </div> 
                                        </div>
                                    </div>
                                {/each} 

                                <div class="button-commit-background">
                                    <div class="button-commit-holder">
                                        {#if hasTravelCooldown}
                                            <a class="btn disabled" id="commit-btn" href="?page=travel&action=fly" enable-with-text-when-done="Travel" data-timer-type="inline" data-timer="{travelTime}"></a>
                                        {/if}
                                        {#unless hasTravelCooldown}
                                            <a class="btn" id="commit-btn" href="?page=travel&action=fly">Travel</a>
                                        {/unless}
                                    </div>
                                </div>
                            </div>

                            <div id="unreachable" class="tab-pane fade">
                                {#each unreachableLocations}
                                    <div class="location-holder-unreachable">
                                        <div class="location-holder-container">
                                            <div class="location-image-holder">
                                                <img src="modules/installed/travel/images/{id}.jpg" class="img-fluid location-image" alt="Responsive image">
                                            </div> 

                                            {#if canAffordToTravel}
                                                <div class="location-cost-text">
                                                    {#money cost}
                                                </div> 
                                            {/if}
                                            {#unless canAffordToTravel}
                                                <div class="location-cost-text-no-money">
                                                    {#money cost}
                                                </div> 
                                            {/unless}

                                            <div class="location-distance-text">
                                                {number_format distance} Km
                                            </div> 

                                            <div class="location-select">
                                                <input type="radio" class ="input" id="location{id}" name="location-select" disabled>
                                            </div> 
                                        </div>
                                    </div>
                                {/each} 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ';

        public $locationsList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Locations</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each locations}
                        <tr>
                            <td>{name}</td>
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
            <form method="post" action="?page=admin&module=travel&action={editType}&id={id}" enctype="multipart/form-data">
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
                    <label class="pull-left">Image <small>(Leave blank to stay the same)</small></label>
                    <input type="file" class="form-control" name="image" value="">
                </div> 

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';
    }
