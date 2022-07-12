<?php

    class lottery extends module {
        
        public $allowedMethods = array(
            'tickets'=>array('type'=>'post')
        );        

        public function method_buy() {

            $lottery = $this->getLottery();
            $tickets = abs($this->methodData->tickets);
            $cost = $tickets * $lottery["cost"];

            if (($this->user->info->US_lotteryTickets + $tickets) > $lottery["maxTickets"]) {
                return $this->error("You can only buy " . number_format($lottery["maxTickets"]) . " tickets!");
            }

            if ($this->user->info->US_money < $cost) {
                return $this->error("You cant afford this!");
            }

            if (time() > $lottery["nextDraw"] - 300) {
                return $this->error("You cant buy tickets within 5 minutes of the draw time");
            } 

            $this->user->set("US_money", $this->user->info->US_money - $cost);
            $this->user->set("US_lotteryTickets", $this->user->info->US_lotteryTickets + $tickets);

            $tax = $this->_settings->loadSetting("lotteryTax", true, 25);

            $jackpot = $cost / 100 * (100 - $tax);

            $this->db->update("
                UPDATE lottery SET LO_jackpot = LO_jackpot + :jackpot WHERE LO_date = :date
            ", array(
                ":jackpot" => $jackpot, 
                ":date" => $lottery["nextDraw"]
            ));

            $this->error("You paid " . $this->money($cost) . " for " . number_format($tickets) . " lottery tickets", "success");

        }

        public function getLottery() {

            $drawHour = $this->_settings->loadSetting("lotteryTime", true, 19);

            $currentHour = (int) date("H");

            if ($currentHour >= $drawHour) {
                $nextDraw = strtotime("tomorrow ".$drawHour.":00:00");
                $lastDraw = strtotime($drawHour.":00:00");
            } else {
                $nextDraw = strtotime($drawHour.":00:00");
                $lastDraw = strtotime("yesterday ".$drawHour.":00:00");
            }

            $nextLottery = $this->db->select("SELECT * FROM lottery WHERE LO_date = " . $nextDraw);
            $lastLottery = $this->db->select("SELECT * FROM lottery WHERE LO_date = " . $lastDraw);

            if (!$nextLottery) {
                $insert = $this->db->insert("
                    INSERT INTO lottery (LO_date, LO_winner, LO_jackpot) VALUES (".$nextDraw.", 0, 0);
                ");
                return $this->getLottery();
            }


            $user = false;
            $prev = false;
            if ($lastLottery) {
                $prev = $lastLottery["LO_jackpot"];
                $user = new User($lastLottery["LO_winner"]);
                $user = $user->user;
            }

            return array(
                "nextDraw" => $nextDraw, 
                "user" => $user,
                "prev" => $prev,
                "maxTickets" => $this->_settings->loadSetting("lotteryMax", true, 1000000),
                "cost" => $this->_settings->loadSetting("lotteryCost", true, 40),
                "tickets" => $this->user->info->US_lotteryTickets,
                "jackpot" => $nextLottery["LO_jackpot"]
            );

        }

        public function constructModule() {

            $lottery = $this->getLottery();

            $this->html .= $this->page->buildElement("lottery", $lottery);
        }
        
    }

?>