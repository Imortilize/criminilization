<?php

/**
* This module gives the user new missions to do during the day
*
* @package Missions
* @author Chris Day
* @version 1.0.0
*/

function _missionProgress($m) {
	return round($m["progress"] / $m["count"] * 100);
}

new hook("actionMenu", function ($user) {

	global $page;

    if (!$user) return;

    $extra = false;

    $m = $user->info->US_task;

    if (strlen($m)) {
    	$extra = "[" . _missionProgress(json_decode($m, 1)) . "%]";
    }

    $timer = $user->getTimer("mission");

    $page->addToTemplate("mission_timer", $timer);
    $page->addToTemplate("mission_extra", $extra);


    return array(
        "url" => "?page=tasks", 
        "text" => "Daily Tasks", 
        "timer" => $timer,
        "templateTimer" => "mission_timer",
        "sort" => 600, 
        "extra" => $extra
    );
});

new hook("userAction", function ($action) {

	if (!$action["success"]) return;

	$user = new User($action["user"]);

	if (!strlen($user->info->US_task)) return;
	
	$mission = json_decode($user->info->US_task, 1);

	if ($mission["progress"] == $mission["count"]) return;

	if ($mission["type"] == $action["module"]) {

		switch ($action["module"]) {
			case "crimes";
			case "theft";

				if ($mission["id"]) {
					if ($mission["id"] == $action["id"]) {
						$mission["progress"]++;
					}
				} else {
					$mission["progress"]++;
				}
			break;
			case "chase";
				$mission["progress"]++;
			break;
		}

		$user->set("US_task", json_encode($mission));

	}

});