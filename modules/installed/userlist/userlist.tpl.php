<?php
class userlistTemplate extends template {

    public $userlist = '
    <div class="panel panel-default">
        <div class="panel-heading">Users in {location}</div>
        <div class="panel-body">
            <table class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr>
                        <th class="text-center" width="35px">#</th>
                        <th>User</th>
                        <th>Rank</th>
                        <th>Protection</th>
                        <th>Active</th>
                        <th>{_setting "gangName"}</th>
                    </tr>
                </thead>
                <tbody>
                    {#each user}
                        <tr>
                            <td class="text-center">{number}</td>
                            <td>{>userName}</td>
                            <td>{rank}</td>
                            <td>-</td>
                            <td>{_ago active}</td>
                            <td>{family.name}</td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
        <nav>
            <ul class="pagination">
                {#each pages}
                    <li {#if active}class="active"{/if}><a href="?page=notifications&p={page}">{page}</a></li>
                {/each}
            </ul>
        </nav>
    </div>
    ';

}
