<?php

    class stocks extends module {
        
        public $allowedMethods = array(
            "stock" => array("type" => "POST"),
            "type" => array("type" => "POST")
        );
        
        public $pageName = 'Welcome back';

        public $historyLength = 60;

        public function calculateStockPriceChange() {

            $stocks = $this->db->selectAll("SELECT * FROM stocks");

            foreach ($stocks as $stock) {

                $min = $stock["ST_min"];
                $max = $stock["ST_max"];
                $volitile = mt_rand(1, $stock["ST_vol"] * 10) / 1000;
                $rising = $stock["ST_rising"];
                $history = explode(",", $stock["ST_history"]);
                $stockCost = $history[count($history) - 1];
                $nextChange = $stock["ST_change"];

                $maxChange = $stockCost / 2 * $volitile;

                if ($rising == 1) {
                    $stockCost = mt_rand(floor($stockCost - $maxChange * 0.1), ceil($stockCost + $maxChange * 0.9));
                } else {
                    $stockCost = mt_rand(floor($stockCost - $maxChange * 0.9), ceil($stockCost + $maxChange * 0.1));
                }

                $variation = 0.95 + (mt_rand(0, 1000) / 1000);

                if ($stockCost < $min) {
                    $stockCost = $min * $variation;
                    $rising = true;
                }

                if ($stockCost > $max) {
                    $stockCost = $max * $variation;
                    $rising = false;
                }

                if ($nextChange == 1) {
                    $nextChange = mt_rand(1, 2);
                    $rising = !!mt_rand(0, 1);
                }

                $history[] = $stockCost;

                $history = array_slice($history, -$this->historyLength);

                $this->db->update("
                    UPDATE stocks SET ST_history = :h, ST_change = :c, ST_rising = :r WHERE ST_id = :id
                ", array(
                    ":h" => implode(",", $history), 
                    ":c" => $nextChange, 
                    ":r" => intval($rising), 
                    ":id" =>$stock["ST_id"]
                ));

            }

        }


        public function getMax() {

            $max = 80;

            $ranks = $this->db->select("
                SELECT count(*) as 'count' FROM ranks WHERE R_exp < :exp
            ", array(
                ":exp" => $this->user->info->US_exp
            ))["count"];

            $max = $max + ($ranks * 120);

            return $max;
        }
        
        public function constructModule() {

            $lastUpdate = $this->_settings->loadSetting("lastStockUpdate");

            if (!$lastUpdate) $lastUpdate = 0; 
            $lastUpdate = $lastUpdate - ($lastUpdate % 60);

            if (time() - $lastUpdate >= 60) {
                $times = floor((time() - $lastUpdate) / 60);
                $this->_settings->update("lastStockUpdate", $lastUpdate + ($times * 60));
                if ($times > 10) $times = 10;
                $i = 0;
                while ($i < $times) {
                    $this->calculateStockPriceChange();
                    $i++;
                }
            }

            $i = 0;
            $times = $this->historyLength;
            while ($i < $times) {
                $this->calculateStockPriceChange();
                $i++;
            }

            $stocks = $this->db->selectAll("
                SELECT
                    ST_id as 'id', 
                    ST_name as 'name', 
                    ST_desc as 'desc', 
                    UST_qty as 'owned',
                    UST_cost as 'paid',
                    ST_history as 'history'
                FROM stocks 
                LEFT OUTER JOIN userStocks ON (UST_stock = ST_id AND UST_user = :id)
                ORDER BY ST_name
            ", array(
                ":id" => $this->user->id
            ));


            foreach ($stocks as $key => $stock) {

                if (!$stock["owned"]) {
                    $stock["owned"] = 0;
                }

                $history = explode(",", $stock["history"]);

                $stock["price"] = array(
                    "value" => $history[count($history) - 1], 
                    "goingUp" => $history[count($history) - 1] > $history[count($history) - 2]
                );

                $paid = $stock["paid"];
                $value = $stock["owned"] * $stock["price"]["value"];

                $stock["total"] = $value;
                
                $stock["total"] = 0;

                if ($stock["owned"]) {

                    $stock["profit"] = array(
                        "value" => round(100 - abs( $paid / $value * 100), 2), 
                        "goingUp" => $paid < $value
                    );
                    $stock["total"] = $value;
                }

                $stock["history"] = json_encode($history);

                $stocks[$key] = $stock;
            }

            $this->html .= $this->page->buildElement("stockMarket", array(
                "stocks" => $stocks,
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

                    foreach ($this->methodData->stock as $stock => $qty) {
                        $id = trim(abs($stock));

                        $qty = abs(intval($qty));

                        if (!$qty) continue;

                        $stock = $this->db->select("SELECT * FROM stocks WHERE ST_id = :id", array(
                            ":id" => $id
                        ));

                        $history = explode(",", $stock["ST_history"]);
                        $stockPrice = $history[count($history) - 1];

                        $owned = $this->db->select("SELECT * FROM userStocks WHERE UST_user = :u AND UST_stock = :id", array(
                            ":id" => $id, 
                            ":u" => $this->user->id
                        ));

                        if ($max < $qty + $owned["UST_qty"]) {
                            $this->error("You can only buy " . number_format($max) . " of each stock!");
                        } else if ($stockPrice * $qty > $this->user->info->US_money) {
                            $this->error("You cant afford to buy $qty shares of " . $stock["ST_name"]);
                        } else {
                            $update = $this->db->insert("
                                INSERT INTO userStocks (UST_user, UST_stock, UST_qty, UST_cost)
                                VALUES (:u, :s, :q, :c)
                                ON DUPLICATE KEY UPDATE UST_qty = UST_qty + :q, UST_cost = UST_cost + :c;
                            ", array(
                                ":u" => $this->user->id,
                                ":s" => $id, 
                                ":q" => $qty, 
                                ":c" => ($stockPrice * $qty)
                            ));

                            $this->user->set("US_money", $this->user->info->US_money - $qty * $stockPrice);  
                            $totalQty += $qty;
                            $totalCost += $qty * $stockPrice;
                        }


                    }


                    $this->error("You bought $totalQty shares for " .$this->money($totalCost) , "success");

                break;
                case "sell":

                    $totalQty = 0;
                    $totalCost = 0;

                    foreach ($this->methodData->stock as $stock => $qty) {
                        $id = trim(abs($stock));

                        $qty = abs(intval($qty));

                        if (!$qty) continue;

                        $stock = $this->db->select("SELECT * FROM stocks WHERE ST_id = :id", array(
                            ":id" => $id
                        ));

                        $history = explode(",", $stock["ST_history"]);
                        $stockPrice = $history[count($history) - 1];

                        $owned = $this->db->select("SELECT * FROM userStocks WHERE UST_user = :u AND UST_stock = :id", array(
                            ":id" => $id, 
                            ":u" => $this->user->id
                        ));

                        if ($owned["UST_qty"] < $qty) {
                            $this->error("You dont own $qty shares of " . $stock["ST_name"]);
                        } else {

                            $valueOfRemaingStock = $owned["UST_cost"] * (1 - $qty / $owned["UST_qty"]);

                            $this->db->update("UPDATE userStocks SET UST_qty = UST_qty - :q, UST_cost = :c WHERE UST_stock = :s AND UST_user = :u", array(
                                ":c" => $valueOfRemaingStock,
                                ":q" => $qty, 
                                ":s" => $id, 
                                ":u" => $this->user->id
                            ));

                            $totalQty += $qty;
                            $totalCost += $qty * $stockPrice;
                        }

                    }

                    $this->user->set("US_money", $this->user->info->US_money + $totalCost);

                    if ($totalQty) {
                        $this->error("You sold $totalQty shares for " . $this->money($totalCost), "success");
                    }

                break;
                default:
                    return $this->error("Invalid action!");
                break;

            }




        }
        
    }

?>