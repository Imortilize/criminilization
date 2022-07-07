<?php

    class keno extends module {
        
        public $allowedMethods = array(
            "bet" => array("type" => "post"),
            "numbers" => array("type" => "post")
        );

        public $payouts = array(
            4, 
            0,
            0,
            0,
            0,
            2, 
            15, 
            50, 
            500, 
            5000, 
            100000
        );
        
        public $pageName = '';

        public $maxBet = 10000;

        public $winningNumbers = array();
        
        public function constructModule() {

            $rows = array();
            
            $numbers = array();

            if (isset($this->methodData->numbers)) {
                $numbers = array_unique(explode(",", $this->methodData->numbers));
            }

            $row = 0;
            while ($row < 8) {
                $cols = array();
                $col = 1;
                while ($col <= 10) {
                    $number = $row * 10 + $col;
                    
                    $class = in_array($number, $numbers)?"chosen":"";
                    if (in_array($number, $this->winningNumbers)) {
                        $class .= " selected";
                    }
                    $cols[] = array(
                        "class" => $class,
                        "num" => $number
                    );
                    $col++;
                }
                $rows[] = array(
                    "cols" => $cols
                );
                $row++;
            }

            $this->property = new Property($this->user, "keno");

            $options = $this->property->getOwnership();
            if ($options["cost"]) $this->maxBet = $options["cost"];

            $options["numbers"] = isset($this->methodData->numbers)?$this->methodData->numbers:"";
            $options["bet"] = isset($this->methodData->bet)?$this->methodData->bet:"";
            $options["maxBet"] = $this->maxBet;
            $options["rows"] = $rows;
            $options["location"] = $this->user->getLocation();

            $this->html .= $this->page->buildElement("kenoTable", $options);
        }

        public function method_bet() {

            $this->property = new Property($this->user, "keno");

            $options = $this->property->getOwnership();

            if (isset($options["closed"]) && $options["closed"]) {
                return;
            }

            if ($options["cost"]) $this->maxBet = $options["cost"];

            if (isset($this->methodData->bet)) {
                $bet = abs(intval($this->methodData->bet));

                if ($bet > $this->user->info->US_money) {
                    return $this->error("You dont have enough cash to cover this bet");
                } 

                if ($bet < 100) {
                    return $this->error("You must bet atleast $100");
                }

                if ($bet > $this->maxBet) {
                    return $this->error("The max bet is " . $this->money($this->maxBet));
                }

                if (!isset($this->methodData->numbers)) {
                    return $this->error("Please select 10 unique numbers");
                }

                $numbers = array_unique(explode(",", $this->methodData->numbers));

                if (count($numbers) != 10) {
                    return $this->error("Please select 10 unique numbers");
                }

                $winningNumbers = array();

                while (count($winningNumbers) != 20) {
                    $rand = mt_rand(1, 80);
                    if (!in_array($rand, $winningNumbers)) $winningNumbers[] = $rand;
                }

                $this->winningNumbers = $winningNumbers;

                $matches = 0;

                foreach ($numbers as $number) {
                    if (in_array($number, $this->winningNumbers)) $matches++;
                }

                $prize = $this->payouts[$matches] * $bet;


                if ($prize) {
                    $this->userWins($prize);
                    $this->error("You matched $matches numbers, you won " . $this->money($prize) . "!", "success");
                } else {
                    $this->property->updateProfit($bet);                    
                    $this->user->set("US_money", $this->user->info->US_money - $bet);
                    if (isset($options["user"]["id"])) {
                        $owner = new User($options["user"]["id"]);
                        $owner->set("US_money", $owner->info->US_money + $bet);
                    }
                    $this->error("You only matched $matches numbers, you lost your bet of " . $this->money($bet) . "!");
                }

            }
        }

        public function method_own() {
            $this->property = new Property($this->user, "keno");

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

        public function userWins($cash) {

            $this->property = new Property($this->user, "keno");
            
            $this->property->updateProfit(-$cash);

            $owner = $this->property->getOwnership();

            $actionHook = new hook("userAction");
            $action = array(
                "user" => $this->user->id, 
                "module" => "casinoPayout", 
                "id" => 3, 
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
                        "id" => 3, 
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
    
    }

?>