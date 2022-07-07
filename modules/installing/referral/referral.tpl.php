<?php

    class referralTemplate extends template {

        public $referrals = '

            <div class="panel panel-default">
                <div class="panel-heading">Referrals</div>
                <div class="panel-body">
                    {#unless referrals}
                        <div class="text-center">
                            <em>You have not referred anyone yet</em>
                        </div>
                    {/unless}
                    {#each referrals}

                        <div class="crime-holder">
                            <p>
                                <span class="action">
                                    {>userName}                                    
                                </span>
                                <span class="cooldown">
                                    {rank}
                                </span>
                                <span class="commit">
                                    {signup}
                                </span>
                            </p>
                        </div>
                    {/each}
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Referral Link</div>
                <div class="panel-body">
                    <p>
                        To earn extra rewards use this link when inviting people
                    </p>
                    <p>
                        <em>{host}</em>
                    </p>
                </div>
            </div>


        ';

        public $referralList = '
            <table class="table table-condensed table-responsive table-striped table-bordered">
                <thead>
                    <tr>
                        <th width="180px">Date</th>
                        <th width="120px">User</th>
                        <th>Rank</th>
                        <th width="120">Referred By</th>
                    </tr>
                </thead>
                <tbody>
                    {#each referrals}
                        <tr>
                            <td>{date}</td>
                            <td>
                                <a href="?page=admin&module=users&action=edit&id={id}">{name}</a>
                            </td>
                            <td>{rank}</td>
                            <td>
                                <a href="?page=admin&module=users&action=edit&id={refID}">{refName}</a>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $rankList = '
            <table class="table table-condensed table-responsive table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th width="100px">EXP Needed</th>
                        <th width="80px">Cash</th>
                        <th width="80px">Bullets</th>
                        <th width="80px">Item</th>
                        <th width="80px">Points</th>
                        <th width="80px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {#each ranks}
                        <tr>
                            <td>{name}</td>
                            <td>{exp}</td>
                            <td>{#money cash}</td>
                            <td>{number_format bullets}</td>
                            <td>{itemName}</td>
                            <td>{number_format points}</td>
                            <td>
                                [<a href="?page=admin&module=referral&action=editRank&id={id}">Edit</a>] 
                                [<a href="?page=admin&module=referral&action=deleteRank&id={id}">Delete</a>]
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        ';

        public $rankForm = '
            <form method="post" action="?page=admin&module=referral&action={editType}Rank&id={id}">
                <div class="form-group col-md-3">
                    <label class="pull-left">Cash reward</label>
                    <input type="number" class="form-control" name="cash" value="{cash}">
                </div>
                <div class="form-group col-md-3">
                    <label class="pull-left">Bullets reward</label>
                    <input type="number" class="form-control" name="bullets" value="{bullets}">
                </div>
                <div class="form-group col-md-3">
                    <label class="pull-left">Item reward</label>
                    <input type="number" class="form-control" name="item" value="{item}">
                </div>
                <div class="form-group col-md-3">
                    <label class="pull-left">Points reward</label>
                    <input type="number" class="form-control" name="points" value="{points}">
                </div>
                <div class="text-right">
                    <button class="btn btn-default" name="submit" type="submit" value="1">Save</button>
                </div>
            </form>
        ';

    }

?>