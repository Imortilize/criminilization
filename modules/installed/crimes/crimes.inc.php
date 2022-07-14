<?php

	require('crime_helper.php');

    class crimes extends module {
        
        public $allowedMethods = array('crime'=>array('type'=>'get'));
        
        public $pageName = 'Crimes';
        
        public function constructModule() {
            
			// Check crime timer and clock committing a crime until the timer is complete
            if (!$this->user->checkTimer('crime')) {
				
				if (count($this->alerts) == 0) {
					$crimeTimer = $this->user->getTimer('crime');
					$this->alerts[] = $this->page->buildElement('crimeCooldown', array(
						"name" => "Crimes",
						"header" => "Cooldown",
						"text" => "You must wait until you can commit another crime.",
						"crimeTimer" => $crimeTimer
					));
				}
				
				return;
            } 

			// No crime timer active, so show the crimes panel
			$crimes = $this->db->prepare("
				SELECT * FROM crimes INNER JOIN ranks ON (R_id = C_level) ORDER BY C_level ASC
			");
			$crimes->execute();

			$crimePercs = explode('-', $this->user->info->US_crimes);

			$crimeInfo = array();
			while ($crime = $crimes->fetchObject()) {

				$crimeID = $crime->C_id;

				// Calculate the crimes stat distributuon
				[$distributionStat1, 
					$distributionStat2, 
					$distributionStat1Colour,
					$distributionStat2Colour,
					$distributionStat1Icon, 
					$distributionStat2Icon, 
					$isMixed, 
					$isOffence,
					$isDefence,
					$isStealth,
					$isOffDef,
					$isOffStl, 
					$isDefStl,
					$offRatio,
					$defRatio,
					$stlRatio,
					$bonus,
					$bonusColour,
					$bonusIcon,
					$bonusOffPercentage,
					$bonusDefPercentage,
					$bonusStlPercentage] = calculateStatDistribution(
					$crime
				);

				// Grab the stat distros and determine the crime stat indicators


				// Create the crime info to display
				$crimeInfo[] = array(
					"name" => $crime->C_name,
					"rank" => $crime->R_name,
					"time" => $this->user->getTimer("crime-" . $crime->C_id),
					"locked" => $crime->R_exp > $this->user->info->US_exp,
					"cooldown" => $this->timeLeft($crime->C_cooldown),
					"percent" => $crime->C_chance, 
					"id" => $crimeID,
					"statIndicator1Icon" => $distributionStat1Icon,
					"statIndicator1Colour" => $distributionStat1Colour,
					"statIndicator2Icon" => $distributionStat2Icon,
					"statIndicator2Colour" => $distributionStat2Colour,
					"offRatio" => $offRatio,
					"defRatio" => $defRatio,
					"stlRatio" => $stlRatio,
					"offColour" => offenceColour,
					"defColour" => defenceColour,
					"stlColour" => stealthColour,
					"bonus" => $bonus,
					"bonusColour" => $bonusColour,
					"bonusIcon" => $bonusIcon,
					"bonusOffRatio" => $bonusOffPercentage,
					"bonusDefRatio" => $bonusDefPercentage,
					"bonusStlRatio" => $bonusStlPercentage
				);
			}

			$location = $this->user->getLocation();
			$this->html .= $this->page->buildElement('crimeHolder', array(
				"crimes" => $crimeInfo,
				"location" => $location
			));
        }
        
        public function method_commit() {
            
            $id = abs(intval($this->methodData->crime));
            
			// Check crime timer and clock committing a crime until the timer is complete
			if (!$this->user->checkTimer('crime')) {
				return;
			} else {
                
                $chance = mt_rand(1, 100);
                $jailChance = mt_rand(1, 3);
                $crimeID = $id;
                
				// Grab the crime info from the database
                $crime = $this->db->prepare("SELECT * FROM crimes INNER JOIN ranks ON (R_id = C_level) WHERE C_id = :crime");
                $crime->bindParam(':crime', $crimeID);
                $crime->execute();
                $crimeInfo = $crime->fetchObject();
             
				// Validate the crime
                if (!$crimeInfo){ 
                    return $this->error("This crime does not exist!"); 
                }

                if ($crimeInfo->R_exp > $this->user->info->US_exp){ 
                    return $this->error("This crime is locked!"); 
                }
                
				// Checl whether the crime has been commited successfully or not
                $userChance = $crimeInfo->C_chance;
                if ($chance > $userChance) {
					
					// Display the crime failed screen
                    $this->alerts[] = $this->page->buildElement('crimeCommitted', array(
						"id" => $crimeID, 
                        "name" => $crimeInfo->C_name,
						"success" => false,
						"text1" => $crimeInfo->C_failText,
                        "text2" => "",
						"money" => 0
                    ));
					
					// Update the jail timer using this bizarre random math logic,
					// TODO: implement better jail system
                    //$this->user->updateTimer('jail', (mt_rand(3, 9) * 5), true);
					
					// No idea what this value does right now?
                    //$add = 0;
     
					// Action event for failed crime
                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "crimes", 
                        "id" => $crimeID, 
                        "success" => false, 
                        "reward" => 0
                    );
                    $actionHook->run($action);

                } else {

                    $rewards = array();
					                
					// Calculate the cash reward
					$cashReward = mt_rand($crimeInfo->C_money, $crimeInfo->C_maxMoney);
                    if ($cashReward) {
						$cashReward = number_format($cashReward);
                    }
					
					// Calculate the stats to award
					[$offence, $defence, $stealth] = calculateStats(
						$crimeInfo->C_totalStats,
						$crimeInfo->C_offenceRatio,
						$crimeInfo->C_defenceRatio,
						$crimeInfo->C_stealthRatio,
						$crimeInfo->C_bonus
					);

					// Set the new user stats
                    $this->user->set("US_money", $this->user->info->US_money + $cashReward);

					$newUserXp = ($this->user->info->US_exp + $crimeInfo->C_exp);
					if ($newUserXp <= 2147483647) {
						$this->user->set("US_exp", $newUserXp);
					}
                    
					$this->user->set("US_offence", $this->user->info->US_offence + $offence);
					$this->user->set("US_defence", $this->user->info->US_defence + $defence);
					$this->user->set("US_stealth", $this->user->info->US_stealth + $stealth);
				
					// Action event for successful crime
                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "crimes", 
                        "id" => $crimeID, 
                        "success" => true, 
                        "reward" => $cashReward
                    );
                    $actionHook->run($action);

					// No idea what this value does right now?
                    //$add = mt_rand(1, 4);

					// Display the crime success alert
                    $this->alerts[] = $this->page->buildElement('crimeCommitted', array(
						"id" => $crimeID, 
                        "name" => $crimeInfo->C_name,
						"success" => true,
						"text1" => $crimeInfo->C_successText,
                        "text2" => $crimeInfo->C_successText2,
						"money" => $cashReward
                    ));
                }
                
				// Update the crime timer to the commited crimes cooldown
				// TODO: Randomise this cooldown either side
                $this->user->updateTimer('crime', $crimeInfo->C_cooldown, true);
            }     
        } 
    }
?>


