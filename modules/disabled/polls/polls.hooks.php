<?php

/**
* This allows you to create polls for your users to vote on
*
* @package Polls
* @author Chris Day
* @version 1.0.0
*/


new hook("accountMenu", function ($user) {

    if (!$user) return;

	global $db;
	
	$count = 0;

	$polls = $db->selectAll("
		SELECT * 
		FROM poll
		LEFT OUTER JOIN pollVotes ON (PV_poll = P_id AND PV_user = :user)
	", array(
		":user" => $user->id
	));

	foreach ($polls as $poll) {
		if (strlen($poll["PV_vote"]) == 0) $count++;
	}
    
    return array(
        "url" => "?page=polls", 
        "text" => "Polling Station", 
        "sort" => -2,
        "extraID" => "mail", 
        "extra" => $count
    );
});
