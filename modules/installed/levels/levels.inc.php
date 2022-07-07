<?php

	class levels extends module {
        
        public $allowedMethods = array("sortby"=>array("type"=>"GET"));
        
        public function constructModule() {

            $rank = (array) $this->user->getRank();
        	$nextRank = (array) $this->user->checkRank();

        	if (isset($nextRank["R_exp"])) {
                if ($rank["R_id"] == $nextRank["R_id"]) {
                    $nextRank = (array) $this->user->nextRank();
                }
                $expIntoNextRank =  $this->user->info->US_exp - $rank["R_exp"];
                $expNeededForNextRank = $nextRank["R_exp"] - $rank["R_exp"];
                $expperc = round($expIntoNextRank / $expNeededForNextRank * 100, 2);
                $maxRank = false;
            } else {
                $maxRank = true;
            	$expperc = 100;
            }

            if ($expperc > 100) $expperc = 100;

        	$this->html .= $this->page->buildElement("rankUp", array(
        		"rank" => $rank, 
        		"nextRank" => $nextRank, 
        		"expperc" => $expperc, 
        		"rankup" => $expperc == 100, 
        		"maxRank" => $maxRank
        	));

        }

        public function method_rankup() {
        	$old = $this->user->info->US_rank;
        	$this->user->rankUp();
        	$new = $this->user->info->US_rank;
        	
        	if ($old != $new) {
        		$this->error("You have ranked up!", "success");
        	}
        }

    }