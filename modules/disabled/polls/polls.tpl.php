<?php

/**
* This allows you to create polls for your users to vote on
*
* @package Polls
* @author Chris Day
* @version 1.0.0
*/

class pollsTemplate extends template {

	public $polls = '
		<div class="panel panel-default">
			<div class="panel-heading">
				Polling Station
			</div>
			<div class="panel-body text-left">
				{#each polls}

					<h4>{desc}</h4>

					<div class="list-group">
						{#each options}
							<div class="list-group-item">
								<div class="row">
									<div class="col-md-3">
										{label}
									</div>	
									{#if ../voted}
										<div class="col-md-9">
									{else}
										<div class="col-md-7">
									{/if}
										<div class="progress">
											<div class="progress-bar" style="width: {percent}%"></div>
											<small class="overlay">
												{votes} Vote(s)
											</small>
										</div>
									</div>
									{#unless ../voted}
										<div class="col-md-2">
											<a href="?page=polls&action=vote&poll={../id}&vote={id}" class="btn btn-info btn-block btn-xs">
												Vote
											</a>
										</div>
									{/unless}
								</div>
							</div>
						{/each}
					</div>

					{#if voted}
						<div class="alert alert-info alert-sm">
							You voted for {voted}!
						</div>
					{/if}

				{else}
					<p class="text-center">
						<em>There are no polls in progress</em>
					</p>
				{/each}
			</div>
		</div>
	';


        

        public $pollsList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Poll</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each polls}
                        <tr>
                            <td>{desc}</td>
                            <td>
                                [<a href="?page=admin&module=polls&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=polls&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $pollslete = '
            <form method="post" action="?page=admin&module=polls&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this poll?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this poll</button>
                </div>
            </form>
        
        ';
        public $pollsForm = '
            <form method="post" action="?page=admin&module=polls&action={editType}&id={id}">
                <div class="form-group">
                    <label class="pull-left">Poll Description</label>
                    <input type="text" class="form-control" name="desc" value="{desc}">
                </div>
                <div class="form-group">
                    <label class="pull-left">Options (option 1, option 2, option 3)</label>
                    <input type="text" class="form-control" name="options" value="{options}">
                </div>
                
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

}
