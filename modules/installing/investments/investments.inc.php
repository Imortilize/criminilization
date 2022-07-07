<?php

    class investments extends module {
        
        public $allowedMethods = array(
            'investment'=>array('type'=>'post'),
            'invest'=>array('type'=>'post')
        );
        
        public function constructModule() {

            $this->html .= $this->page->buildElement("bank", array(
                "investments" => $this->db->selectAll("
                    SELECT
                        IN_id as 'id',  
                        IN_name as 'name',  
                        ROUND(IN_min / 100, 2) as 'min',  
                        ROUND(IN_max / 100, 2) as 'max',  
                        IN_maxInvest as 'maxInvest',  
                        UNIX_TIMESTAMP() - IN_time as 'time'
                    FROM investments
                "), 
                "time" => $this->user->getTimer("investment"),
                "invested" => $this->user->info->US_invest,
                "investment" => $this->db->select("
                    SELECT
                        IN_id as 'id',  
                        IN_name as 'name',  
                        ROUND(IN_min / 100, 2) as 'min',  
                        ROUND(IN_max / 100, 2) as 'max',  
                        IN_maxInvest as 'maxInvest',  
                        IN_time as 'time'
                    FROM investments
                    WHERE IN_id = :id
                ", array(
                    ":id" => $this->user->info->US_investment
                ))
            ));
            
        }

        public function method_invest() {

            if (!isset($this->methodData->invest)) {
                return $this->error("How much money do you want to invest?");
            }

            $money = abs(intval($this->methodData->invest));

            if (!$money) {
                return $this->error("How much money do you want to invest?");
            }
            
            if ($money > $this->user->info->US_money) {
                return $this->error("You dont have that much money");
            }

            if ($this->user->info->US_invest > 0) {
                return $this->error("You can't invest until your current investment has matured!");
            } 

            if (!isset($this->methodData->investment)) {
                return $this->error("Please select an investment!");
            }

            $investment = $this->db->select("
                SELECT
                    IN_id as 'id',  
                    IN_name as 'name',  
                    ROUND(IN_min / 100, 2) as 'min',  
                    ROUND(IN_max / 100, 2) as 'max',  
                    IN_maxInvest as 'maxInvest',  
                    IN_time as 'time'
                FROM investments
                WHERE IN_id = :id
            ", array(
                ":id" => $this->methodData->investment
            ));

            if (!$investment["id"]) {
                return $this->error("This investment does not exist!");
            }

            if ($investment["maxInvest"] < $money) {
                return $this->error("You can invest a maximum of " . $this->money($investment["maxInvest"]) . " in " . $investment["name"]);
            }

            $this->user->set("US_money", $this->user->info->US_money - $money);
            $this->user->set("US_invest", $money);
            $this->user->set("US_investment", $investment["id"]);
            $this->user->updateTimer("investment", $investment["time"], 1);

            $this->error("You have invested " . $this->money($money) . "!", "success");
            
        }
        
    }

?>