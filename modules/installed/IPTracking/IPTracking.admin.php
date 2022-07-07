<?php

/**
* This module tracks users IP addresses
*
* @package IP Tracking
* @author Chris Day
* @version 1.0.0
*/


class adminModule {

	public function method_users() {

		$IPs = $this->db->selectAll("
			SELECT 
				IP_addr as 'addr',
				COUNT(*) as 'users', 
				MAX(IP_t) as 'lastActive'
			FROM userIP 
			GROUP BY IP_addr
		");

        $this->html .= $this->page->buildElement("users", array(
        	"ips" => $IPs
        ));
	}

	public function method_delete() {
		$this->db->delete("
			DELETE FROM userIP WHERE IP_addr = :addr
		", array(
			":addr" => $this->methodData->ip
		));

		$this->method_users();

	}

	public function method_logs() {
        $this->construct = false;

        $sharedUsers = $this->db->selectAll("
            SELECT IP_u, IP_t, count(IP_addr) as 'count' FROM userIP WHERE IP_addr =:ip GROUP BY IP_u
        ", array(
            ":ip" => $this->methodData->ip
        ));

        $users = array();

        foreach ($sharedUsers as $key => $value) {
            $u = new User($value["IP_u"]);
            $users[] = array(
            	"user" => $u->user, 
            	"time" => $value["IP_t"],
            	"count" => $value["count"]
            );
        }

        $this->html .= $this->page->buildElement("ipLogs", array(
            "ip" => $this->methodData->ip,
            "users" => $users
        ));
    }

}
