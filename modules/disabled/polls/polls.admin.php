<?php

/**
* This allows you to create polls for your users to vote on
*
* @package Polls
* @author Chris Day
* @version 1.0.0
*/


class adminModule {

	private function getPolls($pollsID = "all") {
		if ($pollsID == "all") {
			$add = "";
		} else {
			$add = " WHERE P_id = :id";
		}

		$sql = "
		SELECT
			P_id as 'id',  
			P_desc as 'desc',
			P_options as 'options'
		FROM poll" . $add . "
		ORDER BY P_id DESC";

		if ($pollsID == "all") {
			return $this->db->selectAll($sql);
		} else {
			return $this->db->select($sql, array(
				":id" => $pollsID
			));
		}
	}

	private function validatePolls($polls) {
		$errors = array();

		if (strlen($polls["desc"]) < 3) {
			$errors[] = "Poll name is to short, this must be at least 5 characters";
		}

		if (explode(",", $polls["options"]) < 2) {
			$errors[] = "Please provide at least two options in the poll!";
		}


		return $errors;

	}

	public function method_new () {

		$polls = array();

		if (isset($this->methodData->submit)) {
			$polls = (array) $this->methodData;
			$errors = $this->validatePolls($polls);

			if (count($errors)) {
				foreach ($errors as $error) {
					$this->html .= $this->page->buildElement("error", array("text" => $error));
				}
			} else {
				$insert = $this->db->insert("
					INSERT INTO poll (P_desc, P_options)  VALUES (:desc, :options);
					", array(
						":desc" => $this->methodData->desc,
						":options" => $this->methodData->options
					));

				$this->html .= $this->page->buildElement("success", array(
					"text" => "This poll has been created"
				));

			}

		}

		$polls["editType"] = "new";
		$this->html .= $this->page->buildElement("pollsForm", $polls);
	}

	public function method_edit () {

		if (!isset($this->methodData->id)) {
			return $this->html = $this->page->buildElement("error", array("text" => "No poll ID specified"));
		}

		$polls = $this->getPolls($this->methodData->id);

		if (isset($this->methodData->submit)) {
			$polls = (array) $this->methodData;
			$errors = $this->validatePolls($polls);

			if (count($errors)) {
				foreach ($errors as $error) {
					$this->html .= $this->page->buildElement("error", array("text" => $error));
				}
			} else {
				$update = $this->db->update("
					UPDATE poll SET P_desc = :desc, P_options = :options WHERE P_id = :id
				", array(
						":desc" => $this->methodData->desc,
						":options" => $this->methodData->options,
						":id" => $this->methodData->id
					));

				$this->html .= $this->page->buildElement("success", array(
					"text" => "This poll has been updated"
				));

			}

		}

		$polls["editType"] = "edit";
		$this->html .= $this->page->buildElement("pollsForm", $polls);
	}

	public function method_delete () {

		if (!isset($this->methodData->id)) {
			return $this->html = $this->page->buildElement("error", array("text" => "No poll ID specified"));
		}

		$polls = $this->getPolls($this->methodData->id);

		if (!isset($polls["id"])) {
			return $this->html = $this->page->buildElement("error", array("text" => "This poll does not exist"));
		}

		if (isset($this->methodData->commit)) {
			$delete = $this->db->delete("
				DELETE FROM polls WHERE P_id = :id;
				", array(
					":id" => $this->methodData->id
				));
			$delete->execute();

			header("Location: ?page=admin&module=polls");

		}


		$this->html .= $this->page->buildElement("pollsDelete", $polls);
	}

	public function method_view () {

		$this->html .= $this->page->buildElement("pollsList", array(
			"polls" => $this->getPolls()
		));

	}

}
