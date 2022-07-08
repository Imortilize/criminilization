<?php

    class crimesTemplate extends template {

        public $crimeCommitted = '
			<div class="crime-panel-container-top-padded">
				<div class="crime-panel">
					<div class="crime-heading-background">
						<div class="crime-panel-heading">Crimes</div>
					</div>
					<!--<img class="img-responsive" src="modules/installed/crimes/images/{id}.png" />-->
					<div class="crime-panel-body">
						<div class="crime-committed-background">
							<div class="crime-panel-message-holder">
								{#if success}
									<p class="crime-committed-success-header-text">Success!</p>
									<p class="crime-committed-text">
										{text1} <span class="crime-committed-money-text">${money}</span> {text2}
									</p>
								{/if}
								
								{#unless success}
									<p class="crime-committed-failure-header-text">Failure!</p>
									<p class="crime-committed-text">{text1}</p>
								{/unless}
							</div>
						</div>
					</div>
				</div>
			</div>
        ';
		
        public $crimeHolder = '

		<div class="crime-panel-container">
			<div class="crime-panel">
				<div class="crime-heading-background">
					<div class="crime-panel-heading">Crimes in {location}</div>
				</div>
				<div class="crime-panel-body">
					<div class="crime-panel-background">
						<div class="crime-header-holder">
							<p>
                                <span class="crime-indicator-header">
                                    Focus
								</span> 

								<span class="crime-header">
									Crime
								</span> 

								<span class="stat-distribution-header">
									Stats
								</span> 
								
								<!--<span class="bonus-distribution-header">
									Bonus
								</span> -->

								<span class="select-crime-header">
								</span> 
							</p>
						</div>

						{#each crimes}
							<div class="crime-holder">
								<div class="crime-holder-container">
 
                                    <div class="crime-indicator"">
                                        {#if statIndicator2Icon}
                                       <!-- <div class="crime-indicator-container" style="background-color:{bonusColour};border-color:{bonusColour};">-->
                                            <i class="fa {statIndicator1Icon} crime-indicator-icon-left" style="color:{statIndicator1Colour};"></i>
                                            <i class="fa {statIndicator2Icon} crime-indicator-icon-right" style="color:{statIndicator2Colour};"></i>
                                      <!--  </div>-->
                                        {/if}

                                        {#unless statIndicator2Icon}
                                            <i class="fa {statIndicator1Icon} crime-indicator-icon" style="color:{statIndicator1Colour};"></i>
                                        {/unless}
                                    </div> 

									<div class="crime-text">
										{name} 
									</div> 

									<div class="crime-stat-container">
                                        <div class="crime-bonus-icon" style="color:{bonusColour}">
											<i class="fa-solid {bonusIcon}"></i>
										</div> 
										<div class="crime-stat-internal-container">
                                            <div class="progress crime-stat-progress-bar-controller-override">
                                                <div class="progress-bar crime-stat-progress-bar-container" role="progressbar" style="background-color:{offColour};width:{offRatio}%">
                                                </div>
                                                <div class="progress-bar crime-stat-progress-bar-bonus-container" role="progressbar" style="background-color:{offColour};width:{bonusOffRatio}%">
                                                </div>
                                            </div>
											<div class="progress crime-stat-progress-bar-controller-override">
												<div class="progress-bar crime-stat-progress-bar-container" role="progressbar" style="background-color:{defColour};width:{defRatio}%">
												</div>
                                                <div class="progress-bar crime-stat-progress-bar-bonus-container" role="progressbar" style="background-color:{defColour};width:{bonusDefRatio}%">
                                                </div>
											</div>	
											<div class="progress crime-stat-progress-bar-controller-override">
												<div class="progress-bar crime-stat-progress-bar-container" role="progressbar" style="background-color:{stlColour};width:{stlRatio}%">
												</div>
                                                <div class="progress-bar crime-stat-progress-bar-bonus-container" role="progressbar" style="background-color:{stlColour};width:{bonusStlRatio}%">
                                                </div>
											</div>
										</div>
									</div>
															
									<!--<div class="crime-bonus-distro-container">
										<div class="crime-bonus-text" style="color:{bonusColour}">
											<i class="fa-solid {bonusIcon}"></i>
											{bonus} 
										</div> 
									</div> -->
										
									<div class="select">
										<input type="radio" class ="input" id="crime{id}" name="crime-select">
									</div> 
								</div>
							</div>
						{/each}
						{#unless crimes}
							<div class="text-center"><em>There are no crimes</em></div>
						{/unless}
					</div>
					<div class="crime-button-background">
						{#if crimes}
							<div class="crime-commit-holder">
								<a class="btn btn-crime-commit" id="crime-btn" href="?page=crimes&action=commit">Commit</a>
							</div>
						{/if}
					</div>
				</div>
			</div>
		</div>
        ';
		
		public $crimeCooldown = '
            <div class="crime-panel-container-top-padded">
				<div class="crime-panel">
					<div class="crime-heading-background">
						<div class="crime-panel-heading">{name}</div>
					</div>
					<!--<img class="img-responsive" src="modules/installed/crimes/images/{id}.png" />-->
					<div class="crime-panel-body">
						<div class="crime-cooldown-background">
							<div class="crime-panel-message-holder">
								<p class="crime-cooldown-header-text">{header}!</p>
								<p class="crime-cooldown-text">{text}</p>
							</div>
						</div>
					</div>
				</div>
            </div>
        ';

        public $crimeList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
						<th>Index</th>
                        <th>Crime</th>
                        <th width="120px">Cooldown</th>
                        <th width="120px">Reward</th>
                        <th width="70px">Level</th>
                        <th width="70px">EXP</th>
                        <th width="70px">Bonus</th>
						<th width="70px">Offence</th>
						<th width="70px">Defence</th>
						<th width="70px">Stealth</th>
						<th width="70px">Total</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each crimes}
                        <tr>
							<td align="right">{index}</td>
                            <td>{name}</td>
                            <td>{cooldown} seconds</td>
                            <td>${money} - ${maxMoney}</td>
                            <td>{level}</td>
                            <td>{exp}</td>
                            <td>{bonus}</td>
							<td>{offStats}</td>
							<td>{defStats}</td>
							<td>{stlStats}</td>
							<td>{adjustedTotalStats}</td>
                            <td>
                                [<a href="?page=admin&module=crimes&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=crimes&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $crimeDelete = '
            <form method="post" action="?page=admin&module=crimes&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this crime?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this crime</button>
                </div>
            </form>
        
        ';
        public $crimeForm = '
            <form method="post" action="?page=admin&module=crimes&action={editType}&id={id}" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group">
                            <label class="pull-left">Crime Name</label>
                            <input type="text" class="form-control" name="name" value="{name}">
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Image <small>(Leave blank to stay the same)</small></label>
                            <input type="file" class="form-control" name="image" value="">
                        </div> 
                    </div> 
                </div> 

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Min. money for successful crime</label>
                            <input type="number" class="form-control" name="money" value="{money}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Max. monney for successful crime</label>
                            <input type="number" class="form-control" name="maxMoney" value="{maxMoney}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Min. bullets for successful crime</label>
                            <input type="number" class="form-control" name="bullets" value="{bullets}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Max. bullets for successful crime</label>
                            <input type="number" class="form-control" name="maxBullets" value="{maxBullets}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Chance of success (%)</label>
                            <input type="number" class="form-control" name="chance" value="{chance}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Cooldown (Seconds)</label>
                            <input type="number" class="form-control" name="cooldown" value="{cooldown}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">EXP Gained</label>
                            <input type="number" class="form-control" name="exp" value="{exp}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Min user level to comit this crime</label>
                            <input type="number" class="form-control" name="level" value="{level}">
                        </div>
                    </div>
                </div>
				
				<div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Total Stats</label>
                            <input type="number" min="0" class="form-control" name="totalStats" value="{totalStats}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Offence %</label>
                            <input type="number" min="0" max="100" class="form-control" name="offRatio" value="{offRatio}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Defence %</label>
                            <input type="number" min="0" max="100" class="form-control" name="defRatio" value="{defRatio}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Stealth %</label>
                            <input type="number" min="0" max="100" class="form-control" name="stlRatio" value="{stlRatio}">
                        </div>
                    </div>
					 <div class="col-md-2">
                        <div class="form-group">
                            <label class="pull-left">Bonus</label>
                            <input type="text" class="form-control" name="bonus" value="{bonus}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="pull-left">Success Text</label>
                    <input type="text" class="form-control" name="successText" value="{successText}">
                </div>
				
				<div class="form-group">
                    <label class="pull-left">Success Text 2</label>
                    <input type="text" class="form-control" name="successText2" value="{successText2}">
                </div>

                <div class="form-group">
                    <label class="pull-left">Fail Text</label>
                    <input type="text" class="form-control" name="failText" value="{failText}">
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';
    }

?>