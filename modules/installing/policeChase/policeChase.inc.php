<?php

    class policeChase extends module {
        
        public $pageName = 'Police Chase';
        public $allowedMethods = array(
            'car'=>array('type'=>'post'),
            'move'=>array('type'=>'get')
        );
        
        public function constructModule() {
            
            if (!$this->user->checkTimer('chase')) {
                $time = $this->user->getTimer('chase');
                $crimeError = array(
                    "timer" => "chase",
                    "text"=>'You cant attempt another police chase until your timer is up!',
                    "time" =>$this->user->getTimer("chase")
                );
                $this->html .= $this->page->buildElement('timer', $crimeError);
            }

            $car = $this->getCarInfo();

            if ($car) {
                $this->html .= $this->page->buildElement('policeChase', array(
                    "car" => $car
                ));
            } else {

                $cars = $this->db->selectAll("
                    SELECT 
                        CONCAT(CA_name, ' (', GA_damage, '% Damage)') as 'label', 
                        GA_id as 'id'
                    FROM garage 
                    INNER JOIN cars ON (GA_car = CA_id) 
                    WHERE GA_uid = :id AND GA_location = :loc
                ", array(
                    ":loc" => $this->user->info->US_location, 
                    ":id" => $this->user->id
                ));

                $this->html .= $this->page->buildElement('policeChaseSelect', array(
                    "cars" => $cars
                ));
            }
            
        }
        
        public function getCarInfo() {
            if (!$this->user->info->US_pcCar) return false;

            $car = $this->db->select("SELECT * FROM garage INNER JOIN cars ON (GA_car = CA_id) WHERE GA_id = :id", array(
                ":id" => $this->user->info->US_pcCar
            ));

            return $car;

        }

        public function method_select() {
            if ($this->user->checkTimer('chase')) {
                if (isset($this->methodData->car)) {
                    $car = $this->db->select("
                        SELECT *
                        FROM garage 
                        INNER JOIN cars ON (GA_car = CA_id) 
                        WHERE GA_id = :id
                    ", array(
                        ":id" => $this->methodData->car
                    ));
                    
                    if (!$car) {
                        return $this->error("Invalid car selected!");
                    }

                    if ($car["GA_uid"] != $this->user->id) {
                        return $this->error("Invalid car selected!");
                    }

                    if ($car["GA_location"] != $this->user->info->US_location) {
                        return $this->error("Your car is in the wrong location!");
                    }

                    $this->db->update("UPDATE garage SET GA_uid = 0 WHERE GA_id = :id", array(
                        ":id" => $car['GA_id']
                    ));

                    $this->user->set("US_pcCar", $car["GA_id"]);

                }
            }
        }

        public function giveCarBack() {
            $id = $this->user->info->US_pcCar;
            $this->db->update("UPDATE garage SET GA_uid = :user WHERE GA_id = :id", array(
                ":id" => $id, 
                ":user" => $this->user->id
            ));
            $this->user->set("US_pcCar", 0);
        }

        public function method_move() {

            //if (!$this->checkCSFRToken()) return;
            

            if ($this->user->checkTimer('chase')) {

                $car = $this->getCarInfo();

                $chance = mt_rand(1, 100);

                $success = $car["CA_pcSuccess"];
                $fail = $car["CA_pcSuccess"] + $car["CA_pcFail"];


                $settings = new Settings();

                $minReward = $settings->loadSetting("pcMinReward", true, 500);
                $maxReward = $settings->loadSetting("pcMaxReward", true, 1000);
                $cooldown = $settings->loadSetting("pcCooldown", true, 300);
                $JailTime = $settings->loadSetting("pcJailTime", true, 120);
                $minDamage = $settings->loadSetting("pcMinDamage", true, 1);
                $maxDamage = $settings->loadSetting("pcMaxDamage", true, 3);
                $expGain = $settings->loadSetting("pcExpGain", true, 3);

                $damage = mt_rand($minDamage, $maxDamage);

                $newDamage = $car["GA_damage"] + $damage;


                $id = $this->user->info->US_pcCar;
                $this->db->update("UPDATE garage SET GA_damage = :damage WHERE GA_id = :id", array(
                    ":id" => $id, 
                    ":damage" => $newDamage
                ));

                $damageText = "";
                if ($damage ) {
                    $damageText = ", your car took ".$damage."% damage";
                }

                if ($newDamage >= 100) {
                    $this->error("You crashed your car and totaled it!");
                
                    $this->user->updateTimer('jail', $JailTime, true);
                    $this->user->updateTimer('chase', $cooldown, true);
                    $this->user->set("US_pcCar", 0);
                    

                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "chase", 
                        "id" => 0, 
                        "success" => false, 
                        "reward" => 0
                    );
                    $actionHook->run($action);

                } else if ($chance <= $success) {
                
                    $prize = mt_rand($minReward, $maxReward) * $this->user->info->US_rank;

                    $this->user->set("US_money", $this->user->info->US_money + $winnings);
                    if ($expGain) $this->user->set("US_exp", $this->user->info->US_exp + $expGain);
                    
                    $this->user->updateTimer('chase', $cooldown, true);
                    
                    $this->error('You got away, you were paid '.$this->money($prize).''.$damageText.'!', "success");

                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "chase", 
                        "id" => 0, 
                        "success" => true, 
                        "reward" => $winnings
                    );
                    $actionHook->run($action);
                    $this->giveCarBack();
                    
                } else if ($chance <= $fail) {
                                    
                    $this->user->updateTimer('jail', $JailTime, true);
                    $this->user->updateTimer('chase', $cooldown, true);
                    
                    $this->error('You were caught and was sent to jail'.$damageText.'!');

                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id, 
                        "module" => "chase", 
                        "id" => 0, 
                        "success" => false, 
                        "reward" => 0
                    );
                    $actionHook->run($action);
                    $this->giveCarBack();
                    
                } else {
                    
                    $this->error('You are still going, what direction do you want to go now'.$damageText.'?', "info");
                
                }
                
            }
            
        }
        
    }

