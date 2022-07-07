<?php

    class dailyLoginBonusTemplate extends template {
	
public $dailyLoginCollect = '
  	
';	

      

public $dailyLoginList = '
<table class="table table-condensed table-striped table-bordered table-responsive">

<thead>
  <tr>
    <th width="120px">Days</th>
    <th>Reward</th>
    <th>Actions</th>
  </tr>
</thead>

<tbody>
{#each dailyLoginBonuses}
  <tr>
    <td>{days}</td>
    <td>{reward}</td>
    <td>
    [<a href="?page=admin&module=dailyLoginBonus&action=edit&id={id}">Edit</a>] 
    [<a href="?page=admin&module=dailyLoginBonus&action=delete&id={id}">Delete</a>]
    </td>
  </tr>
{/each}
</tbody>
</table>';

public $dailyLoginDelete = '
<form method="post" action="?page=admin&module=dailyLoginBonus&action=delete&id={id}&commit=1">
<div class="text-center">
<p> Are you sure you want to delete this daily login bonus?</p>
<p><em>"{name}"</em></p>
<button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this stat or skill</button>
</div>
</form>';


public $dailyLoginBonusForm = '
<form method="post" action="?page=admin&module=dailyLoginBonus&action={editType}&id={id}">

<div class="form-group">
  <label class="pull-left"># of Days to login in a row</label>
  <input type="number" class="form-control" name="days" value="{days}">
</div>

<div class="row">
  <div class="col-md-3">
    <div class="form-group">
    <label class="pull-left">Money Reward if any</label>
    <input type="number" class="form-control" name="money" value="{money}">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-3">
    <div class="form-group">
    <label class="pull-left">Bullets Reward if any</label>
    <input type="number" class="form-control" name="bullets" value="{bullets}">
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-3">
    <div class="form-group">
     <label class="pull-left">Car Reward if any.</label>
     <select class="form-control" name="carsID">
      <option value="0" {noCar}>None</option>
    {#each car}
      <option value="{id}" {carPicked}>{name}</option>
     {/each}
    </select>
    </div>
  </div>
</div>


<div class="text-right">
  <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
</div>

</form>';

      
}

?>