<?php

	require('crime_helper.php');

    class crimes extends module {
        
        public $allowedMethods = array('crime'=>array('type'=>'get'));
        
        public $pageName = 'Crimes';
        
        public function constructModule() {
            
			// Check crime timer and clock committing a crime until the timer is complete
            if (!$this->user->checkTimer('crime')) {
				
				/*$crimeError = array(
					"timer" => "crime",
					"text"=>'You can\'t commit another crime untill your timer is up!',
					"time" => $this->user->getTimer("crime")
				);
				$this->html .= $this->page->buildElement('timer', $crimeError);*/

				// TODO: Add a crime cooldown in progress panel....
				if (count($this->alerts) == 0) {
					$this->alerts[] = $this->page->buildElement('crimeCooldown', array(
						"name" => "Crimes",
						"header" => "Cooldown",
						"text" => "You must wait until you can commit another crime.",
					));
				}
				
				return;
            } else {
				$crimes = $this->db->prepare("
					SELECT * FROM crimes INNER JOIN ranks ON (R_id = C_level) ORDER BY C_level ASC
				");
				$crimes->execute();

				$crimePercs = explode('-', $this->user->info->US_crimes);

				$crimeInfo = array();
				while ($crime = $crimes->fetchObject()) {

					$crimeID = $crime->C_id;

					// Calculate the crimes stat distributuon
					[$firstDistributionStat, 
					 $secondDistributionStat, 
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
					 $offColour,
					 $defColour,
					 $stlColour,
					 $bonus,
					 $bonusColour,
					 $bonusIcon] = calculateStatDistribution(
						$crime
					);

					// Create the crime info to display
					$crimeInfo[] = array(
						"name" => $crime->C_name,
						"rank" => $crime->R_name,
						"time" => $this->user->getTimer("crime-" . $crime->C_id),
						"locked" => $crime->R_exp > $this->user->info->US_exp,
						"cooldown" => $this->timeLeft($crime->C_cooldown),
						"percent" => $crime->C_chance, 
						"id" => $crimeID,
						"isMixed" => $isMixed,
						"offRatio" => $offRatio,
						"defRatio" => $defRatio,
						"stlRatio" => $stlRatio,
						"offColour" => $offColour,
						"defColour" => $defColour,
						"stlColour" => $stlColour,
						"bonus" => $bonus,
						"bonusColour" => $bonusColour,
						"bonusIcon" => $bonusIcon
					);
				}

				$location = $this->user->getLocation();
				$this->html .= $this->page->buildElement('crimeHolder', array(
					"crimes" => $crimeInfo,
					"location" => $location
				));
			}
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
						$crimeInfo->C_stealthRatio
					);

					// Display the crime success alert
                    $this->alerts[] = $this->page->buildElement('crimeCommitted', array(
						"id" => $crimeID, 
                        "name" => $crimeInfo->C_name,
						"success" => true,
						"text1" => $crimeInfo->C_successText,
                        "text2" => $crimeInfo->C_successText2,
						"money" => $cashReward
                    ));

					// Set the new user stats
                    $this->user->set("US_money", $this->user->info->US_money + $cashReward);
                    $this->user->set("US_exp", $this->user->info->US_exp + $crimeInfo->C_exp);
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
                }
                
				// Update the crime timer to the commited crimes cooldown
				// TODO: Randomise this cooldown either side
                $this->user->updateTimer('crime', $crimeInfo->C_cooldown, true);
            }     
        } 
    }
?>


