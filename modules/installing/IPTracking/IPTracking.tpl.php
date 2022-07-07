<?php

/**
* This module tracks users IP addresses
*
* @package IP Tracking
* @author Chris Day
* @version 1.0.0
*/

class iPTrackingTemplate extends template {

	public $users = '

        <table class="table table-condensed table-responsive table-striped table-bordered">
            <thead>
                <tr>
                    <th width="180px">IP Address</th>
                    <th width="180px">Number of Users</th>
                    <th>Last Active</th>
                    <th width="180px">Actions</th>
                </tr>
            </thead>
            <tbody>
            	{#each ips}
            		<tr>
	            		<td>{addr}</td>
	            		<td>{users}</td>
	            		<td>{_ago lastActive} ago</td>
	            		<td>
	            			<a href="?page=admin&module=IPTracking&action=logs&ip={addr}">View</a>
	            			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	            			<a href="?page=admin&module=IPTracking&action=delete&ip={addr}">Delete IP</a>
	            		</td>
            		</tr>
            	{/each}
            </tbody>
        </table>
	';

	public $ipLogs = '
        <h3>Users using the IP address {ip}</h3>

        <table class="table table-condensed table-responsive table-striped table-bordered no-dt">
            <thead>
                <tr>
                    <th width="150px">Username</th>
                    <th>Last Used</th>
                </tr>
            </thead>
            <tbody>
                {#each users}
                    <tr>
                        <td>
                            <a href="?page=admin&module=users&action=edit&id={user.id}">
                                {user.name} 
                            </a>
                        </td>
                        <td>
                            {_ago time} ago
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    ';

}
