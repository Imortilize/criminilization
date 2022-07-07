<?php

    class gangGarage extends module {
        
        public $allowedMethods = array('car'=>array('type'=>'request'));
        
        public $pageName = 'Garage';
        
        public function getCars($id) {

            $garage = $this->db->selectAll("
                SELECT * from garage 
                INNER JOIN locations ON (L_id = GA_location) 
                INNER JOIN cars ON (CA_id = GA_car) 
                WHERE GA_uid = :uid", array(
                    ':uid' => $id
                )); 
            $cars = array();

            $g = new Gang($this->user->info->US_gang);
            $canTake = $g->can("takeCar");
            
            foreach ($garage as $car) {
                
                $multi = (100 - $car["GA_damage"]) /100;
                $value = round(($car["CA_value"] * $multi));   
                
                $cars[] = array(
                    "name" => $car["CA_name"], 
                    "canTake" => $canTake, 
                    "location" => $car["L_name"], 
                    "damage" => (100 - $car["GA_damage"]), 
                    "id" => $car["GA_id"], 
                    "value" => $value
                );
                
            }

            return $cars;

        }

        public function constructModule() {

            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }

            $g = new Gang($this->user->info->US_gang);
            
            $max = $g->gang["level"] * 5 + 5;

            $gangCars = $this->getCars((0 - $this->user->info->US_gang));

            $this->html .= $this->page->buildElement('garage', array(
                "max" => $max,
                "cars" => $this->getCars($this->user->info->US_id),
                "gangCars" => $gangCars,
                "usedSpace" => count($gangCars)
            ));
            
        }
        
        public function method_park() {

            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }
            
            if (isset($this->methodData->car)) {
                $id = $this->methodData->car;
            } else {
                return;
            }

            $g = new Gang($this->user->info->US_gang);

            $max = $g->gang["level"] * 5 + 5;

            $gangCars = $this->getCars((0 - $this->user->info->US_gang));
            
            if (count($gangCars) >= $max) {
                return $this->error("Your garage is full!");
            }

            $car = $this->db->select("
                SELECT * 
                FROM garage 
                INNER JOIN cars ON (CA_id = GA_car) 
                WHERE GA_id = :car
            ", array(
                ':car' => $id
            ));

            if (empty($car) || $car["GA_uid"] != $this->user->id) {
                return $this->error('You dont own this car or it does not exist!');
            }
                
            $this->db->update("UPDATE garage SET GA_uid = :gang WHERE GA_id = :id", array(
                ":id" => $car["GA_id"],
                ":gang" => 0 - $this->user->info->US_gang,
            ));
            
            $this->error("You parked your car in the garage!", "success");

            $g->log("Parked a " . $car["CA_name"]);

            $actionHook = new hook("userAction");
            $action = array(
                "user" => $this->user->id, 
                "module" => "gangGarage.park", 
                "id" => $car["GA_id"], 
                "success" => true, 
                "reward" => 0
            );
            $actionHook->run($action);
            
        }
        
        public function method_take() {

            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }

            $g = new Gang($this->user->info->US_gang);
            if (!$g->can("takeCar")) {
                return $this->error("You dont have permission to do this!");
            } 
            
            if (isset($this->methodData->car)) {
                $id = $this->methodData->car;
            } else {
                return;
            }
            
            $car = $this->db->select("
                SELECT * 
                FROM garage 
                INNER JOIN cars ON (CA_id = GA_car) 
                WHERE GA_id = :car
            ", array(
                ':car' => $id
            ));

            if (empty($car) || $car["GA_uid"] != (0-$this->user->info->US_gang)) {
                return $this->error('You dont own this car or it does not exist!');
            }
                
            $this->db->update("UPDATE garage SET GA_uid = :user WHERE GA_id = :id", array(
                ":id" => $car["GA_id"],
                ":user" => $this->user->info->US_gang,
            ));

            $g->log("Took a " . $car["CA_name"]);
            
            $this->error("You took the car from the garage!", "success");
            
            $actionHook = new hook("userAction");
            $action = array(
                "user" => $this->user->id, 
                "module" => "gangGarage.take", 
                "id" => $car["GA_id"], 
                "success" => true, 
                "reward" => 0
            );
            $actionHook->run($action);
            
        }
        
        
    }

?>