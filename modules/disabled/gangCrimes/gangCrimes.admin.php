<?php

    class adminModule {

        private function getCrime($crimeID = "all") {
            if ($crimeID == "all") {
                $add = "";
            } else {
                $add = " WHERE GC_id = :id";
            }
            
            $crime = $this->db->prepare("
                SELECT
                    GC_id as 'id',  
                    GC_name as 'name',  
                    GC_chance as 'chance',  
                    GC_cooldown as 'cooldown',  
                    GC_money as 'money',  
                    GC_maxMoney as 'maxMoney',  
                    GC_bullets as 'bullets',  
                    GC_maxBullets as 'maxBullets',  
                    GC_level as 'level'
                FROM gangCrimes" . $add . "
                ORDER BY GC_level, GC_money"
            );

            if ($crimeID == "all") {
                $crime->execute();
                return $crime->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $crime->bindParam(":id", $crimeID);
                $crime->execute();
                return $crime->fetch(PDO::FETCH_ASSOC);
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
                        INSERT INTO gangCrimes (GC_name, GC_cooldown, GC_money, GC_maxMoney, GC_level, GC_bullets, GC_maxBullets, GC_chance)  VALUES (:name, :cooldown, :money, :maxMoney, :level, :bullets, :maxBullets, :chance);
                    ");
                    $insert->bindParam(":name", $this->methodData->name);
                    $insert->bindParam(":cooldown", $this->methodData->cooldown);
                    $insert->bindParam(":name", $this->methodData->name);
                    $insert->bindParam(":money", $this->methodData->money);
                    $insert->bindParam(":maxMoney", $this->methodData->maxMoney);
                    $insert->bindParam(":level", $this->methodData->level);
                    $insert->bindParam(":bullets", $this->methodData->bullets);
                    $insert->bindParam(":maxBullets", $this->methodData->maxBullets);
                    $insert->bindParam(":chance", $this->methodData->chance);
                    $insert->execute();


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
                        UPDATE gangCrimes SET GC_name = :name, GC_cooldown = :cooldown, GC_money = :money, GC_maxMoney = :maxMoney, GC_level = :level, GC_bullets = :bullets, GC_maxBullets = :maxBullets, GC_chance = :chance WHERE GC_id = :id
                    ");
                    $update->bindParam(":name", $this->methodData->name);
                    $update->bindParam(":cooldown", $this->methodData->cooldown);
                    $update->bindParam(":money", $this->methodData->money);
                    $update->bindParam(":maxMoney", $this->methodData->maxMoney);
                    $update->bindParam(":level", $this->methodData->level);
                    $update->bindParam(":bullets", $this->methodData->bullets);
                    $update->bindParam(":maxBullets", $this->methodData->maxBullets);
                    $update->bindParam(":chance", $this->methodData->chance);
                    $update->bindParam(":id", $this->methodData->id);
                    $update->execute();

                    $this->html .= $this->page->buildElement("success", array("text" => "This crime has been updated"));

                }

            }

            $crime["editType"] = "edit";
            $this->html .= $this->page->buildElement("crimeForm", $crime);
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
                    DELETE FROM gangCrimes WHERE GC_id = :id;
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();

                header("Location: ?page=admin&module=gangCrimes");

            }


            $this->html .= $this->page->buildElement("crimeDelete", $crime);
        }

        public function method_view () {
            
            $this->html .= $this->page->buildElement("crimeList", array(
                "crimes" => $this->getCrime()
            ));

        }

    }