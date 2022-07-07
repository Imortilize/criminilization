<?php

    class gangCrimes extends module {
        
        public $allowedMethods = array('crime'=>array('type'=>'get'), 'user'=>array('type'=>'get'));
        
        public function constructModule() {
                
            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }

            $g = new Gang($this->user->info->US_gang);
            
            $crimes = $this->db->selectAll("
                SELECT * FROM gangCrimes WHERE GC_level <= :level
            ", array(
                ':level' => $g->gang["level"]
            ));
            
            $crimeInfo = array();
            
            foreach ($crimes as $crime) {
                $crimeID = $crime["GC_id"];
                $crimeInfo[] = array(
                    "name" => $crime["GC_name"],
                    "cooldown" => $this->timeLeft($crime["GC_cooldown"]),
                    "percent" => $crime["GC_chance"], 
                    "id" => $crimeID
                );
            }
            
            if (!$this->user->checkTimer('gangCrimes')) {
                $time = $this->user->getTimer('gangCrimes');
                $crimeError = array(
                    "timer" => "gangCrimes",
                    "text"=>'You can\'t commit another gang crimes untill your timer is up!',
                    "time" => $this->user->getTimer("gangCrimes")
                );
                $this->html .= $this->page->buildElement('timer', $crimeError);
            }

            $this->html .= $this->page->buildElement('crimeHolder', array(
                "crimes" => $crimeInfo
            ));
        }
        
        public function method_reset() {

            $this->construct = false;

            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }

            $g = new Gang($this->user->info->US_gang);
            if (!$g->can("takeCar")) {
                return $this->error("You dont have permission to do this!");
            }

            $u = new User($this->methodData->user);

            if ($u->info->US_gang != $this->user->info->US_gang) {
                $this->error("This user is not part of your gang!");
            } else {
                $this->error("Income reset!", "success");
                $u->set("US_gangCash", 0);
                $u->set("US_gangBullets", 0);
            }

            $this->method_income();

        }
        
        public function method_income() {

            $this->construct = false;

            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }

            $g = new Gang($this->user->info->US_gang);
            if (!$g->can("takeCar")) {
                return $this->error("You dont have permission to do this!");
            }

            $members = $g->gang["members"];

            foreach ($members as $key => $value) {

                $u = new User($value["user"]["id"]);

                $value["cash"] = $u->info->US_gangCash;
                $value["bullets"] = $u->info->US_gangBullets;

                $members[$key] = $value;
            }

            $this->html .= $this->page->buildElement('memberIncome', array(
                "members" => $members
            ));



        }

        public function method_commit() {
                
            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }

            $g = new Gang($this->user->info->US_gang);
            
            $id = abs(intval($this->methodData->crime)); 
            
            if ($this->user->checkTimer('gangCrimes')) {
                
                $chance = mt_rand(1, 100);
                $jailChance = mt_rand(0, 1);
                $crimeID = $id;
                
                $crimeInfo = $this->db->select("SELECT * FROM gangCrimes WHERE GC_id = :crime", array(
                    ':crime' => $crimeID
                ));
             
                if (!$crimeInfo){ 
                    return $this->error("This crime does not exist!"); 
                }

                if ($crimeInfo["GC_level"] > $g->gang["level"]) {
                    return $this->error("You cant commit this crime yet!");
                }

                $userChance = $crimeInfo["GC_chance"];
                $cashReward = mt_rand($crimeInfo["GC_money"], $crimeInfo["GC_maxMoney"]);
                $bulletReward = mt_rand($crimeInfo["GC_bullets"], $crimeInfo["GC_maxBullets"]);
                
                if ($chance > $userChance && $jailChance) {

                    $this->error('You failed to commit the crime, you were caught and sent to jail!');

                    $this->user->updateTimer('jail', mt_rand(30, 90), true);
     
                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "gangCrime", 
                        "id" => $crimeID, 
                        "success" => false, 
                        "reward" => 0
                    );
                    $actionHook->run($action);

                } else if ($chance > $userChance) {
                    $this->error('You failed to commit the crime!');
    
                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "gangCrime", 
                        "id" => $crimeID, 
                        "success" => false, 
                        "reward" => 0
                    );
                    $actionHook->run($action);

                } else {

                    $rewards = array();

                    if ($cashReward) $rewards[] = $this->money($cashReward);
                    if ($bulletReward) $rewards[] = number_format($bulletReward) . ' bullets';

                    $this->error(
                        'You successfuly commited the crime and earned '. implode(" and ", $rewards) .'!', 
                        'success'
                    );

                    $this->db->update("
                        UPDATE gangs SET G_money = G_money + :money, G_bullets = G_bullets + :bullets WHERE G_id = :id
                    ", array(
                        ":id" => $this->user->info->US_gang, 
                        ":money" => $cashReward,
                        ":bullets" => $bulletReward
                    ));

                    $this->user->set("US_gangCash", $this->user->info->US_gangCash + $cashReward);
                    $this->user->set("US_gangBullets", $this->user->info->US_gangBullets + $bulletReward);
     
                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "gangCrime", 
                        "id" => $crimeID, 
                        "success" => true, 
                        "reward" => $cashReward
                    );
                    $actionHook->run($action);

                }
                
                $this->user->updateTimer('gangCrimes', $crimeInfo["GC_cooldown"], true);
                
            }
        
        }
        
    }

?>
