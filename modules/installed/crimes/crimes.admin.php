<?php

	require('crime_helper.php');

    class adminModule {

        private function getCrime($crimeID = "all") {
            if ($crimeID == "all") {
                $add = "";
            } else {
                $add = " WHERE C_id = :id";
            }
            
            $crimes = $this->db->prepare("
                SELECT
                    C_id as 'id',  
					C_index as 'index',  
                    C_name as 'name',  
                    C_exp as 'exp',  
                    C_cooldown as 'cooldown',  
                    C_money as 'money',  
                    C_maxMoney as 'maxMoney',  
                    C_bullets as 'bullets',  
                    C_maxBullets as 'maxBullets',  
                    C_successText as 'successText',  
					C_successText2 as 'successText2',  
                    C_failText as 'failText',  
                    C_chance as 'chance',  
                    C_level as 'level',
					C_totalStats as 'totalStats',
					C_offenceRatio as 'offRatio',
					C_defenceRatio as 'defRatio',
					C_stealthRatio as 'stlRatio',
					(C_totalStats*(C_offenceRatio * 0.01)) as 'offStats',
					(C_totalStats*(C_defenceRatio * 0.01)) as 'defStats',
					(C_totalStats*(C_stealthRatio * 0.01)) as 'stlStats',
					C_bonus as 'bonus'
                FROM crimes" . $add . "
                ORDER BY C_index"
            );

            if ($crimeID == "all") {
                $crimes->execute();
				$fetchedCrimes = $crimes->fetchAll(PDO::FETCH_ASSOC);
				
				foreach ($fetchedCrimes as &$crime) {

					// Calculate the stats to award
					$stats = calculateStats(
						$crime['totalStats'],
						$crime['offRatio'],
						$crime['defRatio'],
						$crime['stlRatio'],
                        $crime['bonus']
					);

					$crime['offStats'] = $stats[0];
                    $crime['defStats'] = $stats[1];
                    $crime['stlStats'] = $stats[2];
                    $crime['adjustedTotalStats'] = $stats[3];;
				}
				unset($crime); 
				
				// Return the modified crimes
                return $fetchedCrimes;
            } else {
                $crimes->bindParam(":id", $crimeID);
                $crimes->execute();
				$crime = $crimes->fetch(PDO::FETCH_ASSOC);
				
				// Calculate the stats to award
				$stats = calculateStats(
					$crime['totalStats'],
					$crime['offRatio'],
					$crime['defRatio'],
					$crime['stlRatio'],
                    $crime['bonus']
				);

				// Set the stats values
				$crime['offStats'] = $stats[0];
				$crime['defStats'] = $stats[1];
				$crime['stlStats'] = $stats[2];
				
				return $crime;
            }
        }

        public function upload($id) {
            if (isset($_FILES["image"])) {
                $new = __DIR__ . "/images/" . $id . ".png";
                move_uploaded_file($_FILES["image"]["tmp_name"], $new);
            }
        }

        private function validateCrime($crime) {
            $errors = array();

            if (strlen($crime["name"]) < 6) {
                $errors[] = "Crime name is to short, this must be atleast 5 characters";
            }
            if (intval($crime["money"]) > intval($crime["maxMoney"])) {
                $errors[] = "The maximum reward is greater then the minimum reward";
            }
            if (!intval($crime["level"])) {
                $errors[] = "No level specified";
            } 
            if (!intval($crime["cooldown"])) {
                $errors[] = "No cooldown specified";
            }
			
			if (!intval($crime["totalStats"])) {
                $errors[] = "No total stats specified";
            }
			if (!floatval($crime["offRatio"])) {
                $errors[] = "No offence ratio specified";
            }
			if (!floatval($crime["defRatio"])) {
                $errors[] = "No defence ratio specified";
            }
			if (!floatval($crime["stlRatio"])) {
                $errors[] = "No steatlh ratio specified";
            }	
			if ((floatval($crime["offRatio"]) + floatval($crime["defRatio"]) + floatval($crime["stlRatio"])) < 100) {
				$errors[] = "Total stat ratios add up to less than 100%";
			}
		
            return $errors;   
        }

        public function method_new () {

            $crime = array();

            if (isset($this->methodData->submit)) {
                $crime = (array) $this->methodData;
                $errors = $this->validateCrime($crime);
                
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $insert = $this->db->prepare("
                        INSERT INTO crimes (C_name, C_cooldown, C_money, C_maxMoney, C_level, C_bullets, C_maxBullets, C_exp, C_successText, C_successText2, C_failText, C_chance, C_bonus)  VALUES (:name, :cooldown, :money, :maxMoney, :level, :bullets, :maxBullets, :exp, :successText, :successText2, :failText, :chance, :crimeBonus);
                    ");
                    $insert->bindParam(":name", $this->methodData->name);
                    $insert->bindParam(":cooldown", $this->methodData->cooldown);
                    $insert->bindParam(":money", $this->methodData->money);
                    $insert->bindParam(":maxMoney", $this->methodData->maxMoney);
                    $insert->bindParam(":level", $this->methodData->level);
                    $insert->bindParam(":bullets", $this->methodData->bullets);
                    $insert->bindParam(":maxBullets", $this->methodData->maxBullets);
                    $insert->bindParam(":exp", $this->methodData->exp);
                    $insert->bindParam(":successText", $this->methodData->successText);
					$insert->bindParam(":successText2", $this->methodData->successText2);
                    $insert->bindParam(":failText", $this->methodData->failText);
                    $insert->bindParam(":chance", $this->methodData->chance);
					$insert->bindParam(":bonus", $this->methodData->bonus);
                    $insert->execute();

                    $this->upload($this->db->lastInsertId());

                    $this->html .= $this->page->buildElement("success", array("text" => "This crime has been created"));

                }

            }

            $crime["editType"] = "new";
            $this->html .= $this->page->buildElement("crimeForm", $crime);
        }

        public function method_edit () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No crime ID specified"));
            }

            $crime = $this->getCrime($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $crime = (array) $this->methodData;
                $errors = $this->validateCrime($crime);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $update = $this->db->prepare("
                        UPDATE crimes SET C_name = :name, C_cooldown = :cooldown, C_money = :money, C_maxMoney = :maxMoney, C_level = :level, C_bullets = :bullets, C_maxBullets = :maxBullets, C_exp = :exp, C_successText = :successText, C_successText2 = :successText2, C_failText = :failText, C_chance = :chance, C_totalStats = :totalStats, C_offenceRatio = :offRatio, C_defenceRatio = :defRatio, C_stealthRatio = :stlRatio, C_bonus = :bonus WHERE C_id = :id
                    ");
                    $update->bindParam(":name", $this->methodData->name);
                    $update->bindParam(":cooldown", $this->methodData->cooldown);
                    $update->bindParam(":money", $this->methodData->money);
                    $update->bindParam(":maxMoney", $this->methodData->maxMoney);
                    $update->bindParam(":level", $this->methodData->level);
                    $update->bindParam(":bullets", $this->methodData->bullets);
                    $update->bindParam(":maxBullets", $this->methodData->maxBullets);
                    $update->bindParam(":exp", $this->methodData->exp);
                    $update->bindParam(":successText", $this->methodData->successText);
					$update->bindParam(":successText2", $this->methodData->successText2);
                    $update->bindParam(":failText", $this->methodData->failText);
                    $update->bindParam(":chance", $this->methodData->chance);	
					$update->bindParam(":totalStats", $this->methodData->totalStats);
					$update->bindParam(":offRatio", $this->methodData->offRatio);
					$update->bindParam(":defRatio", $this->methodData->defRatio);
					$update->bindParam(":stlRatio", $this->methodData->stlRatio);
                    $update->bindParam(":id", $this->methodData->id);
					$update->bindParam(":bonus", $this->methodData->bonus);
                    $update->execute();

                    $this->upload($this->methodData->id);

					// Build the element to say the crime has been updated successfully
                    $this->html .= $this->page->buildElement("success", array("text" => "This crime has been updated"));
                }

				// Return to the view page
				$crimes = $this->getCrime();
				$this->html .= $this->page->buildElement("crimeList", array(
					"crimes" => $crimes
				));
				return;
            } else {

				// Build the edit form for this crime
				$crime["editType"] = "edit";
				$this->html .= $this->page->buildElement("crimeForm", $crime);
			}
        }

        public function method_delete () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No crime ID specified"));
            }

            $crime = $this->getCrime($this->methodData->id);

            if (!isset($crime["id"])) {
                return $this->html = $this->page->buildElement("error", array("text" => "This crime does not exist"));
            }

            if (isset($this->methodData->commit)) {
                $delete = $this->db->prepare("
                    DELETE FROM crimes WHERE C_id = :id;
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();

                header("Location: ?page=admin&module=crimes");

            }


            $this->html .= $this->page->buildElement("crimeDelete", $crime);
        }

        public function method_view () { 
			$crimes = $this->getCrime();
            $this->html .= $this->page->buildElement("crimeList", array(
                "crimes" => $crimes
            ));
        }

    }