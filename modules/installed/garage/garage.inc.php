<?php

    class garage extends module {
        
        public $allowedMethods = array(
            'id'=>array('type'=>'post'),
            'filterDamage'=>array('type'=>'post'),
            'filterLocation'=>array('type'=>'post'),
            'location'=>array('type'=>'post')
        );
        
        public $pageName = 'Garage';

        public $filterLocation = "*";
        public $filterDamage = "*";
        
        public function constructModule() {
            
            $add = "";

            if ($this->filterLocation != "*") {
                $add = " AND GA_location = :location";
            } 

            if ($this->filterDamage == "0") {
                $add .= " AND GA_damage > 0";
            } else if ($this->filterDamage == "1") {
                $add .= " AND GA_damage = 0";
            } 

            $garage = $this->db->prepare("
                SELECT * FROM garage 
                INNER JOIN cars ON (CA_id = GA_car) 
                WHERE GA_uid = :uid " . $add . "
            ");
            $garage->bindParam(':uid', $this->user->info->US_id);
            if ($this->filterLocation != "*") {
                $garage->bindParam(":location", $this->filterLocation);
            }
            $garage->execute();

            $cars = array();
            
            while ($car = $garage->fetchObject()) {
            
                $loc = $this->db->prepare("SELECT * FROM locations WHERE L_id = ".$car->GA_location);
                $loc->execute();
                $loc = $loc->fetchObject();
                
                $multi = (100 - $car->GA_damage) /100;
                $value = round(($car->CA_value * $multi));   
                
                $cars[] = array(
                    "type" => $car->CA_id, 
                    "name" => $car->CA_name, 
                    "location" => $loc->L_name, 
                    "damage" => $car->GA_damage.'%', 
                    "id" => $car->GA_id, 
                    "value" => $value
                );
                
            }
            
            $locations = $this->db->prepare("SELECT L_id as 'id', L_name as 'name', L_cost as 'cost' FROM locations");
            $locations->execute();
            $locations = $locations->fetchAll(PDO::FETCH_ASSOC);

            $damageFilters = array(
                array(
                    "id" => "1", 
                    "name" => "Fully Repaired"
                ),
                array(
                    "id" => "0", 
                    "name" => "Damaged"
                )
            );

            foreach ($locations as $key => $value) {
                $locations[$key]["selected"] = $value["id"] === $this->filterLocation;
            }

            foreach ($damageFilters as $key => $value) {
                $damageFilters[$key]["selected"] = $value["id"] === $this->filterDamage;
            }

            $this->html .= $this->page->buildElement('garage', array(
                "damageFilters" => $damageFilters,
                "locations" => $locations,
                "cars" => $cars
            ));
            
        }
        
        public function method_filter() {
            if (isset($this->methodData->filterLocation)) {
                $this->filterLocation = $this->methodData->filterLocation;
            }
            if (isset($this->methodData->filterDamage)) {
                $this->filterDamage = $this->methodData->filterDamage;
            }
        }

        public function method_sell() {

            $ids = $this->methodData->id;

            $sold = 0;
            $cash = 0;
            
            foreach ($ids as $id) {
                
                $car = $this->db->prepare("SELECT * FROM garage INNER JOIN cars ON (CA_id = GA_car) WHERE GA_id = :car");
                $car->bindParam(':car', $id);
                $car->execute();
                $car = $car->fetchObject();
                
                if (empty($car) || $car->GA_uid != $this->user->id) {
                    
                    $this->alerts[] = $this->page->buildElement('error', array("text"=>'You dont own this car or it does not exist!'));
                
                } else {
                    
                    $this->db->query("DELETE FROM garage WHERE GA_id = " . $car->GA_id);
                    $multi = (100 - $car->GA_damage) /100;
                    $value = round(($car->CA_value * $multi));   
                    
                    $sold++;
                    $cash += $value;

                }
            }
            $this->user->set("US_money", $this->user->info->US_money + $cash);
            $this->alerts[] = $this->page->buildElement('success', array("text"=>'You sold '.$sold.' cars for '.$this->money($cash).'!'));
            
        }
        
        public function method_crush() {

            $ids = $this->methodData->id;
            
            $settings = new Settings();

            $crushed = 0;
            $bullets = 0;

            foreach ($ids as $id) {
                
                $car = $this->db->prepare("SELECT * FROM garage INNER JOIN cars ON (CA_id = GA_car) WHERE GA_id = :car");
                $car->bindParam(':car', $id);
                $car->execute();
                $car = $car->fetchObject();
                
                if (empty($car) || $car->GA_uid != $this->user->id) {
                    
                    $this->alerts[] = $this->page->buildElement('error', array("text"=>'You dont own this car or it does not exist!'));
                
                } else {
                    $this->db->query("DELETE FROM garage WHERE GA_id = " . $car->GA_id);
                    $multi = (100 - $car->GA_damage) / 100;
                    $value = round($car->CA_value * $multi / 1000 * $settings->loadSetting("crushBullets", true, 65));   
                    
                    $crushed++;
                    $bullets += $value;
                
                }
            }

            $actionHook = new hook("userAction");
            $action = array(
                "user" => $this->user->id, 
                "module" => "bullets", 
                "id" => 2, 
                "success" => true, 
                "reward" => $bullets
            );
            $actionHook->run($action);
                    
            $this->user->set("US_bullets", $this->user->info->US_bullets + $bullets);
            $this->alerts[] = $this->page->buildElement('success', array("text"=>'You crushed '.$crushed.' cars for '.number_format($bullets).' bullets!'));
            
        }
        
        public function method_repair() {

            $ids = $this->methodData->id;

            $repaired = 0;
            $cost = 0;
            
            foreach ($ids as $id) {
                
                $car = $this->db->prepare("SELECT * FROM garage INNER JOIN cars ON (CA_id = GA_car) WHERE GA_id = :car");
                $car->bindParam(':car', $id);
                $car->execute();
                $car = $car->fetchObject();
                
                if (empty($car) || $car->GA_uid != $this->user->id) {
                    
                    $this->alerts[] = $this->page->buildElement('error', array("text"=>'You dont own this car or it does not exist!'));
                
                } else {
                    
                    $multi = $car->GA_damage / 100;
                    if ($multi > 0.2) {
                        $multi = $multi - 0.1;
                    }
                    
                    $value = round(($car->CA_value * $multi)); 
                    
                    if ($value < $this->user->info->US_money) {
                    
                     
                        $repaired++;
                        $cost += $value;
                        $this->db->query("UPDATE garage SET GA_damage = 0 WHERE GA_id = " . $car->GA_id);
                        $this->user->set("US_money", $this->user->info->US_money - $value);                    
                    } else {
                    
                        $this->alerts[] = $this->page->buildElement('error', array("text"=>'You do not have enough money to do this, you need '.$this->money($value).'!'));
                        
                    }
            
                }
            }
            
            $this->alerts[] = $this->page->buildElement('success', array("text"=>'You repaired '.$repaired.' cars for '.$this->money($cost).'!'));
        }
        
        public function method_move() {

            $ids = $this->methodData->id;

            $moved = 0;
            $cost = 0;

            foreach ($ids as $id) {
                
                $car = $this->db->prepare("SELECT * FROM garage INNER JOIN cars ON (CA_id = GA_car) WHERE GA_id = :car");
                $car->bindParam(':car', $id);
                $car->execute();
                $car = $car->fetchObject();
                
                if (empty($car) || $car->GA_uid != $this->user->id) {
                    
                    $this->alerts[] = $this->page->buildElement('error', 
                        array("text"=>'You dont own this car or it does not exist!')
                    );
                
                } else {
                    
                    $multi = $car->GA_damage / 100;

                    $location = $this->db->prepare("SELECT * FROM locations WHERE L_id = :id");
                    $location->bindParam(":id", $this->methodData->location);
                    $location->execute();
                    $location = $location->fetch(PDO::FETCH_ASSOC);

                    $value = $location["L_cost"]; 
                    
                    if ($value < $this->user->info->US_money) {
                        $cost += $value;
                        $moved++;

                        $this->db->query("UPDATE garage SET GA_location = " . $location["L_id"] . " WHERE GA_id = " . $car->GA_id);
                        $this->user->set("US_money", $this->user->info->US_money - $value);
                    } else {
                    
                        $this->alerts[] = $this->page->buildElement('error', array(
                            "text"=>'You do not have enough money to do this, you need '.$this->money($value).'!'
                        ));
                        
                    }
            
                }
            }
            
            $this->alerts[] = $this->page->buildElement(
                'success', array("text"=>'You moved '.$moved.' cars to ' . $location["L_name"] . ' for '.$this->money($cost).'!')
            );
            
        }
        
    }

?>