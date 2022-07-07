<?php

    class adminModule {

        private function getNews($drugID = "all") {
            if ($drugID == "all") {
                $add = "";
            } else {
                $add = " WHERE DR_id = :id";
            }
            
            $drug = $this->db->prepare("
                SELECT
                    DR_id as 'id', 
                    DR_name as 'name',  
                    DR_min as 'min',
                    DR_max as 'max'
                FROM drugs
                " . $add . "
                ORDER BY DR_id
            ");

            if ($drugID == "all") {
                $drug->execute();
                return $drug->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $drug->bindParam(":id", $drugID);
                $drug->execute();
                return $drug->fetch(PDO::FETCH_ASSOC);
            }
        }

        private function validatedrug($drug) {
            $errors = array();
        
            if (strlen($drug["name"]) < 2) {
                $errors[] = "Item name is to short, this must be atleast 5 characters.";
            } 
            if (strlen($drug["min"]) > $drug["max"]) {
                $errors[] = "Item min price shoulf be lower then the max price.";
            } 

            return $errors;
            
        }

        public function upload($id) {
            if (isset($_FILES["image"])) {
                $new = __DIR__ . "/images/" . $id . ".png";
                move_uploaded_file($_FILES["image"]["tmp_name"], $new);
            }
        }

        public function method_new () {

            $drug = array();

            if (isset($this->methodData->submit)) {
                $drug = (array) $this->methodData;
                $errors = $this->validatedrug($drug);
                
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $drugID = $this->db->insert("
                        INSERT INTO drugs (
                            DR_name, DR_min, DR_max
                        )  VALUES (
                            :name, :min, :max
                        );
                    ", array(
                        ":name" => $this->methodData->name,
                        ":min" => $this->methodData->min,
                        ":max" => $this->methodData->max
                    ));

                    $locations = $this->db->selectAll("SELECT * FROM locations");

                    foreach ($locations as $location) {
                        $this->db->insert("
                            INSERT INTO drugPrices (
                                DRP_location, DRP_drug, DRP_cost
                            ) VALUES (
                                :l, :d, :c
                            )
                        ", array(
                            ":l" => $location["L_id"], 
                            ":d" => $drugID, 
                            ":c" => mt_rand($this->methodData->min, $this->methodData->max)
                        ));
                    }

                    $this->upload($drugID);

                    $this->html .= $this->page->buildElement("success", array("text" => "This item has been created"));

                }

            }

            $drug["editType"] = "new";
            $this->html .= $this->page->buildElement("drugMarketNewForm", $drug);
        }

        public function method_edit () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No item ID specified"));
            }

            $drug = $this->getNews($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $drug = (array) $this->methodData;
                $errors = $this->validatedrug($drug);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $update = $this->db->prepare("
                        UPDATE drugs SET 
                            DR_name = :name,  
                            DR_min = :min, 
                            DR_max = :max
                        WHERE DR_id = :id
                    ");
                    $update->bindParam(":name", $this->methodData->name);
                    $update->bindParam(":min", $this->methodData->min);
                    $update->bindParam(":max", $this->methodData->max);
                    $update->bindParam(":id", $this->methodData->id);
                    $update->execute();

                    $this->html .= $this->page->buildElement("success", array("text" => "Item has been updated"));

                    $this->upload($this->methodData->id);

                }

            }

            $drug["editType"] = "edit";
            $this->html .= $this->page->buildElement("drugMarketNewForm", $drug);
        }

        public function method_delete () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No drug ID specified"));
            }

            $drug = $this->getNews($this->methodData->id);

            if (!isset($drug["id"])) {
                return $this->html = $this->page->buildElement("error", array("text" => "This drug does not exist"));
            }

            if (isset($this->methodData->commit)) {
                $delete = $this->db->prepare("
                    DELETE FROM drugs WHERE DR_id = :id;
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();

                header("Location: ?page=admin&module=smuggle");

            }


            $this->html .= $this->page->buildElement("drugMarketDelete", $drug);
        }

        public function method_view () {

            $this->html .= $this->page->buildElement("drugMarketList", array(
                "drugMarket" => $this->getNews()
            ));

        }

    }