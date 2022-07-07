<?php

    class adminModule {

        private function getNews($stockID = "all") {
            if ($stockID == "all") {
                $add = "";
            } else {
                $add = " WHERE ST_id = :id";
            }
            
            $stock = $this->db->prepare("
                SELECT
                    ST_id as 'id', 
                    ST_name as 'name',  
                    ST_desc as 'desc',
                    ST_min as 'min',
                    ST_vol as 'vol',
                    ST_max as 'max'
                FROM stocks
                " . $add . "
                ORDER BY ST_id
            ");

            if ($stockID == "all") {
                $stock->execute();
                return $stock->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $stock->bindParam(":id", $stockID);
                $stock->execute();
                return $stock->fetch(PDO::FETCH_ASSOC);
            }
        }

        private function validatestock($stock) {
            $errors = array();
        
            if (strlen($stock["name"]) < 2) {
                $errors[] = "Stock name is to short, this must be atleast 5 characters.";
            } 
            if (strlen($stock["desc"]) < 10) {
                $errors[] = "Stock text is to short, this must be atleast 10 characters.";
            } 
            if (strlen($stock["min"]) > $stock["max"]) {
                $errors[] = "Stock min price shoulf be lower then the max price.";
            } 

            return $errors;
            
        }

        public function method_new () {

            $stock = array();

            if (isset($this->methodData->submit)) {
                $stock = (array) $this->methodData;
                $errors = $this->validatestock($stock);
                
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $history = mt_rand($this->methodData->min, $this->methodData->max);
                    $vol = $this->methodData->vol;
                    $insert = $this->db->prepare("
                        INSERT INTO stocks (
                            ST_name, ST_desc, ST_min, ST_max, ST_vol, ST_history
                        )  VALUES (
                            :name, :desc, :min, :max, :vol, :history
                        );
                    ");
                    $insert->bindParam(":name", $this->methodData->name);
                    $insert->bindParam(":desc", $this->methodData->desc);
                    $insert->bindParam(":min", $this->methodData->min);
                    $insert->bindParam(":max", $this->methodData->max);
                    $insert->bindParam(":history", $history);
                    $insert->bindParam(":vol", $vol);
                    $insert->execute();

                    $this->html .= $this->page->buildElement("success", array("text" => "This stock post has been created"));

                }

            }

            $stock["editType"] = "new";
            $this->html .= $this->page->buildElement("stockMarketNewForm", $stock);
        }

        public function method_edit () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No stock ID specified"));
            }

            $stock = $this->getNews($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $stock = (array) $this->methodData;
                $errors = $this->validatestock($stock);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $vol = $this->methodData->vol;
                    $update = $this->db->prepare("
                        UPDATE stocks SET 
                            ST_name = :name, 
                            ST_desc = :desc, 
                            ST_min = :min, 
                            ST_max = :max, 
                            ST_vol = :vol
                        WHERE ST_id = :id
                    ");
                    $update->bindParam(":name", $this->methodData->name);
                    $update->bindParam(":desc", $this->methodData->desc);
                    $update->bindParam(":min", $this->methodData->min);
                    $update->bindParam(":max", $this->methodData->max);
                    $update->bindParam(":id", $this->methodData->id);
                    $update->bindParam(":vol", $vol);
                    $update->execute();

                    $this->html .= $this->page->buildElement("success", array("text" => "News post has been updated"));

                }

            }

            $stock["editType"] = "edit";
            $this->html .= $this->page->buildElement("stockMarketNewForm", $stock);
        }

        public function method_delete () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No stock ID specified"));
            }

            $stock = $this->getNews($this->methodData->id);

            if (!isset($stock["id"])) {
                return $this->html = $this->page->buildElement("error", array("text" => "This stock post does not exist"));
            }

            if (isset($this->methodData->commit)) {
                $delete = $this->db->prepare("
                    DELETE FROM stocks WHERE ST_id = :id;
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();

                header("Location: ?page=admin&module=stockMarket");

            }


            $this->html .= $this->page->buildElement("stockMarketDelete", $stock);
        }

        public function method_view () {

            $this->html .= $this->page->buildElement("stockMarketList", array(
                "stockMarket" => $this->getNews()
            ));

        }

    }