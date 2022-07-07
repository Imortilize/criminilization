<?php

/**
* This allows you to create polls for your users to vote on
*
* @package Polls
* @author Chris Day
* @version 1.0.0
*/


class polls extends module {

	public $allowedMethods = array(
		"poll" => array( "type" => "GET" ),
		"vote" => array( "type" => "GET" )
	);

	public function constructModule () {

		$polls = $this->db->selectAll("
			SELECT * 
			FROM poll
			LEFT OUTER JOIN pollVotes ON (PV_poll = P_id AND PV_user = :user) 
		", array(
			":user" => $this->user->id
		));

		foreach ($polls as $pollKey => $poll) {

			$options = explode(",", $poll["P_options"]);

			$voted = false;
			if (isset($options[$poll["PV_vote"]])) {
				$voted = $options[$poll["PV_vote"]];
			}

			$total = 0;
			foreach ($options as $optionKey => $option) {

				$votes = $this->db->select("
					SELECT COUNT(*) as 'count' FROM pollVotes 
					WHERE PV_poll = :poll AND PV_vote = :option
				", array(
					":poll" => $poll["P_id"], 
					":option" => $optionKey
				));

				$total += $votes["count"];

				$options[$optionKey] = array(
					"label" => $option, 
					"id" => $optionKey,
					"votes" => $votes["count"]
				);

			}

			foreach ($options as $optionKey => $option) {
				
				if ($option["votes"]) {
					$option["percent"] = $option["votes"] / $total * 100;
				} else {
					$option["percent"] = 0;
				}

				$options[$optionKey] = $option;
			}

			$polls[$pollKey] = array(
				"id" => $poll["P_id"], 
				"desc" => $poll["P_desc"], 
				"options" => $options,
				"voted" => $voted
			);

		}

		$this->html .= $this->page->buildElement("polls", array(
			"polls" => $polls
		));
	}

	function method_vote() {

		$poll = $this->db->select("
			SELECT * 
			FROM poll
			LEFT OUTER JOIN pollVotes ON (PV_poll = P_id AND PV_user = :user) 
			WHERE P_id = :id
		", array(
			":id" => $this->methodData->poll,
			":user" => $this->user->id
		));

		if (!$poll) {
			return $this->error("This poll does not exist!");
		}

		if (strlen($poll["PV_vote"])) {
			return $this->error("You have already voted on this poll!");
		}

		$this->db->insert("
			INSERT INTO pollVotes (PV_poll, PV_user, PV_vote) VALUES (:poll, :user, :vote);
		", array(
			":poll" => $this->methodData->poll,
			":vote" => $this->methodData->vote,
			":user" => $this->user->id
		));

		$this->error("You have voted!", "success");

	}

}
