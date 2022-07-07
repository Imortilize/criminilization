<?php

    class travel extends module {

        public $allowedMethods = array('location'=>array('type'=>'get'));

        public $pageName = 'Airport';

        function distance($lat1, $lon1, $lat2, $lon2, $unit = "K") {

            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return round($miles * 1.609344);
            } else if ($unit == "N") {
                return round($miles * 0.8684);
            } else {
                return round($miles);
            }
        }

        public function constructModule() {

            if (!$this->user->checkTimer('travel')) {

                $time = ($this->user->getTimer('travel'));
                $this->html .= $this->page->buildElement('timer', array(
                    "timer" => "travel",
                    "text" => 'You cant travel yet!',
                    "time" => $this->user->getTimer("travel")
                ));

            }

            $locations = $this->db->selectAll("SELECT * from locations WHERE L_id != :loc ORDER BY L_id", array(
                ":loc" => $this->user->info->US_location
            ));

            $current = $this->getLocation($this->user->info->US_location, true);
            $currentDistance = explode('-', $current->L_distance);
            foreach ($locations as $location) {

                $hook = new Hook("alterModuleData");
                $hookData = array(
                    "module" => "travel",
                    "user" => $this->user,
                    "data" => $location
                );
                $location = $hook->run($hookData, 1)["data"];

                $location['distance'] = explode('-', $location['L_distance']);
                $distance = $this->distance(
                    $currentDistance[0], $currentDistance[1],
                    $location['distance'][0], $location['distance'][1]
                );

                $vehicle = $this->db->select("SELECT * FROM vehicles where V_id = :id", array(
                    ":id" => $this->user->info->US_vehicle
                ));
                if (!isset($vehicle['V_id'])) {
                    $vehicle['V_fuel'] = 2;
                    $vehicle['V_range'] = 4;
                    $vehicle['V_units'] = 8;
                    $vehicle['V_max'] = 4000;
                }

                $data[] = array(
                    "location" => $location["L_name"],
                    "cost" => ($distance * $vehicle['V_fuel']),
                    "distance" => $distance,
                    "id" => $location["L_id"],
                    "cooldown" => $vehicle['V_range'],
                    "class" => $vehicle['V_max'] <= $distance ? "danger" : "success",
                );

            }

            $this->html .= $this->page->buildElement('locationHolder', array(
                "locations" => $data
            ));

        }

        public function method_fly() {

            $id = abs(intval($this->methodData->location));

            $location = $this->db->select("SELECT * from locations WHERE L_id = :id ORDER BY L_id", array(
                ':id'=>  $id
            ));
            $vehicle = $this->db->select("SELECT * FROM vehicles where V_id = :id", array(
                ":id" => $this->user->info->US_vehicle
            ));
            if (!isset($vehicle['V_id'])) {
                $vehicle['V_fuel'] = 2;
                $vehicle['V_range'] = 4;
                $vehicle['V_units'] = 8;
                $vehicle['V_max'] = 4000;
            }
            $current = $this->getLocation($this->user->info->US_location, true);
            $currentDistance = explode('-', $current->L_distance);

            $location['distance'] = explode('-', $location['L_distance']);
            $distance = $this->distance(
                $currentDistance[0], $currentDistance[1],
                $location['distance'][0], $location['distance'][1]
            );

            $hook = new Hook("alterModuleData");
            $hookData = array(
                "module" => "travel",
                "user" => $this->user,
                "data" => $location
            );
            $location = $hook->run($hookData, 1)["data"];

            if (!$location){
                return $this->error("This location does not exist!");
            }

            if ($this->user->checkTimer('travel')) {
                if ($location["L_id"] == $this->user->info->US_location) {

                    $this->alerts[] = $this->page->buildElement('error', array("text" => 'You are already in '.$location["L_name"].'!'));

                } else if ($this->user->info->US_money < ($distance * $vehicle['V_fuel'])) {

                    $this->alerts[] = $this->page->buildElement('error', array("text" => 'You cant afford to travel here!'));

                }elseif($distance > $vehicle['V_max']){
                    return $this->error("Your vehicle can not go that range!");
                } else {

                    $this->user->subtract("US_money", ($distance * $vehicle['V_fuel']));
                    $this->user->set("US_location", $location["L_id"]);

                    $this->user->updateTimer('travel', $vehicle['V_range'], true);

                    $actionHook = new hook("userAction");
                    $action = array(
                        "user" => $this->user->id,
                        "module" => "travel",
                        "id" => $id,
                        "success" => true,
                        "reward" => 0
                    );
                    $actionHook->run($action);

                    $this->alerts[] = $this->page->buildElement('success', array("text" => 'You traveled to '.$location["L_name"].' for '.$this->money($location["L_cost"]).'!'));

                }
            }

        }

    }


