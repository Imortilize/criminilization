<?php

    class adminModule {

        private function getOC($ocID = "all") {
            if ($ocID == "all") {
                $add = "";
            } else {
                $add = " WHERE OCT_id = :id";
            }
            
            $oc = $this->db->prepare("
                SELECT
                    OCT_id as 'id',  
                    OCT_name as 'name',  
                    OCT_cooldown as 'cooldown',  
                    OCT_cost as 'cost',  
                    OCT_successEXP as 'successEXP',  
                    OCT_failedEXP as 'failedEXP',  
                    OCT_minCash as 'minCash',  
                    OCT_maxCash as 'maxCash'
                FROM 
                    ocTypes
                    " . $add . "
                ORDER BY OCT_cost ASC
            ");

            if ($ocID == "all") {
                $oc->execute();
                return $oc->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $oc->bindParam(":id", $ocID);
                $oc->execute();
                return $oc->fetch(PDO::FETCH_ASSOC);
            }
        }

        public function method_options() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("ocCar2level", $this->methodData->ocCar2level);
                $settings->update("ocCar3level", $this->methodData->ocCar3level);
                $settings->update("ocCar4level", $this->methodData->ocCar4level);
                $settings->update("ocWep1Name", $this->methodData->ocWep1Name);
                $settings->update("ocWep2Name", $this->methodData->ocWep2Name);
                $settings->update("ocWep3Name", $this->methodData->ocWep3Name);
                $settings->update("ocWep4Name", $this->methodData->ocWep4Name);
                $settings->update("ocExp1Name", $this->methodData->ocExp1Name);
                $settings->update("ocExp2Name", $this->methodData->ocExp2Name);
                $settings->update("ocExp3Name", $this->methodData->ocExp3Name);
                $settings->update("ocExp4Name", $this->methodData->ocExp4Name);
                $settings->update("ocWep1Cost", $this->methodData->ocWep1Cost);
                $settings->update("ocWep2Cost", $this->methodData->ocWep2Cost);
                $settings->update("ocWep3Cost", $this->methodData->ocWep3Cost);
                $settings->update("ocWep4Cost", $this->methodData->ocWep4Cost);
                $settings->update("ocExp1Cost", $this->methodData->ocExp1Cost);
                $settings->update("ocExp2Cost", $this->methodData->ocExp2Cost);
                $settings->update("ocExp3Cost", $this->methodData->ocExp3Cost);
                $settings->update("ocExp4Cost", $this->methodData->ocExp4Cost);

                $this->html .= $this->page->buildElement("success", array(
                    "text" => "OC items updated."
                ));
            }


            $output = array(
                "ocWep1Name" => $settings->loadSetting("ocWep1Name", true, "Glock 17"),
                "ocWep2Name" => $settings->loadSetting("ocWep2Name", true, "Uzi"),
                "ocWep3Name" => $settings->loadSetting("ocWep3Name", true, "Browning Auto-5"),
                "ocWep4Name" => $settings->loadSetting("ocWep4Name", true, "M16 Assault Rifle"),
                "ocExp1Name" => $settings->loadSetting("ocExp1Name", true, "IED"),
                "ocExp2Name" => $settings->loadSetting("ocExp2Name", true, "Grenades"),
                "ocExp3Name" => $settings->loadSetting("ocExp3Name", true, "Dynamite"),
                "ocExp4Name" => $settings->loadSetting("ocExp4Name", true, "C4"),
                "ocWep1Cost" => $settings->loadSetting("ocWep1Cost", true, 10000),
                "ocWep2Cost" => $settings->loadSetting("ocWep2Cost", true, 30000),
                "ocWep3Cost" => $settings->loadSetting("ocWep3Cost", true, 50000),
                "ocWep4Cost" => $settings->loadSetting("ocWep4Cost", true, 100000),
                "ocExp1Cost" => $settings->loadSetting("ocExp1Cost", true, 10000),
                "ocExp2Cost" => $settings->loadSetting("ocExp2Cost", true, 30000),
                "ocExp3Cost" => $settings->loadSetting("ocExp3Cost", true, 50000),
                "ocExp4Cost" => $settings->loadSetting("ocExp4Cost", true, 100000),
                "ocCar1level" => $settings->loadSetting("ocCar1level", true, 0),
                "ocCar2level" => $settings->loadSetting("ocCar2level", true, 10000),
                "ocCar3level" => $settings->loadSetting("ocCar3level", true, 40000),
                "ocCar4level" => $settings->loadSetting("ocCar4level", true, 100000),
            );

            $this->html .= $this->page->buildElement("options", $output);

        }

        public function method_new () {

            $oc = array();

            if (isset($this->methodData->submit)) {
                $oc = (array) $this->methodData;

                $insert = $this->db->prepare("
                    INSERT INTO ocTypes (
                        OCT_name, 
                        OCT_cooldown,   
                        OCT_cost,   
                        OCT_successEXP, 
                        OCT_failedEXP,
                        OCT_minCash, 
                        OCT_maxCash
                    )  VALUES (
                        :name, 
                        :cooldown, 
                        :cost, 
                        :successEXP, 
                        :failedEXP,
                        :minCash, 
                        :maxCash
                    );
                ");
                $insert->bindParam(":name", $this->methodData->name);
                $insert->bindParam(":cooldown", $this->methodData->cooldown);
                $insert->bindParam(":cost", $this->methodData->cost);
                $insert->bindParam(":successEXP", $this->methodData->successEXP);
                $insert->bindParam(":failedEXP", $this->methodData->failedEXP);
                $insert->bindParam(":minCash", $this->methodData->minCash);
                $insert->bindParam(":maxCash", $this->methodData->maxCash);
                $insert->execute();
                $id = $this->db->lastInsertId();


                $this->html .= $this->page->buildElement("success", array("text" => "This OC has been added"));

            }

            $oc["editType"] = "new";

            $this->html .= $this->page->buildElement("ocForm", $oc);
        }

        public function method_multipliers () {
            $this->html .= $this->page->buildElement("ocMultipliers", array());
        }

        public function method_edit () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No oc ID specified"));
            }

            $oc = $this->getOC($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $oc = (array) $this->methodData;

                $update = $this->db->prepare("
                    UPDATE ocTypes SET 
                        OCT_name = :name, 
                        OCT_cooldown = :cooldown,  
                        OCT_cost = :cost,  
                        OCT_successEXP = :successEXP, 
                        OCT_failedEXP = :failedEXP,
                        OCT_minCash = :minCash,
                        OCT_maxCash = :maxCash
                    WHERE 
                        OCT_id = :id;
                ");
                $update->bindParam(":name", $this->methodData->name);
                $update->bindParam(":cooldown", $this->methodData->cooldown);
                $update->bindParam(":cost", $this->methodData->cost);
                $update->bindParam(":successEXP", $this->methodData->successEXP);
                $update->bindParam(":failedEXP", $this->methodData->failedEXP);
                $update->bindParam(":minCash", $this->methodData->minCash);
                $update->bindParam(":maxCash", $this->methodData->maxCash);
                $update->bindParam(":id", $this->methodData->id);
                $update->execute();
                
                $this->html .= $this->page->buildElement("success", array("text" => "This oc has been updated"));

            }

            $oc["editType"] = "edit";

            $this->html .= $this->page->buildElement("ocForm", $oc);
        }

        public function method_delete () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No oc ID specified"));
            }

            $oc = $this->getOC($this->methodData->id);

            if (!isset($oc["id"])) {
                return $this->html = $this->page->buildElement("error", array("text" => "This oc does not exist"));
            }

            if (isset($this->methodData->commit)) {
                $delete = $this->db->prepare("
                    DELETE FROM oc WHERE OCT_id = :id;
                    DELETE FROM ocLoot WHERE RL_id = :id;
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();

                header("Location: ?page=admin&module=oc");

            }


            $this->html .= $this->page->buildElement("ocDelete", $oc);
        }

        public function method_view () {
            
            $this->html .= $this->page->buildElement("ocList", array(
                "oc" => $this->getOC()
            ));

        }

    }
