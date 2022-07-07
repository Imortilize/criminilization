<?php

    class scavenge extends module {
        
        public $allowedMethods = array();
        
        public function constructModule() {

        	if ($this->user->checkTimer("scavenge")) {
	        	$settings = new Settings();

	        	$s = array(
	                "scavenge_rounds" => $settings->loadSetting("scavenge_rounds", true, 30),
	                "scavenge_chance_failed" => $settings->loadSetting("scavenge_chance_failed", true, 50),
	                "scavenge_chance_points" => $settings->loadSetting("scavenge_chance_points", true, 1),
	                "scavenge_chance_money" => $settings->loadSetting("scavenge_chance_money", true, 35),
	                "scavenge_chance_bullets" => $settings->loadSetting("scavenge_chance_bullets", true, 14),
	                "scavenge_reward_min_points" => $settings->loadSetting("scavenge_reward_min_points", true, 1),
	                "scavenge_reward_max_points" => $settings->loadSetting("scavenge_reward_max_points", true, 1),
	                "scavenge_reward_min_bullets" => $settings->loadSetting("scavenge_reward_min_bullets", true, 5),
	                "scavenge_reward_max_bullets" => $settings->loadSetting("scavenge_reward_max_bullets", true, 10),
	                "scavenge_reward_min_money" => $settings->loadSetting("scavenge_reward_min_money", true, 10),
	                "scavenge_reward_max_money" => $settings->loadSetting("scavenge_reward_max_money", true, 75), 
	                "pointsName" => $settings->loadSetting("pointsName")
	            );

	            $s["win_money"] = $s["scavenge_chance_failed"] + $s["scavenge_chance_money"];
	            $s["win_bullets"] = $s["win_money"] + $s["scavenge_chance_bullets"];
	            $s["win_points"] = $s["win_bullets"] + $s["scavenge_chance_points"];

	        	$bullets = 0;
	        	$money = 0;
	        	$points = 0;
	        	$nothing = 0;

	        	$actions = array();

	        	$i = 0;
	        	while ($i < $s["scavenge_rounds"]) {
	        		$spin = mt_rand(1, 100);

	        		if ($s["scavenge_chance_failed"] >= $spin) {
	        			$nothing++;
	        			$actions[] = array("class" => "danger", "text" => "You found nothing");

	        		} else if ($s["win_money"] > $spin) {
	        			$reward = mt_rand($s["scavenge_reward_min_money"], $s["scavenge_reward_max_money"]);
	        			$money += $reward;
	        			$actions[] = array("class" => "success", "text" => "You found " . $this->money($reward));

	        		} else if ($s["win_bullets"] >= $spin) {
	        			$reward =  mt_rand($s["scavenge_reward_min_bullets"], $s["scavenge_reward_max_bullets"]);
	        			$bullets += $reward;
	        			$actions[] = array("class" => "success", "text" => "You found " . number_format($reward) . " bullets");

	        		} else if ($s["win_points"] >= $spin) {
	        			$reward = mt_rand($s["scavenge_reward_min_points"], $s["scavenge_reward_max_points"]);
	        			$points += $reward;
	        			$actions[] = array("class" => "success", "text" => "You found " . number_format($reward) . " " . $s["pointsName"]);
	        		}

	        		$i++;
	        	}

	        	$this->error("After scavenging the streets you found ".$this->money($money).", ".number_format($bullets)." bullets and " . number_format($points) . " " . $s["pointsName"], "success");

	        	$this->html .= $this->page->buildElement("scavenge", array(
	        		"actions" => $actions
	        	));

	        	$this->user->set("US_money", $this->user->info->US_money + $money);
	        	$this->user->set("US_bullets", $this->user->info->US_bullets + $bullets);
	        	$this->user->set("US_points", $this->user->info->US_points + $points);
	        	$this->user->updateTimer("scavenge", 86400, true);

        	} else {
        		$this->error("You scavenge the streets but can't find anything!");
        	}


        }
    }

?>