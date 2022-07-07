<?php

    class adminModule {

        private function getBusinesses($businessesID = "all") {
            if ($businessesID == "all") {
                $add = "";
            } else {
                $add = " WHERE BU_id = :id";
            }
            
            $businesses = $this->db->prepare("
                SELECT
                    BU_id as 'id',  
                    BU_name as 'name',  
                    BU_rank as 'rank',  
                    BU_payout as 'payout',  
                    BU_payoutTime as 'payoutTime',  
                    BU_cost as 'cost'
                FROM businesses" . $add . "
                ORDER BY BU_name, BU_cost"
            );

            if ($businessesID == "all") {
                $businesses->execute();
                return $businesses->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $businesses->bindParam(":id", $businessesID);
                $businesses->execute();
                return $businesses->fetch(PDO::FETCH_ASSOC);
            }
        }

        private function validateBusinesses($businesses) {
            $errors = array();

            if (strlen($businesses["name"]) < 6) {
                $errors[] = "Businesses name is to short, this must be atleast 5 characters";
            }

            return $errors;
            
        }

        public function uploadFile($id) {

            if ($id) {
                if (isset($_FILES["image"])) {
                    move_uploaded_file($_FILES["image"]["tmp_name"], __DIR__ . "/images/" . $id . ".jpg");
                }
            }

        }

        public function method_new () {

            $businesses = array();

            if (isset($this->methodData->submit)) {
                $businesses = (array) $this->methodData;
                $errors = $this->validateBusinesses($businesses);
                
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array(
                            "text" => $error
                        ));
                    }
                } else {
                    $insert = $this->db->prepare("
                        INSERT INTO businesses (
                            BU_name, BU_rank, BU_payout, BU_payoutTime, BU_cost
                        )  VALUES (
                            :name, :rank, :payout, :payoutTime, :cost
                        );
                    ");
                    $insert->bindParam(":name", $this->methodData->name);
                    $insert->bindParam(":rank", $this->methodData->rank);
                    $insert->bindParam(":payout", $this->methodData->payout);
                    $insert->bindParam(":payoutTime", $this->methodData->payoutTime);
                    $insert->bindParam(":cost", $this->methodData->cost);
                    
                    $insert->execute();


                    $this->html .= $this->page->buildElement("success", array(
                        "text" => "This business has been created"
                    ));
                    $id = $this->db->lastInsertId();

                    $this->uploadFile($id);

                }

            }

            $businesses["editType"] = "new";
            $this->html .= $this->page->buildElement("businessesForm", $businesses);
        }

        public function method_edit () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array(
                    "text" => "No business ID specified"
                ));
            }

            $businesses = $this->getBusinesses($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $businesses = (array) $this->methodData;
                $errors = $this->validateBusinesses($businesses);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $update = $this->db->prepare("
                        UPDATE 
                            businesses 
                        SET 
                            BU_name = :name,
                            BU_rank = :rank,
                            BU_payout = :payout,
                            BU_payoutTime = :payoutTime,
                            BU_cost = :cost
                        WHERE BU_id = :id
                    ");
                    $update->bindParam(":name", $this->methodData->name);
                    $update->bindParam(":rank", $this->methodData->rank);
                    $update->bindParam(":payout", $this->methodData->payout);
                    $update->bindParam(":payoutTime", $this->methodData->payoutTime);
                    $update->bindParam(":cost", $this->methodData->cost);
                    $update->bindParam(":id", $this->methodData->id);
                    $update->execute();

                    $this->html .= $this->page->buildElement("success", array(
                        "text" => "This business has been updated"
                    ));

                    $this->uploadFile($this->methodData->id);

                }

            }

            $businesses["editType"] = "edit";
            $this->html .= $this->page->buildElement("businessesForm", $businesses);
        }

        public function method_delete () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No business ID specified"));
            }

            $businesses = $this->getBusinesses($this->methodData->id);

            if (!isset($businesses["id"])) {
                return $this->html = $this->page->buildElement("error", array("text" => "This business does not exist"));
            }

            if (isset($this->methodData->commit)) {
                $delete = $this->db->prepare("
                    DELETE FROM businesses WHERE BU_id = :id;
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();

                header("Location: ?page=admin&module=businesses");

            }


            $this->html .= $this->page->buildElement("businessesDelete", $businesses);
        }

        public function method_view () {
            
            $this->html .= $this->page->buildElement("businessesList", array(
                "businesses" => $this->getBusinesses()
            ));

        }

    }
