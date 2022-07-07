<?php

    class skillsTemplate extends template {
	
       public $skillHolder = '
<form method="post" action="?page=skills&action=update">	
            <div class="row">
                <div class="col-md-7">
             		<div class="panel panel-default">
             			<div class="panel-heading">
                            Upgade Skills and Stats
             		</div>
				</div>
                <div class="panel-body">
                 <ul class="list-group">
             	<li class="list-group-item text-left">
						<strong>Skill Points Available</strong>
                        <span class="badge">{pointsLeft}</span>
                 </li>
				{#each skills}
             		<li class="list-group-item text-left">
						<strong class="{skillColor}">{safeName}</strong>
                        <span class="badge">{skillValue}</span>
					    <input name="txtData[]" type="number" class="form-control" aria-label="Amount" value="{value}">
						<input name="hiddenName[]" type="hidden" value="{safeName}">
                  	</li>
				{/each}
	             <li class="list-group-item text-left">
					<button class="btn btn-default" name="submit" type="submit" value="1">Update Stats/Skills</button>
                 </li>		
                 </ul>
				</div>
			</div>
		</div>
</form>	      	
';	

      

        public $skillsList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Stat/Skill</th>
                        <th width="70px">Default Value</th>
                        <th width="120px">Max Value</th>
                        <th width="120px">Upgradable</th>
                        <th width="120px">Hidden</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each skill}
                        <tr>
                            <td>{name}</td>
                            <td>{defaultValue}</td>
                            <td>{maxValue}</td>
                            <td>{canUpdate}</td>
                            <td>{isHidden}</td>
                            <td>
                                [<a href="?page=admin&module=skills&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=skills&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
		';

        public $skillDelete = '
            <form method="post" action="?page=admin&module=skills&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this stat or skill?</p>
                   <p>Deleting this Skill/Stat may cause other features and modules to stop working correctly.</p>
                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this stat or skill</button>
                </div>
            </form>
        
        ';

        public $skillForm = '
            <form method="post" action="?page=admin&module=skills&action={editType}&id={id}">
                <div class="form-group">
                    <label class="pull-left">Stat or Skill Name</label>
                    <input type="text" class="form-control" name="name" value="{name}">
                </div>

                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Max level for stat or skill</label>
                            <input type="number" class="form-control" name="maxValue" value="{maxValue}">
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Default level for stat or skill</label>
                            <input type="number" class="form-control" name="defaultValue" value="{defaultValue}">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Can users upgrade this stat or skill</label>
                            <select class="form-control" name="canUpdate">
                                <option {#if canUpdaten}selected{/if} value="n">No</option>
                                <option {#if canUpdatey}selected{/if} value="y">Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="pull-left">Hide this skill/stat from users?.</label>
                            <select class="form-control" name="isHidden">
                                <option {#if isHiddenn}selected{/if} value="n">No</option>
                                <option {#if isHiddeny}selected{/if} value="y">Yes</option>
                            </select>
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