<?php
class logsTemplate extends template {

    public $main = '
    <table class="table table-condensed table-striped table-bordered table-responsive">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="100px">User</th>
                <th>Action</th>
                <th>Module</th>
                <th width="100px">date</th>
            </tr>
        </thead>
        <tbody>
            {#each log}
                <tr>
                    <td>{id}</td>
                    <td>{>userName}</td>
                    <td>{action}</td>
                    <td>{module}</td>
                    <td>{date}</td>
                </tr>
            {/each}
            {#unless log}
            <tr>
                <td colspan="3">No logs yet.</td>
            </tr>
            {/unless}
        </tbody>
    </table>
    ';

}
