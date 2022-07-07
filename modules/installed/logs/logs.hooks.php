<?php

new hook("casinoMenu", function ($user) {
    global $db;
    if ($user) {
        /* Delete old logs older than 10 days */
        $settings = new settings();
        $logsDeleteTime = $settings->loadSetting('deleteLogs', true, 10);
        $deleteLogs = $db->prepare("DELETE FROM logs where L_date < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL :time DAY))");
        $deleteLogs->bindParam(":time", $logsDeleteTime);
        $deleteLogs->execute();
    }
});

new Hook("clearRound", function () {
    global $db, $page;
    $db->delete("TRUNCATE TABLE logs;");
    $page->alert("logs cleared", "info");
});

function addLog($action, $mod) {
    global $user, $db;
    $insert = $db->insert("INSERT INTO logs (L_user, L_text, L_date, L_module) values (:user, :action, UNIX_TIMESTAMP(), :mod)", array(
        ":user" => $user->id,
        ":action" => $action,
        ":mod" => $mod
    ));
    if ($insert) return true;
    return false;
}

function userLog($user) {
    global $db;
    $query = $db->selectAll("SELECT * FROM logs where L_user = :user order by L_id desc limit 0,10", array(
        ":user" => $user
    ));
    $archive = array();
    foreach ($query as $row) {
        $archive[] = array(
            "action" => $row['L_text'],
            "module" => $row['L_module'],
            "date" => date("jS M H:i", $row["L_date"]),
        );
    }
    return $archive;
}

new Hook("alterModuleTemplate", function ($template) {
    global $user, $db;
    if (isset($_GET['view'])) $profile = new user($_GET['view']);

    if (!empty($profile->info) && $template["templateName"] == 'profile') {
        $template["items"]["staff"] = $user->hasAdminAccessTo('profile');
        $template["items"]["log"] = userLog($profile->id);
        $template["items"]["user"] = $profile->info;
        $template["html"] = $template["html"] . '
        {#if staff}
        <div class="panel panel-default">
            <div class="panel-heading">{user.U_name}\'s Last 10 Actions</div>
            <div class="panel-body p-0 table-responsive">
                <table class="table table-dark table-condensed table-striped m-0">
                    <thead>
                    <tr>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    {#each log}
                    <tr>
                        <td>{action}</td>
                        <td>{module}</td>
                        <td>{date}</td>
                    </tr>
                    {/each}
                    {#unless log}
                    <tr>
                        <td colspan="2">No logs yet.</td>
                    </tr>
                    {/unless}
                    </tbody>
                </table>
            </div>
        </div>
        {/if}
        ';
    }
    return $template;
});

new Hook("userAction", function ($data) {
    global $db, $user, $page;
    if($data['module'] == "register") {
        if($data['success'] == "true") {
			$user = new user($data['user']);
            addLog($user->info->U_name." has joined the game.", "register");
        }
    }
    if($data['module'] == "bank.sendMoney") {
        if($data['success'] == "true"){
            $user = new user($data['user']);
            addLog($user->info->U_name." sent ".$page->money($data['reward'])." to ".$user->info->U_name, "bank");
        }
    }
    if($data['module'] == "bank.withdraw") {
        if($data['success'] == "true"){
            $user = new user($data['user']);
            addLog($user->info->U_name." withdraw ".$page->money($data['reward']), "bank");
        }
    }
    if($data['module'] == "bank.deposit") {
        if($data['success'] == "true"){
            $user = new user($data['user']);
            addLog($user->info->U_name." deposited ".$page->money($data['reward']), "bank");
        }
    }
    if($data['module'] == "blackmarket.item") {
        addLog($user->info->U_name." bought ".$data['data']['name'], 'blackmarket');
    }
    if($data['module'] == "bounties.remove") {
        $user = new user($data['id']);
        addLog($user->info->U_name." removed from bounty list", 'bounties');
    }
    if($data['module'] == "bounties.add") {
        $user = new user($data['id']);
        addLog($user->info->U_name." added to bounty list", 'bounties');
    }
    if($data['module'] == "crimes") {
        if($data['success'] == "true"){
            addLog($user->info->U_name." has successed to commit crime ", 'crimes');
        } else {
            addLog($user->info->U_name." has failed to commit crime ", 'crimes');
        }
    }
    if($data['module'] == "detectives") {
        $user = new user($data['id']);
        addLog($user->info->U_name." hired detective to find ".$user->info->U_name, 'detectives');
    }
    if($data['module'] == "garage.sell") {
        addLog($user->info->U_name." sold cars for ".$this->money($data['reward']), 'garage');
    }
    if($data['module'] == "garage.repair") {
        addLog($user->info->U_name." repaired cars for ".$this->money($data['reward']), 'garage');
    }
    if($data['module'] == "jail") {
        if ($data['success'] == "true") {
            addLog($user->info->U_name." successed to broke someone from jail", 'jail');
        }else{
            addLog($user->info->U_name." failed to broke someone from jail", 'jail');
        }
    }
    if($data['module'] == "chase") {
        if ($data['success'] == "true") {
            addLog($user->info->U_name." won ".$page->money($data['reward'])." from chasing police", 'chase');
        }else{
            addLog($user->info->U_name." failed chasing police", 'chase');
        }
    }
    if($data['module'] == "property.drop") {
        addLog($user->info->U_name." dropped property", 'property');
    }
    if($data['module'] == "property.transfer") {
        addLog($user->info->U_name." transferred property", 'property');
    }
    if($data['module'] == "theft") {
        if($data['success'] == "true"){
            $car = $db->select("SELECT * FROM cars where CA_id = :id", array(":id" => $data['id']));
            addLog($user->info->U_name." has successed to steal ".$car['CA_name'], 'theft');
        } else {
            addLog($user->info->U_name." has failed to steal a car ", 'theft');
        }
    }
    if($data['module'] == "travel") {
        if($data['success'] == "true"){
            $location = $db->select("SELECT * FROM locations where L_id = :id", array(":id" => $data['id']));
            addLog($user->info->U_name." has traveled to ".$location['L_name'], 'travel');
        }
    }
    if($data['module'] == "logout") {
        if($data['success'] == "true"){
            addLog($user->info->U_name." logged out.", 'logout');
        }
    }
});
