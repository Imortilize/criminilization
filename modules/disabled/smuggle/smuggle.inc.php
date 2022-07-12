<?php

    class smuggle extends module {
        
        public $allowedMethods = array(
            "drug" => array("type" => "POST"),
            "type" => array("type" => "POST")
        );
        
        public $pageName = 'Welcome back';

        public $times = 30;

        public function getMax() {

            $max = 100;

            $ranks = $this->db->select("
                SELECT count(*) as 'count' FROM ranks WHERE R_exp < :exp
            ", array(
                ":exp" => $this->user->info->US_exp
            ))["count"];

            $max = $max + ($ranks * 50);

            return $max;

        }
        
        public function constructModule() {

            $lastUpdate = $this->_settings->loadSetting("lastSmuggleUpdate");

            if (!$lastUpdate) $lastUpdate = 0; 

            if (time() - $lastUpdate >= (3600 * 3)) {
                $lastUpdate = $this->_settings->update("lastSmuggleUpdate", time());
                $locations = $this->db->selectAll("SELECT * FROM locations");
                $drugs = $this->db->selectAll("SELECT * FROM drugs");

                foreach ($drugs as $drug) {
                    foreach ($locations as $location) {
                        $cost = mt_rand($drug["DR_min"], $drug["DR_max"]);

                        $this->db->insert("
                            INSERT INTO drugPrices (
                                DRP_location, DRP_drug, DRP_cost
                            ) VALUES (
                                :l, :d, :c
                            ) ON DUPLICATE KEY UPDATE DRP_cost = :c

                        ", array(
                            ":l" => $location["L_id"], 
                            ":d" => $drug["DR_id"], 
                            ":c" => $cost
                        ));
                    }
                }
            }

            $keys = array();
            $i = 0;
            while ($i < $this->times) {
                $i++;
                if ($i % 15 == 0) {
                    $keys[] = date("H:i", $i * 60);
                } else {
                    $keys[] = 1;
                }

            }

            $drugs = $this->db->selectAll("
                SELECT
                    DR_id as 'id', 
                    DR_name as 'name',  
                    UDR_qty as 'owned',
                    UDR_cost as 'paid', 
                    DRP_cost as 'price'
                FROM drugs 
                INNER JOIN drugPrices ON (DRP_drug = DR_id AND DRP_location = :l)
                LEFT OUTER JOIN userDrugs ON (UDR_drug = DR_id AND UDR_user = :id)
                ORDER BY DR_name
            ", array(
                ":id" => $this->user->id,
                ":l" => $this->user->info->US_location
            ));


            foreach ($drugs as $key => $drug) {

                if (!$drug["owned"]) {
                    $drug["owned"] = 0;
                }

                $paid = $drug["paid"];
                $value = $drug["owned"] * $drug["price"];

                $drug["total"] = $value;
                
                $drug["total"] = 0;

                if ($drug["owned"]) {
                    $drug["profit"] = round(100 - abs( $paid / $value * 100), 2);
                    $drug["total"] = $value;
                } else {
                    $drug["profit"] = 0;
                }

                $drugs[$key] = $drug;
            }


            $this->html .= $this->page->buildElement("drugMarket", array(
                "drugs" => $drugs,
                "max" => $this->getMax()
            ));

        }

        public function method_process() {

            $max = $this->getMax();
            $cost = 0;

            switch ($this->methodData->type) {
                case "buy":

                    $totalQty = 0;
                    $totalCost = 0;

                    foreach ($this->methodData->drug as $drug => $qty) {
                        $id = trim(abs($drug));
                        $qty = abs(intval($qty));
                        if (!$qty) continue;
                        $drug = $this->db->select("
                            SELECT * 
                            FROM drugs 

                            INNER JOIN drugPrices 
                                ON (DRP_drug = DR_id AND DRP_location = :l)

                            LEFT OUTER JOIN userDrugs 
                                ON (UDR_drug = DR_id AND UDR_user = :id)

                            WHERE DR_id = :id
                        ", array(
                            ":id" => $id,
                            ":l" => $this->user->info->US_location
                        ));

                        $drugPrice = $drug["DRP_cost"];

                        $owned = $this->db->select("SELECT SUM(UDR_qty) as 'qty' FROM userDrugs WHERE UDR_user = :u", array(
                            ":u" => $this->user->id
                        ));

                        if ($max < $qty + $owned["qty"]) {
                            $this->error("You can only buy a total of " . number_format($max) . " items!");
                        } else if ($drugPrice * $qty > $this->user->info->US_money) {
                            $this->error("You cant afford to buy $qty " . $drug["DR_name"] . "'s");
                        } else {
                            $update = $this->db->insert("
                                INSERT INTO userDrugs (UDR_user, UDR_drug, UDR_qty, UDR_cost)
                                VALUES (:u, :s, :q, :c)
                                ON DUPLICATE KEY UPDATE UDR_qty = UDR_qty + :q, UDR_cost = UDR_cost + :c;
                            ", array(
                                ":u" => $this->user->id,
                                ":s" => $id, 
                                ":q" => $qty, 
                                ":c" => ($drugPrice * $qty)
                            ));

                            $this->user->set("US_money", $this->user->info->US_money - $qty * $drugPrice);  
                            $totalQty += $qty;
                            $totalCost += $qty * $drugPrice;
                        }


                    }


                    if ($totalQty) $this->error("You bought $totalQty items for " .$this->money($totalCost) , "success");

                break;
                case "sell":

                    $totalQty = 0;
                    $totalCost = 0;

                    foreach ($this->methodData->drug as $drug => $qty) {
                        $id = trim(abs($drug));

                        $qty = abs(intval($qty));

                        if (!$qty) continue;

                        $drug = $this->db->select("
                            SELECT * 
                            FROM drugs 

                            INNER JOIN drugPrices 
                                ON (DRP_drug = DR_id AND DRP_location = :l)

                            LEFT OUTER JOIN userDrugs 
                                ON (UDR_drug = DR_id AND UDR_user = :id)

                            WHERE DR_id = :id
                        ", array(
                            ":id" => $id,
                            ":l" => $this->user->info->US_location
                        ));

                        $drugPrice = $drug["DRP_cost"];

                        $owned = $this->db->select("
                            SELECT * 
                            FROM userDrugs 
                            WHERE UDR_user = :u AND UDR_drug = :id
                        ", array(
                            ":id" => $id, 
                            ":u" => $this->user->id
                        ));

                        if ($owned["UDR_qty"] < $qty) {
                            $this->error("You dont own $qty " . $drug["DR_name"] . "'s");
                        } else {

                            $valueOfRemaingDrug = $owned["UDR_cost"] * (1 - $qty / $owned["UDR_qty"]);

                            $this->db->update("UPDATE userDrugs SET UDR_qty = UDR_qty - :q, UDR_cost = :c WHERE UDR_drug = :s AND UDR_user = :u", array(
                                ":c" => $valueOfRemaingDrug,
                                ":q" => $qty, 
                                ":s" => $id, 
                                ":u" => $this->user->id
                            ));

                            $totalQty += $qty;
                            $totalCost += $qty * $drugPrice;
                        }

                    }

                    $this->user->set("US_money", $this->user->info->US_money + $totalCost);

                    if ($totalQty) $this->error("You sold $totalQty items for " . $this->money($totalCost), "success");

                break;
                default:
                    return $this->error("Invalid action!");
                break;

            }




        }
        
    }

?>