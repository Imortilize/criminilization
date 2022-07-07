<?php

/**
* This module allows people to get married
*
* @package Marriage
* @author Chris Day
* @version 1.0.0
*/


class marriage extends module {

	public $allowedMethods = array(
		"accept" => array("type"=>"GET"),
		"decline" => array("type"=>"GET"),
		"id" => array("type"=>"GET"),
		"user" => array("type"=>"POST"),
		"proposal" => array("type"=>"POST")
	);

	public function constructModule() {

		$settings = new Settings();

		$marriedTo = @new User($this->user->info->US_married);

		$this->html .= $this->page->buildElement("marriage", array(
			"married" => $this->user->info->US_married > 0,
			"user" => $marriedTo->user,
			"proposeCost" => $settings->loadSetting("proposeCost", 1, 25000),
			"proposeRefund" => $settings->loadSetting("proposeRefund", 1, 10000)
		));
	}

	public function method_divorce() {

		if (!$this->user->info->US_married) {
			return $this->error("You are not married!");
		}
		
		$user = new User($this->user->info->US_married);
		$code = md5($user->info->U_email . $this->user->info->U_email);

		if (isset($this->methodData->accept)) {
			
			if ($code != $this->methodData->accept) {
				return $this->error("Invalid request!");
			}
			
			$this->user->set("US_married", 0);
			$user->set("US_married", 0);

			$user->newNotification("You are now divorced!");
			$this->error("You are now divorced!", "success");

		} else if (isset($this->methodData->decline)) {

			if ($code != $this->methodData->decline) {
				return $this->error("Invalid request!");
			}

			$user->newNotification(
				$this->page->username($this->user) . " refused to divorce you!"
			);
			
			$this->error("You refused the divorced!", "success");

		} else {


			$user->newNotification($this->page->buildElement("divorce", array(
				"user" => $this->user->user,
				"code" => md5($this->user->info->U_email . $user->info->U_email)
			)));

			$this->error("You have sent a divorce request!", "success");

		}

	}

	public function method_accept() {

		$settings = new Settings();

		if ($this->user->info->US_married) {
			return $this->error("You are already married!");
		}

		if ($this->user->info->US_married < 0) {
			return $this->error("You have already proposed!");
		}

		$user = new User($this->methodData->id);

		if (@!$user->info->U_id) {
			return $this->error("This user does not exist!");
		}

		if ($user->info->US_married != 0-$this->user->id) {
			return $this->error("They have not proposed to you!");
		}

		$user->set("US_married", $this->user->id);
		$this->user->set("US_married", $user->info->U_id);

		$this->error("You are now married!", "success");
		$user->newNotification($this->page->username($this->user) . " accepted your hand in marriage!");

	}

	public function method_decline() {

		$settings = new Settings();

		$proposeRefund = $settings->loadSetting("proposeRefund", 1, 10000);

		if ($this->user->info->US_married) {
			return $this->error("You are already married!");
		}

		if ($this->user->info->US_married < 0) {
			return $this->error("You have already proposed!");
		}

		$user = new User($this->methodData->id);

		if (@!$user->info->U_id) {
			return $this->error("This user does not exist!");
		}

		if ($user->info->US_married != 0-$this->user->id) {
			return $this->error("They have not proposed to you!");
		}

		$user->set("US_money", $user->info->US_money + $proposeRefund);
		$user->set("US_married", 0);

		$this->error("You declined the proposal!", "info");
		$user->newNotification($this->page->username($this->user) . " declined your hand in marriage, you sold the ring for $".number_format($proposeRefund)."!");

	}

	public function method_propose() {

		$settings = new Settings();
		$proposeCost = $settings->loadSetting("proposeCost", 1, 25000);
		
		if ($proposeCost > $this->user->info->US_money) {
			return $this->error("You can't afford this!");
		}

		if ($this->user->info->US_married < 0) {
			return $this->error("You have already proposed!");
		}

		if ($this->user->info->US_married) {
			return $this->error("You are already married!");
		}

		$user = new User(null, $this->methodData->user);

		if (@!$user->info->U_id) {
			return $this->error("This user does not exist!");
		}

		if ($user->info->US_married < 0) {
			return $this->error("They have already proposed to someone!");
		}

		if ($user->info->U_id == $this->user->id) {
			return $this->error("You can't marry yourself!");
		}

		if ($user->info->US_married) {
			return $this->error("They are already married!");
		}

		$this->user->set("US_money", $this->user->info->US_money - $proposeCost);
		$this->user->set("US_married", 0-$user->info->U_id);

		$user->newNotification($this->page->buildElement("proposal", array(
			"user" => $this->user->user,
			"text" => $this->methodData->proposal
		)));
		
	}

}
