<?php

    class adminModule {

        private function getInvestment($investmentID = "all") {
            if ($investmentID == "all") {
                $add = "";
            } else {
                $add = " WHERE IN_id = :id";
            }
            
            $investment = $this->db->prepare("
                SELECT
                    IN_id as 'id',  
                    IN_name as 'name',  
                    ROUND(IN_min / 100, 2) as 'min',  
                    ROUND(IN_max / 100, 2) as 'max',  
                    IN_maxInvest as 'maxInvest',  
                    IN_time as 'time',
                    UNIX_TIMESTAMP() - IN_time as 'timeMinusNow'
                FROM investments" . $add
            );

            if ($investmentID == "all") {
                $investment->execute();
                return $investment->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $investment->bindParam(":id", $investmentID);
                $investment->execute();
                return $investment->fetch(PDO::FETCH_ASSOC);
            }
        }

        public function uploadFile($id) {

            if ($id) {
                if (isset($_FILES["image"])) {
                    move_uploaded_file($_FILES["image"]["tmp_name"], __DIR__ . "/images/" . $id . ".jpg");
                }
            }

        }

        private function validateInvestment($investment) {
            $errors = array();

            if (strlen($investment["name"]) < 6) {
                $errors[] = "Investment name is to short, this must be atleast 5 characters";
            }
            if ($investment["min"] > $investment["max"]) {
                $errors[] = "Maximum gain should be greater then the minimum gain";
            }

            return $errors;
            
        }

        public function method_new () {

            $investment = array();

            if (isset($this->methodData->submit)) {
                $investment = (array) $this->methodData;
                $errors = $this->validateInvestment($investment);
                
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {

                    $insert = $this->db->insert("
                        INSERT INTO investments (IN_name, IN_min, IN_max, IN_maxInvest, IN_time)  VALUES (:name, :min, :max, :maxInvest, :time);
                    ", array(
                        ":name" => $this->methodData->name,
                        ":min" => $this->methodData->min * 100,
                        ":max" => $this->methodData->max * 100,
                        ":maxInvest" => $this->methodData->maxInvest,
                        ":time" => $this->methodData->time
                    ));

                    $this->html .= $this->page->buildElement(
                        "success", array("text" => "This investment has been created")
                    );

                }

            }

            $investment["editType"] = "new";
            $this->html .= $this->page->buildElement("investmentForm", $investment);
        }

        public function method_edit () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No investment ID specified"));
            }

            $investment = $this->getInvestment($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $investment = (array) $this->methodData;
                $errors = $this->validateInvestment($investment);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $update = $this->db->update("
                        UPDATE investments SET IN_name = :name, IN_min = :min, IN_max = :max, IN_maxInvest = :maxInvest, IN_time = :time WHERE IN_id = :id
                    ", array(
                        ":name" => $this->methodData->name,
                        ":min" => $this->methodData->min*100,
                        ":max" => $this->methodData->max*100,
                        ":maxInvest" => $this->methodData->maxInvest,
                        ":time" => $this->methodData->time,
                        ":id" => $this->methodData->id
                    ));

                    $this->html .= $this->page->buildElement("success", array("text" => "This investment has been updated"));

                }

            }

            $investment["editType"] = "edit";
            $this->html .= $this->page->buildElement("investmentForm", $investment);
        }

        public function method_delete () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No investment ID specified"));
            }

            $investment = $this->getInvestment($this->methodData->id);

            if (!isset($investment["id"])) {
                return $this->html = $this->page->buildElement("error", array("text" => "This investment does not exist"));
            }

            if (isset($this->methodData->commit)) {
                $delete = $this->db->prepare("
                    DELETE FROM investments WHERE IN_id = :id;
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();

                header("Location: ?page=admin&module=investments");

            }


            $this->html .= $this->page->buildElement("investmentDelete", $investment);
        }

        public function method_view () {
            
            $this->html .= $this->page->buildElement("investmentList", array(
                "investments" => $this->getInvestment()
            ));

        }

    }