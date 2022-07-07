<?php

    class roulette extends module {
        
        public $allowedMethods = array(
            "bet" => array("type" => "post"),
            "on" => array("type" => "get")
        );

        public $maxBet = 10000;

        public $lastSpin = -1;
        
        public function constructModule() {

            $this->property = new Property($this->user, "roulette");

            $options = $this->property->getOwnership();
            if ($options["cost"]) $this->maxBet = $options["cost"];

            $options["bet"] = isset($this->methodData->bet)?$this->methodData->bet:"";
            $options["maxBet"] = $this->maxBet;
            $options["location"] = $this->user->getLocation();
            $options["table"] = $this->getTable();

            $this->html .= $this->page->buildElement("rouletteTable", $options);
        }

        public function method_reset() {
            $table = $this->getTable();

            foreach ($table["rows"] as $key => $row) {
                foreach ($row["cols"] as $colKey => $col) {
                    if (isset($_SESSION["roulette-" . $col["text"]])) {
                        unset($_SESSION["roulette-" . $col["text"]]);
                    }
                }
            }
        }

        public function method_bet() {

            if (isset($this->methodData->on)) {

                $on = $this->getCell($this->methodData->on);

                if (isset($this->methodData->bet)) {
                    $_SESSION["roulette-" . $on["text"]] = abs(intval($this->methodData->bet));
                }

                if (isset($_SESSION["roulette-" . $on["text"]])) {
                    $on["bet"] = $_SESSION["roulette-" . $on["text"]];

                    if (isset($this->methodData->bet)) {
                        return $this->error("You placed " . $this->money($on["bet"]) . " on " . $on["text"], "success");
                    }
                }

                return $this->html .= $this->page->buildElement("newBet", $on);

            }
        }        

        public function method_remove() {
            if (isset($this->methodData->on)) {
                $on = $this->getCell($this->methodData->on);
                if (isset($this->methodData->on)) {
                    unset($_SESSION["roulette-" . $on["text"]]);
                    return $this->error("You removed your bet of " . $this->money($on["bet"]) . " from " . $on["text"], "info");
                }
            }
        }

        public function method_spin() {

            $this->property = new Property($this->user, "roulette");

            $options = $this->property->getOwnership();

            if ($options["closed"]) {
                return;
            }

            if ($options["cost"]) $this->maxBet = $options["cost"];

            $table = $this->getTable();

            $bet = $table["totalBet"];

             if ($bet > $this->user->info->US_money) {
                return $this->error("You dont have enough cash to cover this bet");
            } 

            if ($bet < 100) {
                return $this->error("You must bet atleast $100");
            }

            if ($bet > $this->maxBet) {
                return $this->error("The max bet is " . $this->money($this->maxBet));
            }

            $spin = mt_rand(0, 36);

            $winnings = 0;

            foreach ($table["bets"] as $bet) {
                $stake = $bet["bet"];
                $cell = $this->getCell($spin);

                $winnings += $this->payout($bet["text"], $spin, $stake, $cell);

            }

            $this->lastSpin = $spin;



            if ($winnings == 0) {
                return $this->error("The ball landed on $spin, you broke even your bet was returned to you", "info");
            } else if ($winnings > 0) {
                $this->userWins($winnings);
                return $this->error("The ball landed on $spin, you won " . $this->money($winnings), "success");
            } else {
                $this->user->set("US_money", $this->user->info->US_money + $winnings);
                $this->property->updateProfit(abs($bet));
                if (isset($options["user"]["id"])) {
                    $owner = new User($options["user"]["id"]);
                    $owner->set("US_money", $owner->info->US_money + abs($winnings));
                }
                $this->property->updateProfit(abs($winnings));
                return $this->error("The ball landed on $spin, you lost " . $this->money(abs($winnings)));
            }

        }
        
        public function method_own() {
            $this->property = new Property($this->user, "roulette");

            $owner = $this->property->getOwnership();

            if ($owner["user"]) {
                $user = $this->page->buildElement("userName", $owner);
                return $this->html .= $this->page->buildElement("error", array(
                    "text" => "This property is owned by " . $user
                ));
            }

            if ($this->user->info->US_money < 1000000) {
                return $this->html .= $this->page->buildElement("error", array(
                    "text" => "You need $1,000,000 to buy of this property."
                ));
            }

            $update = $this->db->prepare("
                UPDATE userStats SET US_money = US_money - 1000000 WHERE US_id = :id
            ");
            $update->bindParam(":id", $this->user->id);
            $update->execute();

            $this->property->transfer($this->user->id);

            return $this->html .= $this->page->buildElement("success", array(
                "text" => "You paid $1,000,000 to buy this property."
            ));

        }

        public function payout($betType, $spin, $stake, $cell) {
            $winnings = 0;
            switch ($betType) {
                case "1 to 18":
                    if ($spin >= 1 && $spin <= 18) {
                        $winnings += $stake;
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "19 to 36":
                    if ($spin >= 19 && $spin <= 36) {
                        $winnings += $stake;
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "EVEN":
                    if ($spin && $spin % 2 == 0) {
                        $winnings += $stake;
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "ODD":
                    if ($spin && $spin % 2 == 1) {
                        $winnings += $stake;
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "BLACK":
                    if (isset($cell["color"]) && $cell["color"] == "black") {
                        $winnings += $stake;
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "RED":
                    if (isset($cell["color"]) && $cell["color"] == "red") {
                        $winnings += $stake;
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "1st 2:1":
                    if ($spin && $spin % 3 == 1) {
                        $winnings += ($stake * 2);
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "2nd 2:1":
                    if ($spin && $spin % 3 == 2) {
                        $winnings += ($stake * 2);
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "3rd 2:1":
                    if ($spin && $spin % 3 == 0) {
                        $winnings += ($stake * 2);
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "1st 12":
                    if ($spin >= 1 && $spin <= 12) {
                        $winnings += ($stake * 2);
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "2nd 12":
                    if ($spin >= 13 && $spin <= 24) {
                        $winnings += ($stake * 2);
                    } else {
                        $winnings -= $stake;
                    }
                break;
                case "3rd 12":
                    if ($spin >= 25 && $spin <= 36) {
                        $winnings += ($stake * 2);
                    } else {
                        $winnings -= $stake;
                    }
                break;
                default:
                    if ($betType == $spin) {
                        $winnings += ($stake * 35);
                    } else {
                        $winnings -= $stake;
                    }
                break;
            }
            return $winnings;
        }

        public function userWins($cash) {

            $this->property = new Property($this->user, "roulette");
            
            $this->property->updateProfit(-$cash);

            $owner = $this->property->getOwnership();

            $actionHook = new hook("userAction");
            $action = array(
                "user" => $this->user->id, 
                "module" => "casinoPayout", 
                "id" => 2, 
                "success" => true, 
                "reward" => $cash, 
                "gt" => true
            );
            $actionHook->run($action);

            if ($owner["user"]) {

                $owner = new User($owner["user"]["id"]);

                if ($cash > $owner->info->US_money) {
                    $this->property->transfer($this->user->id);
                    $this->html .= $this->page->buildElement("warning", array(
                        "text" => "The owner did not have enough cash to pay the bet, you took ownership of the casino."
                    ));

                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "casinoBust", 
                        "id" => 2, 
                        "success" => true, 
                        "reward" => 0
                    );
                    $actionHook->run($action);
                } else {
                    $user = $this->db->prepare("
                        UPDATE userStats SET 
                            US_money = US_money + :bet
                        WHERE 
                            US_id = :id;
                        UPDATE userStats SET 
                            US_money = US_money - :bet
                        WHERE 
                            US_id = :owner
                    ");

                    $user->bindParam(":bet", $cash);
                    $user->bindParam(":id", $this->user->info->US_id);
                    $user->bindParam(":owner", $owner->id);
                    $user->execute();
                }

            } else {

                $user = $this->db->prepare("
                    UPDATE userStats SET 
                        US_money = US_money + :bet
                    WHERE 
                        US_id = :id
                ");

                $user->bindParam(":bet", $cash);
                $user->bindParam(":id", $this->user->info->US_id);
                $user->execute();

            }

        }

        public function getCell($cell, $rows = false) {

            if (!$rows) {
                $table = $this->getTable();
                $rows = $table["rows"];
            }

            foreach ($rows as $key => $row) {
                foreach ($row["cols"] as $key => $col) {
                    if ($col["text"] == $cell) return $col;
                }
            }

        }

        public function getTable() {
            $rows = array();
            $bets = array();

            $rows[] = array(
                "cols" => array(
                    array( "colspan" => 2, "class"=>"blank", "text" => "" ),
                    array( "colspan" => 3, "class"=>"zero", "text" => "0" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "rowspan" => 2, "class"=>"vertical", "text" => "1 to 18" ),
                    array( "rowspan" => 4, "class"=>"vertical", "text" => "1st 12" ),
                    array( "color"=>"red", "class"=>"red", "text" => "1" ),
                    array( "color"=>"black", "class"=>"black", "text" => "2" ),
                    array( "color"=>"red", "class"=>"red", "text" => "3" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "color"=>"black", "class"=>"black", "text" => "4" ),
                    array( "color"=>"red", "class"=>"red", "text" => "5" ),
                    array( "color"=>"black", "class"=>"black", "text" => "6" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "rowspan" => 2, "class"=>"vertical", "text" => "EVEN" ),
                    array( "color"=>"red", "class"=>"red", "text" => "7" ),
                    array( "color"=>"black", "class"=>"black", "text" => "8" ),
                    array( "color"=>"red", "class"=>"red", "text" => "9" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "color"=>"black", "class"=>"black", "text" => "10" ),
                    array( "color"=>"black", "class"=>"black", "text" => "11" ),
                    array( "color"=>"red", "class"=>"red", "text" => "12" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "rowspan" => 2, "class"=>"vertical", "text" => "RED" ),
                    array( "rowspan" => 4, "class"=>"vertical", "text" => "2nd 12" ),
                    array( "color"=>"black", "class"=>"black", "text" => "13" ),
                    array( "color"=>"red", "class"=>"red", "text" => "14" ),
                    array( "color"=>"black", "class"=>"black", "text" => "15" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "color"=>"red", "class"=>"red", "text" => "16" ),
                    array( "color"=>"black", "class"=>"black", "text" => "17" ),
                    array( "color"=>"red", "class"=>"red", "text" => "18" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "rowspan" => 2, "class"=>"vertical", "text" => "BLACK" ),
                    array( "color"=>"red", "class"=>"red", "text" => "19" ),
                    array( "color"=>"black", "class"=>"black", "text" => "20" ),
                    array( "color"=>"red", "class"=>"red", "text" => "21" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "color"=>"black", "class"=>"black", "text" => "22" ),
                    array( "color"=>"red", "class"=>"red", "text" => "23" ),
                    array( "color"=>"black", "class"=>"black", "text" => "24" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "rowspan" => 2, "class"=>"vertical", "text" => "ODD" ),
                    array( "rowspan" => 4, "class"=>"vertical", "text" => "3rd 12" ),
                    array( "color"=>"red", "class"=>"red", "text" => "25" ),
                    array( "color"=>"black", "class"=>"black", "text" => "26" ),
                    array( "color"=>"red", "class"=>"red", "text" => "27" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "color"=>"black", "class"=>"black", "text" => "28" ),
                    array( "color"=>"black", "class"=>"black", "text" => "29" ),
                    array( "color"=>"red", "class"=>"red", "text" => "30" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "rowspan" => 2, "class"=>"vertical", "text" => "19 to 36" ),
                    array( "color"=>"black", "class"=>"black", "text" => "31" ),
                    array( "color"=>"red", "class"=>"red", "text" => "32" ),
                    array( "color"=>"black", "class"=>"black", "text" => "33" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "color"=>"red", "class"=>"red", "text" => "34" ),
                    array( "color"=>"black", "class"=>"black", "text" => "35" ),
                    array( "color"=>"red", "class"=>"red", "text" => "36" )
                )
            );

            $rows[] = array(
                "cols" => array(
                    array( "colspan" => 2, "class"=>"blank", "text" => "" ),
                    array( "class"=>"", "text" => "1st 2:1" ),
                    array( "class"=>"", "text" => "2nd 2:1" ),
                    array( "class"=>"", "text" => "3rd 2:1" )
                )
            );

            foreach ($rows as $key => $row) {
                foreach ($row["cols"] as $colKey => $col) {
                    if (isset($this->methodData->on)) { 
                        if ($this->methodData->on == $col["text"]) {
                            $col["class"] .= " bet-on";
                        } 
                    }

                    if ($this->lastSpin != -1) {
                        if (ctype_digit($col["text"]) && $col["text"] == $this->lastSpin) {
                            $col["class"] .= " landed";
                        }
                    }

                    if (isset($_SESSION["roulette-" . $col["text"]])) {
                        $col["class"] .= " has-bet";
                        $col["bet"] = abs(intval($_SESSION["roulette-" . $col["text"]]));
                        if ($this->lastSpin != -1) {
                            $cell = $this->getCell($this->lastSpin, $rows);
                            $col["winnings"] = $this->payout($col["text"], $this->lastSpin, $col["bet"], $cell);
                            $col["won"] = $col["winnings"] > 0;

                            if ($col["won"]) {
                                $col["class"] .= " won";
                            } else {
                                $col["class"] .= " lost";
                            }

                        } 
                        $bets[] = $col;
                    }

                    $row["cols"][$colKey] = $col;
                }
                $rows[$key] = $row;
            }

            $total = 0;

            foreach ($bets as $bet) {
                $total += $bet["bet"];
            }

            return array(
                "rows" => $rows,
                "bets" => $bets, 
                "totalBet" => $total
            );
        }
    
    }

?>