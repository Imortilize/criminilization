<?php

    class crackTheSafe extends module {

        public $allowedMethods = array(
            'pin'=>array('type'=>'post')
        );
        
        public $pageName = '';

        public $prizePerSecond = 15;

        private function updateSafe() {
        	$code = mt_rand(0, 9999);
        	$this->db->update("UPDATE locations SET L_safe = :newCode, L_safeOpened = UNIX_TIMESTAMP() WHERE L_id = :location", array(
        		":location" => $this->user->info->US_location,
        		":newCode" => $code
        	));
        	$this->db->update("
        		UPDATE userStats SET US_safeGuesses = '[]' WHERE US_location = :location
        	", array(
        		":location" => $this->user->info->US_location
        	));
        }

        private function getGuesses() {
        	if ($this->user->info->US_safeGuesses) {
        		return json_decode($this->user->info->US_safeGuesses, true);
        	} else {
        		return array();
        	}
        }

        public function method_guess() {
        	
        	if (!$this->user->checkTimer('crackTheSafe')) return;

        	$guesses = $this->getGuesses();

        	$location = $this->db->select("SELECT * FROM locations WHERE L_id = :location", array(
        		":location" => $this->user->info->US_location
        	));
 
        	$prize = (time() - $location["L_safeOpened"]) * $this->prizePerSecond;
        	$pin = $this->methodData->pin;

        	$guess = array(
        		"time" => time(), 
        		"pin" => $pin
        	);

        	if ($pin == $location["L_safe"]) {
        		$this->error("You got the PIN code correct, you took $".number_format($prize), "success");
        		$this->user->set("US_money", $this->user->info->US_money + $prize);
        		$this->updateSafe();
        	} else {
        		$add = "";
        		$rand = mt_rand(1, 4);
        		if ($rand == 1) {
        			if ($pin > $location["L_safe"]) {
        				$guess["lower"] = 1;
        				$add = ", try a lower number";
        			} else {
        				$guess["higher"] = 1;
        				$add = ", try a higher number";
        			}
        		} 
        		$this->error("You entered the pin code and it was incorrect".$add."!");

	        	array_unshift($guesses, $guess);
	        	$this->user->set("US_safeGuesses", json_encode(array_slice($guesses, 0, 10)));
        	}

            $this->user->updateTimer('crackTheSafe', 300, true);

        }

        public function constructModule() {

            if (!$this->user->checkTimer('crackTheSafe')) {
                $time = $this->user->getTimer('crackTheSafe');
                $crimeError = array(
                    "timer" => "crackTheSafe",
                    "text"=>'You can\'t try again until your timer is up!',
                    "time" => $this->user->getTimer("crackTheSafe")
                );
                $this->html .= $this->page->buildElement('timer', $crimeError);
            }

        	$location = $this->db->select("SELECT * FROM locations WHERE L_id = :location", array(
        		":location" => $this->user->info->US_location
        	));

        	if ($location["L_safe"] == -1) {
        		$this->updateSafe();
        	}

        	$guesses = $this->getGuesses();

        	$this->html .= $this->page->buildElement("safe", array(
        		"guesses" => $guesses,
        		"date" => $this->date($location["L_safeOpened"]), 
        		"prize" => (time() - $location["L_safeOpened"]) * $this->prizePerSecond
        	));

        }

    }