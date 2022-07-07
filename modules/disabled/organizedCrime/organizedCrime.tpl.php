<?php

   class organizedCrimeTemplate extends template {

        public $successNotification = '
            Your organized crime was successful, you managed to get away with:
            <ul>
                {#if money}<li>{#money money}</li>{/if}
                {#if bullets}<li>{number_format bullets} bullets</li>{/if}
                {#if exp}<li>{number_format exp}EXP</li>{/if}
            </ul>
        ';

        public $notification = '
            You have been invited to an organized crime by {username} 
            <a href="?page=organizedCrime" class="btn btn-default pull-right">
                View Invites
            </a>
        ';

        public $ocRoles = '

            <div class="panel panel-default">
                <div class="panel-heading">{typeName} in {locationName}</div>
                <div class="panel-body">
                    {#each members}
                        <div class="crime-holder">
                            <p>
                                <span class="action">
                                    {#if user}
                                        {>userName} 
                                    {/if}
                                    {#unless user}
                                        No one invited
                                    {/unless}
                                    
                                </span>
                                <span class="cooldown">
                                    {desc}
                                </span>
                                <span class="cooldown status">
                                    {#unless status.name}
                                        {status}
                                    {/unless}
                                    {#if status.name}
                                        {status.name}
                                    {/if}
                                </span>
                            </p>
                        </div>
                    {/each}


                    {#unless isLeader}
                        <a class="btn btn-danger" href="?page=organizedCrime&action=leave">
                            Leave OC
                        </a>
                    {/unless}
                </div>
            </div>

            

            {#unless thisUser.status}
                <div class="panel panel-default">
                    <div class="panel-heading">Select Item</div>
                    <div class="panel-body">
                        <form method="POST" action="?page=organizedCrime&action=roles">
                            <select class="form-control form-control-inline" name="item">
                                <option style="display: none" selected>What would you like to use?</option>
                                {#each thisUser.inventory}
                                    {#if car}
                                        <option value="{car}">{name}</option>
                                    {/if}
                                    {#unless car}
                                        <option value="{id}">{name} ({#money cost})</option>
                                    {/unless}
                                {/each}
                                {#unless thisUser.inventory}
                                    <option disabled>You dont have any items for this role</option>
                                {/unless}
                            </select> <button class="btn btn-default">Use</button>
                        </form>
                    </div>
                </div>
            {/unless}

            {#unless full}
                {#if isLeader}
                    <div class="panel panel-default">
                        <div class="panel-heading">Invite Users</div>
                        <div class="panel-body">
                            <form method="POST" action="?page=organizedCrime&action=roles">
                                <input type="text" name="user" class="form-control form-control-inline" placeholder="Username" />
                                <select class="form-control form-control-inline" name="role">
                                    {#each members}
                                        {#unless user}
                                            <option value="{role}">{desc}</option>
                                        {/unless}
                                    {/each}
                                </select>
                                <button class="btn btn-default">Invite</button>
                            </form>
                        </div>
                    </div>
                {/if}
            {/unless}

            {#if isLeader}

                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">Kick Members</div>
                            <div class="panel-body">
                                <form method="POST" action="?page=organizedCrime&action=kick">
                                    <select class="form-control form-control-inline" name="user">
                                        {#each members}
                                            {#unless leader}
                                                {#unless empty}
                                                    <option value="{user.id}">{user.name}</option>
                                                {/unless}
                                            {/unless}
                                        {/each}
                                    </select>
                                    <button class="btn btn-default">Kick</button>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">Actions</div>
                            <div class="panel-body">
                                {#if ready}
                                    <a href="?page=organizedCrime&action=commit" class="btn btn-success" style="margin-right: 30px;">Commit OC</a>
                                {/if}

                                <a href="?page=organizedCrime&action=disband" class="btn btn-danger">Disband OC</a>
                            </div>
                        </div>
                    </div>
                </div>

            {/if}
        ';

        public $startOC = '
            
            {#if invites}
                <div class="panel panel-default">
                    <div class="panel-heading">Organized Crime Invites</div>
                    <div class="panel-body">
                        {#each invites}
                            <div class="crime-holder">
                                <p>
                                    <span class="action">
                                        {>userName} invited you as a {roleName} for a {typeName} in {locationName} 
                                    </span>
                                    <span class="cooldown">
                                    </span>
                                    <span class="commit btn btn-xs btn-danger">
                                        <a href="?page=organizedCrime&action=invite&id=-{inviteID}">Decline</a>
                                    </span>
                                    <span class="commit btn btn-xs btn-success">
                                        <a href="?page=organizedCrime&action=invite&id={inviteID}">Accept</a>
                                    </span>
                                </p>
                            </div>
                        {/each}
                    </div>
                </div>
            {/if}

            <div class="panel panel-default">
                <div class="panel-heading">Start an Organized Crime</div>
                <div class="panel-body">
                    {#each types}
                        <div class="crime-holder">
                            <p>
                                <span class="action">
                                    {name} 
                                </span>
                                <span class="cooldown">
                                    {#money cost}
                                </span>
                                <span class="cooldown">
                                    ({cooldown})
                                </span>
                                <span class="commit">
                                    <a href="?page=organizedCrime&action=plan&id={id}">Plan</a>
                                </span>
                            </p>
                        </div>
                    {/each}
                </div>
            </div>
        ';
        

        public $ocList = '
            <table class="table table-condensed table-striped table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>OC</th>
                        <th width="100px">Cost</th>
                        <th width="100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each oc}
                        <tr>
                            <td>{name}</td>
                            <td>{#money cost}</td>
                            <td>
                                [<a href="?page=admin&module=organizedCrime&action=edit&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=organizedCrime&action=delete&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $ocDelete = '
            <form method="post" action="?page=admin&module=organizedCrime&action=delete&id={id}&commit=1">
                <div class="text-center">
                    <p> Are you sure you want to delete this oc?</p>

                    <p><em>"{name}"</em></p>

                    <button class="btn btn-danger" name="submit" type="submit" value="1">Yes delete this oc</button>
                </div>
            </form>
        
        ';
        
        public $loot = '
            <tr>
                <td>
                    {name}
                </td>
                <td>
                    <input name="item[]" type="hidden" value="{id}" />
                    <input name="qty[]" type="number" class="form-control" value="{qty}" onInput="updateChance()" />
                </td>
                <td>
                    0%
                </td>
            </tr>
        ';

        public $ocForm = '
            {>javascript}
            <form method="post" action="?page=admin&module=organizedCrime&action={editType}&id={id}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="pull-left">OC Name</label>
                            <input type="text" class="form-control" name="name" value="{name}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Cooldown (seconds)</label>
                            <input type="number" class="form-control" name="cooldown" min="1" value="{cooldown}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Planning Cost ($)</label>
                            <input type="number" class="form-control" name="cost" min="1" value="{cost}" required />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">EXP (success)</label>
                            <input type="number" class="form-control" name="successEXP" value="{successEXP}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">EXP (failed)</label>
                            <input type="number" class="form-control" name="failedEXP" value="{failedEXP}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Min Cash Per Person ($)</label>
                            <input type="number" class="form-control" name="minCash" value="{minCash}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Max Cash Per Person ($)</label>
                            <input type="number" class="form-control" name="maxCash" value="{maxCash}" required />
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

        public $javascript = '
            <script type="application/javascript" src="modules/installed/oc/oc.admin.scripts.js"></script>
        ';

        public $options = '

            <form method="post" action="?page=admin&module=organizedCrime&action=options">

                <h3>Weapons</h3>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 1 Name</label>
                            <input type="text" class="form-control" name="ocWep1Name" value="{ocWep1Name}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 2 Name</label>
                            <input type="text" class="form-control" name="ocWep2Name" value="{ocWep2Name}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 3 Name</label>
                            <input type="text" class="form-control" name="ocWep3Name" value="{ocWep3Name}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 4 Name</label>
                            <input type="text" class="form-control" name="ocWep4Name" value="{ocWep4Name}" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 1 Cost ($)</label>
                            <input type="number" class="form-control" name="ocWep1Cost" value="{ocWep1Cost}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 2 Cost ($)</label>
                            <input type="number" class="form-control" name="ocWep2Cost" value="{ocWep2Cost}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 3 Cost ($)</label>
                            <input type="number" class="form-control" name="ocWep3Cost" value="{ocWep3Cost}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 4 Cost ($)</label>
                            <input type="number" class="form-control" name="ocWep4Cost" value="{ocWep4Cost}" />
                        </div>
                    </div>
                </div>
                
                <h3>Explosives</h3>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 1 Name</label>
                            <input type="text" class="form-control" name="ocExp1Name" value="{ocExp1Name}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 2 Name</label>
                            <input type="text" class="form-control" name="ocExp2Name" value="{ocExp2Name}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 3 Name</label>
                            <input type="text" class="form-control" name="ocExp3Name" value="{ocExp3Name}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 4 Name</label>
                            <input type="text" class="form-control" name="ocExp4Name" value="{ocExp4Name}" />
                        </div>
                    </div>
                </div>        
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 1 Cost ($)</label>
                            <input type="number" class="form-control" name="ocExp1Cost" value="{ocExp1Cost}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 2 Cost ($)</label>
                            <input type="number" class="form-control" name="ocExp2Cost" value="{ocExp2Cost}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 3 Cost ($)</label>
                            <input type="number" class="form-control" name="ocExp3Cost" value="{ocExp3Cost}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 4 Cost ($)</label>
                            <input type="number" class="form-control" name="ocExp4Cost" value="{ocExp4Cost}" />
                        </div>
                    </div>
                </div>

                <h3>Cars</h3>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 1 Minimum Cost ($)</label>
                            <input type="text" class="form-control" name="ocCar1level" value="{ocCar1level}" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 2 Minimum Cost ($)</label>
                            <input type="text" class="form-control" name="ocCar2level" value="{ocCar2level}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 3 Minimum Cost ($)</label>
                            <input type="text" class="form-control" name="ocCar3level" value="{ocCar3level}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="pull-left">Level 4 Minimum Cost ($)</label>
                            <input type="text" class="form-control" name="ocCar4level" value="{ocCar4level}" />
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
