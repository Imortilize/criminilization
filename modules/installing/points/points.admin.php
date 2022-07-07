<?php

    class adminModule {

        private function getPackage($storeID = "all") {
            if ($storeID == "all") {
                $add = "";
            } else {
                $add = " WHERE ST_id = :id";
            }
            
            $store = $this->db->prepare("
                SELECT
                    ST_id as 'id',  
                    ST_desc as 'desc',  
                    ST_points as 'points',  
                    ROUND(ST_cost / 100, 2) as 'cost',  
                    ST_tag as 'tag'
                FROM store" . $add . "
                ORDER BY ST_desc, ST_cost"
            );

            if ($storeID == "all") {
                $store->execute();
                return $store->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $store->bindParam(":id", $storeID);
                $store->execute();
                return $store->fetch(PDO::FETCH_ASSOC);
            }
        }

        private function getTransactions() {
            
            $transactions = $this->db->prepare("
                SELECT
                    PA_createdtime as 'date',  
                    PA_id as 'id',  
                    PA_payment_amount as 'paid',  
                    ST_points as 'points',  
                    U_id as 'uid',
                    U_name as 'user'
                FROM payments
                LEFT OUTER JOIN store ON (ST_id = PA_itemid)
                LEFT OUTER JOIN users ON (U_id = PA_user)
                ORDER BY PA_createdtime DESC"
            );

            $transactions->execute();
            return $transactions->fetchAll(PDO::FETCH_ASSOC);
        }

        private function validatePackage($store) {
            $errors = array();

            if (strlen($store["desc"]) < 6) {
                $errors[] = "Package name is to short, this must be atleast 5 characters";
            }
            if (intval($store["cost"]) < 0) {
                $errors[] = "The value must be greater than 0";
            }

            return $errors;
            
        }

        public function method_new () {

            $store = array();

            if (isset($this->methodData->submit)) {
                $store = (array) $this->methodData;
                $errors = $this->validatePackage($store);
                
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $this->methodData->cost *= 100;
                    $insert = $this->db->prepare("
                        INSERT INTO store (ST_desc, ST_tag, ST_points, ST_cost)  VALUES (:desc, :tag, :points, :cost);
                    ");
                    $insert->bindParam(":desc", $this->methodData->desc);
                    $insert->bindParam(":tag", $this->methodData->tag);
                    $insert->bindParam(":points", $this->methodData->points);
                    $insert->bindParam(":cost", $this->methodData->cost);
                    
                    $insert->execute();


                    $this->html .= $this->page->buildElement("success", array("text" => "This package has been created"));

                }

            }

            $store["editType"] = "new";
            $this->html .= $this->page->buildElement("storeForm", $store);
        }

        public function method_edit () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No package ID specified"));
            }

            $store = $this->getPackage($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $store = (array) $this->methodData;
                $errors = $this->validatePackage($store);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $this->methodData->cost *= 100;
                    $update = $this->db->prepare("
                        UPDATE store SET ST_desc = :desc, ST_tag = :tag, ST_points = :points, ST_cost = :cost WHERE ST_id = :id
                    ");
                    $update->bindParam(":desc", $this->methodData->desc);
                    $update->bindParam(":tag", $this->methodData->tag);
                    $update->bindParam(":points", $this->methodData->points);
                    $update->bindParam(":cost", $this->methodData->cost);
                    $update->bindParam(":id", $this->methodData->id);
                    $update->execute();

                    $this->html .= $this->page->buildElement("success", array("text" => "This package has been updated"));

                }

            }

            $store["editType"] = "edit";
            $this->html .= $this->page->buildElement("storeForm", $store);
        }

        public function method_delete () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No package ID specified"));
            }

            $store = $this->getPackage($this->methodData->id);

            if (!isset($store["id"])) {
                return $this->html = $this->page->buildElement("error", array("text" => "This package does not exist"));
            }

            if (isset($this->methodData->commit)) {
                $delete = $this->db->prepare("
                    DELETE FROM store WHERE ST_id = :id;
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();

                header("Location: ?page=admin&module=points");

            }

            $this->html .= $this->page->buildElement("storeDelete", $store);
        }

        public function method_view () {
            
            $this->html .= $this->page->buildElement("storeList", array(
                "store" => $this->getPackage()
            ));

        }

        public function method_transactions () {
            
            $this->html .= $this->page->buildElement("transactions", array(
                "transactions" => $this->getTransactions()
            ));

        }

        public function method_settings() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("currency", $this->methodData->currency);
                $settings->update("currencySymbol", $this->methodData->currencySymbol);
                $settings->update("currencyDecimalSeperator", $this->methodData->currencyDecimalSeperator);
                $settings->update("paypalEmail", $this->methodData->paypalEmail);
                $this->html = $this->page->buildElement("success", array("text" => "Settings updated!"));
            }


            $output = array(
                "currency" => $settings->loadSetting("currency"),
                "currencySymbol" => $settings->loadSetting("currencySymbol"),
                "currencyDecimalSeperator" => $settings->loadSetting("currencyDecimalSeperator", true, "."),
                "paypalEmail" => $settings->loadSetting("paypalEmail")
            );

            $this->html .= $this->page->buildElement("paypalSettings", $output);

        }

    }
