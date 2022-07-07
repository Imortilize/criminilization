<?php

    class awardsTemplate extends template {
      
		public $awardMain = '
			{#if userAwards}
			<div class="panel panel-default">
				<div class="panel-heading">Your Awards</div>
				<div class="panel-body">
					<div class="row">
						{#each userAwards}	
						<div class="col-md-2 col-xs-3">
							<img src="/modules/installed/awards/images/{award.img}" title="{#if award.hidden}Hidden Award{/if}{#unless award.hidden}<{award.desc}>{/unless} [Awarded {time}]" class="img-responsive">
						</div>
						{/each}
					</div>
				</div>
			</div>
			{/if}
			<div class="panel panel-default">
				<div class="panel-heading">Awards</div>
				<div class="panel-body">
					{#each awards}
					{#unless hidden}
					<div class="award-container">
						<div class="panel panel-default">
							<div class="panel-heading">{name} 
							{#if completed}<span class="right"><i class="glyphicon glyphicon-ok"></i></span>{/if}
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-xs-12 col-sm-4">
										<img src="/modules/installed/awards/images/{img}" class="img-responsive">
									</div>
									<div class="col-xs-12 col-sm-8">
										<p class="award-desc"><{desc}></p>
										{#unless completed}
										{#if progress}
										<span class="progress-label">Progress</span>
										<div class="progress"> 
											<div class="progress-bar" role="progressbar" style="width: {progressperc}%;"></div> 
											<div class="progress-bar-title">{progress}</div> 
										</div>
										{/if}
										{/unless}
									</div>
								</div>
							</div>
						</div>
					</div>
					{/unless}
					{/each}
					{#unless awards}
					<p>There is currently no awards available.</p>
					{/unless}
				</div>
			</div>
		';
	  
		public $awardList = '
			<table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
						<th>Name</th>
						<th>Type</th>
						<th>Requirement</th>
						<th width="50px">Hidden</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each awards}
                        <tr>
                            <td>{name}</td>
							<td>{type}</td>
							<td>{number_format required}</td>
							<td class="text-center"><i class="glyphicon glyphicon-{#unless hidden}remove{/unless}{#if hidden}ok{/if}"></i></td>
                            <td>
                                [<a href="?page=admin&module=awards&action=editAward&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=awards&action=deleteAward&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
		';
		
        public $awardDelete = '
            <form method="post" action="?page=admin&module=awards&action=deleteAward&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this award?</p>
                    <p><em>"{name}"</em></p>
                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this award</button>
                </div>
            </form>
        ';
		
        public $awardForm = '
			{#if id}
			<form method="post" action="?page=admin&module=awards&action=editAward&id={id}" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="pull-left">Award Image</label>
							<img src="/modules/installed/awards/images/{img}">
						</div>
					</div>
                    <div class="col-md-6">
						<div class="form-group">
							<label>Upload Award Image</label>
							<input type="file" name="awardImg" class="form-control-file">
						</div>
						<div class="text-center">
							<button class="btn btn-default" type="submit" name="upload" value="1">Upload</button>
						</div>
					</div>
				</div>
			</form>
			<h4>Award Details</h4>
			<hr />
			{/if}
            <form method="post" action="?page=admin&module=awards&action={editType}Award&id={id}">
				<div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
							<label class="pull-left">Name</label>
							<input type="text" class="form-control" name="name" value="{name}">
						</div>
                    </div>
					<div class="col-md-6">
                        <div class="form-group">
							<label class="pull-left">Description</label>
							<input type="text" class="form-control" name="desc" value="{desc}">
						</div>
                    </div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="pull-left">Type</label>
							<select id="input_type" class="form-control" name="type">
								{#if type}
								<option value="{type}" selected>{type}</option>
								<option value="" disabled>--------------------</option>
								{/if}
								<option value="Money">Money</option>
								<option value="Bank">Bank</option>
								<option value="Bullets">Bullets</option>
								<option value="Points">Points</option>
								<option value="Rank">Rank</option>
								<option value="Gang">Gang</option>
								<option value="Crimes Done">Crimes Done</option>
								<option value="Crimes Fail">Crimes Fail</option>
								<option value="Crime Success">Crimes Success</option>
								<option value="Theft Done">Theft Done</option>
								<option value="Theft Fail">Theft Fail</option>
								<option value="Theft Success">Theft Success</option>
								<option value="Chase Done">Chase Done</option>
								<option value="Chase Fail">Chase Fail</option>
								<option value="Chase Success">Chase Success</option>
								<option value="Busts Done">Busts Done</option>
								<option value="Busts Fail">Busts Fail</option>
								<option value="Bust Success">Bust Success</option>
								<option value="Travel Total">Travel Total</option>
							</select>
						</div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Requirement</label>
                            <input type="number" min="0" class="form-control" name="required" value="{required}">
                        </div>
                    </div>
					<div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Hidden</label>
                            <select class="form-control" name="hidden">
								<option value="1" {#if hidden}selected{/if}>Hidden</option>
                                <option value="0" {#unless hidden}selected{/unless}>Visible</option>
                            </select>
                        </div>
                    </div>
				</div>
				<h4>Award Rewards</h4>
				<hr />
				<div class="row">	
					<div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Money</label>
                            <input type="number" min="0" class="form-control" name="rmoney" value="{rmoney}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Bullets</label>
                            <input type="number" min="0" class="form-control" name="rbullets" value="{rbullets}">
                        </div>
                    </div>	
					<div class="col-md-4">
						<div class="form-group">
							<label class="pull-left">Points</label>
							<input type="number" min="0" class="form-control" name="rpoints" value="{rpoints}">
						</div>
					</div>
                </div>
                <div class="text-center">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';
    }
?>