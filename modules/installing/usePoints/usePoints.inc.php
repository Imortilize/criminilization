<?php

    class usePoints extends module {

        public $allowedMethods = array(
            'item'=>array('type'=>'post'),
            'user'=>array('type'=>'post'),
            'points'=>array('type'=>'post'),
            'password'=>array('type'=>'post'),
            'submit'=>array('type'=>'post'),
            'qty'=>array('type'=>'post')
        );

        public function getItems() {

            $setting = new Settings();

            $items = array();

            $items[] = array(
                "type" => "cash",
                "id" => count($items),
                "name" => "$50,000", 
                "cost" => $setting->loadSetting("pointsCashCost", true, 5)
            );

            $items[] = array(
                "type" => "bullets",
                "id" => count($items),
                "name" => "250 Bullets", 
                "cost" => $setting->loadSetting("pointsBulletsCost", true, 25)
            );

            $items[] = array(
                "type" => "health",
                "max" => 1,
                "id" => count($items),
                "name" => "Full Health", 
                "cost" => $setting->loadSetting("pointsHealthCost", true, 15)
            );

            

            $carItems = $this->db->selectAll("SELECT * FROM cars WHERE CA_points");

            foreach ($carItems as $key => $value) {
                $items[] = array(
                    "type" => "car",
                    "id" => count($items),
                    "car" => $value["CA_id"], 
                    "name" => $value["CA_name"], 
                    "cost" => $value["CA_points"]
                );
            }

            $inv = $this->db->selectAll("
                SELECT * FROM itemMeta INNER JOIN items ON (I_id = IM_item) WHERE IM_meta = 'pointShopCost' AND IM_value
            ");

            foreach ($inv as $key => $value) {
                $items[] = array(
                    "type" => "item",
                    "id" => count($items),
                    "item" => $value["I_id"], 
                    "name" => $value["I_name"], 
                    "cost" => $value["IM_value"]
                );
            }

            return $items;
        }

        public function method_transfer() {

            $pointsName = _setting("pointsName");

            if (!isset($this->methodData->user)) {
                return $this->error("This user does not exist");
            }

            if (!isset($this->methodData->points)) {
                return $this->error("How many $pointsName do you want to send?");
            }

            if (!isset($this->methodData->password)) {
                return $this->error("Please enter your password");
            }

            $user = new User(null, $this->methodData->user);

            if (!isset($user->info->U_id)) {
                return $this->error("This user does not exist");
            }

            if ($user->info->U_id == $this->user->id) {
                return $this->error("You cant send $pointsName to yourself!");
            }

            $points = abs(intval($this->methodData->points));

            if (!$points) {
                return $this->error("How many $pointsName do you want to send?");
            }
            
            if ($points > $this->user->info->US_points) {
                return $this->error("You dont have that many $pointsName");
            }

            $password = $this->user->encrypt($this->user->id . $this->methodData->password);

            if ($this->user->info->U_password != $password) {
                return $this->error("Incorrect password");
            }

            $user->set("US_points", $user->info->US_points + $points);
            $this->user->set("US_points", $this->user->info->US_points - $points);
            $user->newNotification(htmlentities($this->user->info->U_name) . " has sent you $points $pointsName");

            $this->error("You have sent $points $pointsName to " . htmlentities($user->info->U_name), "success");

        }

        public function method_buy() {

            if (isset($this->methodData->item)) {
                $item = $this->getItems()[$this->methodData->item]; 

                $qty = abs(intval($this->methodData->qty));

                if (!$qty) {
                    return $this->error("Please enter the quantity that you would like to buy!");
                }
                
                if ($item["type"] == "health") $qty = 1;

                $cost = $item["cost"] * $qty;

                $pointsName = _setting("pointsName");
                
                if ($cost > $this->user->info->US_points) {
                    return $this->error('You dont have enough ' . $pointsName . ' to buy this!');
                }

                $this->user->set("US_points", $this->user->info->US_points - $cost);


                switch ($item["type"]) {
                    case "cash":
                        $this->user->set("US_money", $this->user->info->US_money + ($qty * 50000));
                        $this->error('You paid ' . $cost . ' ' . $pointsName . ' for ' . $this->money($qty * 50000), "success");
                    break;
                    case "item":
                        $i = new Items();
                        $itm = $i->getItem($item["item"]);
                        $this->user->addItem($item["item"], $qty);
                        $this->error('You paid ' . $cost . ' ' . $pointsName . ' for '.$qty.'x ' . $itm["name"], "success");
                    break;
                    case "bullets":
                        $this->user->set("US_bullets", $this->user->info->US_bullets + ($qty * 250));
                        $this->error('You paid ' . $cost . ' ' . $pointsName . ' for ' . number_format($qty * 250) . ' bullets', "success");
                    break;
                    case "health":
                        $this->user->set("US_health", 0);
                        $this->error('You paid ' . $cost . ' ' . $pointsName . ' for full health', "success");
                    break;
                    case "car":
                        $i = 0;
                        while ($i < $qty) {
                            $insert = $this->db->prepare("
                                INSERT INTO garage (GA_uid, GA_car, GA_damage, GA_location) VALUES (:uid, :car, 0, :loc)
                            ");
                            $insert->bindParam(':uid', $this->user->info->US_id);
                            $insert->bindParam(':loc', $this->user->info->US_location);
                            $insert->bindParam(':car', $item["car"]);
                            $insert->execute();
                            $i++;
                        }
                        $this->error('You paid ' . $cost . ' {_setting "pointsName"} for ' . $qty . ' x ' . $item["name"], "success");
                    break;
                }


            }

        }

        public function constructModule() {
            $items = $this->getItems();
            $this->html .= $this->page->buildElement("shop", array(
                "items" => $items
            ));
        }

    }

?>