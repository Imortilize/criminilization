<?php

    new hook("moneyMenu", function ($user) {

    	if ($user && $user->checkTimer("investment")) {
    		if ($user->info->US_invest) {

    			$investment = $user->db->select("
    				SELECT * FROM investments WHERE IN_id = :id
    			", array(
    				":id" => $user->info->US_investment
    			));

    			$gain = mt_rand($investment["IN_min"], $investment["IN_max"]) / 100;

    			$money = $user->info->US_invest * (1 + ($gain / 100));

    			$user->newNotification("
    				<p>Your investment in " . $investment["IN_name"] . " matured!</p>
    				<p>You made a ".abs($gain)."% ".($gain>0?"profit":"loss").", ".$this->money($money)." was returned to you!</p>
    			");

    			$user->set("US_invest", 0);
    			$user->set("US_investment", 0);
    			$user->set("US_money", $user->info->US_money + $money);

    		}
    	}

        return array(
            "url" => "?page=investments", 
            "text" => "Investments"
        );
    });