<?php

/**
* This module tracks users IP addresses
*
* @package IP Tracking
* @author Chris Day
* @version 1.0.0
*/



new hook("userInformation", function ($user) {

	global $page, $db;

	if ($user) {

		$ip = $_SERVER["REMOTE_ADDR"];

		$db->insert("
			INSERT INTO userIP (IP_u, IP_addr, IP_t) VALUES (
				:u, :ip, UNIX_TIMESTAMP()
			) ON DUPLICATE KEY UPDATE IP_t = UNIX_TIMESTAMP()
		", array(
			":u" => $user->id,
			":ip" => $ip
		));

	}

});
    new hook("adminWidget-table", function ($user) {
        
        global $db, $page;

        $ips = $db->selectAll("
            SELECT
                IP_addr as 'ip', 
                COUNT(DISTINCT U_email) as 'count'
            FROM userIP
            INNER JOIN users on (IP_u = U_id)
            WHERE IP_t > (UNIX_TIMESTAMP() - 86400)
            GROUP BY IP_addr
            ORDER BY count DESC
            LIMIT 0, 5
        ");

        $data = array();

        foreach ($ips as $ip) {
        	$data[] = array(
                "columns" => array(
                    array( "value" => $ip["ip"] ),
                    array( "value" => $ip["count"] ),
                    array( "value" => "<a href='?page=admin&module=IPTracking&action=logs&ip=".$ip["ip"]."'>View</a>" ),
                )
            );
        }

        return array(
            "size" => 4,
            "sort" => 30, 
            "title" => "Duplicate IPs",
            "type" => "table", 
            "header" => array(
                "columns" => array(
                    array( "name" => "IP Address"),
                    array( "name" => "Users in Last 24 Hours"),
                    array( "name" => "Action"),
                )
            ),
            "data" => $data
        );

    });