<?php

/**
* This module gives the user new missions to do during the day
*
* @package Missions
* @author Chris Day
* @version 1.0.0
*/


class tasks extends module {

	public $allowedMethods = array(
		"top10"=>array("type"=>"GET")
	);

	public function getMissionDesc($mission) {

		if (!$mission) return "";

		switch ($mission["type"]) {
			case "theft": 
				$theft = "any car theft";
				if ($mission["id"]) {
					$theft = $this->db->select("SELECT * FROM theft WHERE T_id = :id", array(
						":id" => $mission["id"]
					))["T_name"];
					$theft = "'".$theft."'";
				}
				return "Successfully commit " . $theft ." " . $mission["count"] . " times!";
			break;
			case "crimes": 
				$crime = "any crime";
				if ($mission["id"]) {
					$crime = $this->db->select("SELECT * FROM crimes WHERE C_id = :id", array(
						":id" => $mission["id"]
					))["C_name"];
					$crime = "'".$crime."'";
				}
				return "Successfully commit " . $crime ." " . $mission["count"] . " times!";
			break;
			case "chase": 
				return "Evade police capture " . $mission["count"] . " times!";
			break;

		}

	}

	public function constructModule() {

		$mission = json_decode($this->user->info->US_task, 1);

		if (strlen($this->user->info->US_task) && $this->user->checkTimer('mission')) {
			$this->user->set("US_task", "");
			$this->error("Your mission expired!");
		}

		if ($mission) {
			$mission["done"] = $mission["progress"] == $mission["count"];
		}

		$this->html .= $this->page->buildElement("missions", array(
			"inMission" => strlen($this->user->info->US_task), 
			"desc" => $this->getMissionDesc($mission), 
			"mission" => $mission
		));
	}

	public function method_skip() {
		$this->user->set("US_task", "");
		$this->error("You skipped this mission!", "success");
	}

	public function method_claim() {

		$mission = $this->user->info->US_task;

		if (!strlen($mission)) {
			return $this->error("You are not in a mission!");
		}

		$mission = json_decode($mission, 1);

		if ($mission["progress"] != $mission["count"]) {
			return $this->error("You are not ready to claim a reward!");
		}

		$s = new Settings();

		$exp = mt_rand(
			$s->loadSetting("missionMinExp", 1, 10),
			$s->loadSetting("missionMaxExp", 1, 25)
		);

		$money = mt_rand(
			$s->loadSetting("missionMinMoney", 1, 5000),
			$s->loadSetting("missionMaxMoney", 1, 15000)
		);

		$bullets = mt_rand(
			$s->loadSetting("missionMinBullets", 1, 50),
			$s->loadSetting("missionMaxBullets", 1, 150)
		);

		$reward = array(
			"exp" => $exp,
			"money" => $money,
			"bullets" => $bullets
		);

		$this->error($this->page->buildElement("missionSuccess", $reward), "success");

		$this->user->set("US_money", $this->user->info->US_money + $reward["money"]);
		$this->user->set("US_exp", $this->user->info->US_exp + $reward["exp"]);
		$this->user->set("US_bullets", $this->user->info->US_bullets + $reward["bullets"]);

		$this->user->set("US_task", "");

	}

	public function method_start() {
		
		if (!$this->user->checkTimer('mission')) {

			$time = $this->user->getTimer('mission');
			$error = array(
				"timer" => "mission",
				"text"=>'You can\'t start another mission untill your timer is up!',
				"time" => $this->user->getTimer("mission")
			);
			$this->html .= $this->page->buildElement('timer', $error);
			return;
		}

		$mission = $this->user->info->US_task;

		if (strlen($mission)) {
			return $this->error("You are already in a mission!");
		}

		$type = mt_rand(0, 4);

		switch ($type) {
			case 0: 
				$mission = array(
					"type" => "crimes", 
					"count" => mt_rand(20, 50), 
					"id" => 0
				);
			break;
			case 1: 
				$crimes = $this->db->selectAll("SELECT * FROM crimes");
				$mission = array(
					"type" => "crimes", 
					"count" => mt_rand(5, 10), 
					"id" => $crimes[mt_rand(0, count($crimes)-1)]["C_id"]
				);
			break;
			case 2: 
				$mission = array(
					"type" => "theft", 
					"count" => mt_rand(10, 20), 
					"id" => 0
				);
			break;
			case 3: 
				$theft = $this->db->selectAll("SELECT * FROM theft");
				$mission = array(
					"type" => "theft", 
					"count" => mt_rand(1, 2), 
					"id" => $theft[mt_rand(0, count($theft)-1)]["T_id"]
				);
			break;
			case 4: 
				$mission = array(
					"type" => "chase", 
					"count" => mt_rand(1, 5),
					"id" => 0
				);
			break;
		}

		$mission["progress"] = 0;

		$this->user->set("US_task", json_encode($mission));
		$this->user->updateTimer("mission", 3600 * 8, 1);

	}

}
