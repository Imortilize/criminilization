<?php

    class travel extends module {

        public $allowedMethods = array(
            'location' => array('type' => 'GET')
        );

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

            // Grab the timer and check if the user has a travel cooldown
            $time = $this->user->getTimer('travel');
            $hasTravelCooldown = !$this->user->checkTimer('travel');

            // If the user has a travel cooldown active and has an alert active, don't
            // show the travel panel, to avoid stacked up panels
            if ($hasTravelCooldown) {
				
				if (count($this->alerts) > 0) {
					return;
				}
            } 

            // Grab all the locations
            $locations = $this->db->selectAll("SELECT * from locations WHERE L_id != :loc ORDER BY L_id", array(
                ":loc" => $this->user->info->US_location
            ));

            // Grab the user's current location
            $currentLocationIndex = $this->user->info->US_location;
            $currentLocation = $this->getLocation($currentLocationIndex, true);
            $currentDistance = explode(',', $currentLocation->L_distance);

            // Grab the user's current vehicle
            $vehicle = $this->db->select("SELECT * FROM vehicles where V_id = :id", array(
                ":id" => $this->user->info->US_vehicle
            ));
            
            if (!isset($vehicle['V_id'])) {
                $vehicle['V_fuel'] = 2;
                $vehicle['V_range'] = 4;
                $vehicle['V_units'] = 8;
                $vehicle['V_max'] = 4000;
            }

            $vehicleName = $vehicle['V_name'];
            $vehicleFuelCost = $vehicle['V_fuel'];
            $maxVehicleDistance = $vehicle['V_max'];
            $travelCooldown = $vehicle['V_range'];

            // Process the locations
            $reachableLocations = null;
            $unreachableLocations = null;
            foreach ($locations as $location) {

                $locationId = $location['L_id'];
                $locationName = $location["L_name"];
                $locationDistance = $location['L_distance'];

                /*$hook = new Hook("alterModuleData");
                $hookData = array(
                    "module" => "travel",
                    "user" => $this->user,
                    "data" => $location
                );
                $location = $hook->run($hookData, 1)["data"];*/

                $location['distance'] = explode(',', $locationDistance);
                $distance = $this->distance(
                    $currentDistance[0], $currentDistance[1],
                    $location['distance'][0], $location['distance'][1]
                );

                // Calculate the travel cost
                $userMoney = $this->user->info->US_money;
                $travelCost = ($distance * $vehicleFuelCost);
                $canAffordToTravel = ($userMoney >= $travelCost);
                
                if ($maxVehicleDistance >= $distance) {
                    $reachableLocations[] = array(
                        "location" => $locationName,
                        "cost" => $travelCost,
                        "distance" => $distance,
                        "id" => $locationId,
                        "cooldown" => $travelCooldown,
                        "canAffordToTravel" => $canAffordToTravel,
                        "hasTravelCooldown" => $hasTravelCooldown,
                    );
                } else {
                    $unreachableLocations[] = array(
                        "location" => $locationName,
                        "cost" => $travelCost,
                        "distance" => $distance,
                        "id" => $locationId,
                        "cooldown" => $travelCooldown,
                        "canAffordToTravel" => $canAffordToTravel,
                        "hasTravelCooldown" => $hasTravelCooldown,
                    );
                }  
            }

            $this->html .= $this->page->buildElement('locationHolder', array(
                "reachableLocations" => $reachableLocations,
                "unreachableLocations" => $unreachableLocations,
                "vehicleName" => $vehicleName,
                "vehicleDistance" => $maxVehicleDistance,
                "hasTravelCooldown" => $hasTravelCooldown,
                "travelTime" => $time,
                "currentLocation" => $currentLocation->L_name
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
            $currentDistance = explode(',', $current->L_distance);

            $location['distance'] = explode(',', $location['L_distance']);
            $distance = $this->distance(
                $currentDistance[0], $currentDistance[1],
                $location['distance'][0], $location['distance'][1]
            );

            /*$hook = new Hook("alterModuleData");
            $hookData = array(
                "module" => "travel",
                "user" => $this->user,
                "data" => $location
            );
            $location = $hook->run($hookData, 1)["data"];*/

            if (!$location) {
                return $this->error("This location does not exist!");
            }

            if ($this->user->checkTimer('travel')) {

                $currentLocationId = $this->user->info->US_location;
                $newlocationId = $location["L_id"];

                // Check we aren't trying to travel to the same location
                if ($newlocationId == $currentLocationId) {
                    return;
                } 
      
                $vehicleFuel = $vehicle['V_fuel'];
                $travelCost = ($distance * $vehicleFuel);
                
                // Check we have enough money to travel
                if ($this->user->info->US_money < $travelCost) {
                    return;
                }

                // Check we can travel the distance
                $vehicleTravelDistance = $vehicle['V_max'];
                if ($distance > $vehicleTravelDistance) {
                    return;
                } 

                $newlocationName = $location["L_name"];
                $vehicleTravelTime = $vehicle['V_range'];

                // Set user stats and show feedback                
                $this->user->subtract("US_money", $travelCost);
                $this->user->set("US_location", $newlocationId);
                $this->user->updateTimer('travel', $vehicleTravelTime, true);

                $actionHook = new hook("userAction");
                $action = array(
                    "user" => $this->user->id,
                    "module" => "travel",
                    "id" => $id,
                    "success" => true,
                    "reward" => 0
                );
                $actionHook->run($action);

                // Display the crime success alert
                $this->alerts[] = $this->page->buildElement('locationChanged', array(
                    "location" => $newlocationName, 
                    "cost" => $travelCost
                ));
            }
        }
    }