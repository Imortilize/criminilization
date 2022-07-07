<?php

    class auctionHouse extends module {
        
        public $allowedMethods = array(
            "type" => array("type" => "GET"), 
            "bid" => array("type" => "POST"), 
            "auction" => array("type" => "REQUEST"), 
            "id" => array("type" => "REQUEST"), 
            "price" => array("type" => "POST"), 
            "buyNowPrice" => array("type" => "POST"), 
            "length" => array("type" => "POST"), 
        );
        
        public $pageName = '';

        public function getAuctions() {

            $add = "";

            if (isset($this->methodData->type)) {
                $add = "AND A_type = :type";
            }

            $auctions = $this->db->prepare("
                SELECT
                    A_id as 'id',
                    A_user as 'owner', 
                    A_bid as 'currentBid',
                    A_buyNow as 'buyNow',
                    A_type as 'type',
                    A_qty as 'qty',
                    A_highestBidder as 'currentBidder',
                    A_start as 'start', 
                    A_start + (A_length * 3600) as 'end'
                FROM 
                    auctions 
                WHERE
                    A_paidOut = 0
                    " . $add . "
                ORDER BY end ASC
            ");    

            if (isset($this->methodData->type)) {
                $auctions->bindParam(":type", $this->methodData->type);
            }

            $auctions->execute();
            $auctions = $auctions->fetchAll(PDO::FETCH_ASSOC);

            foreach ($auctions as $key => $value) {

                $seller = new User($value["owner"]);

                if ($value["currentBidder"]) {
                    $buyer = new User($value["currentBidder"]);
                    $value["user"] = $buyer->user;
                } else {
                    $value["user"] = false;
                }

                $value["isOwner"] = ($value["owner"] == $this->user->id);

                $value["item"] = $this->getItemName($value);

                if ($value["type"] == "bullets" || $value["type"] == "points") {
                    $value["pp"] = $value["currentBid"] / $value["qty"];
                    $value["ppBuy"] = $value["buyNow"] / $value["qty"];
                }

                $auctions[$key] = $value;
            }

            return $auctions;
        }

        public function getItemName($auction) {
            $items = new Items();
            switch ($auction["type"]) {
                case "item":
                    $item = $items->getItem($auction["qty"]);
                    return array(
                        "name" => $item["name"]
                    );
                break;
                case "bullets":
                    return array(
                        "name" => number_format($auction["qty"]) . " Bullets"
                    );
                break;
                case "points":
                    return array(
                        "name" => number_format($auction["qty"]) . ' ' . _setting("pointsName")
                    );
                break;
                case "car":
                    return $this->getCarDetail($auction["qty"]);
                break;
                case "property":
                    return $this->getPropertyDetail($auction["qty"]);
                break;
             }
        }

        public function getPropertyDetail($id) {
            $prop = $this->db->prepare("
                SELECT * FROM properties 
                INNER JOIN locations ON (PR_location = L_id)  
                WHERE PR_id = :id
            ");
            $prop->bindParam(":id", $id);
            $prop->execute();
            $prop = $prop->fetch(PDO::FETCH_ASSOC);

            return array(
                "name" => $this->page->modules[$prop["PR_module"]]["name"], 
                "extra" => $prop["L_name"],
                "user" => $prop["PR_user"]
            );
        }

        public function getCarDetail($id) {
            $car = $this->db->prepare("
                SELECT 
                    *
                FROM garage 
                INNER JOIN locations ON (GA_location = L_id) 
                INNER JOIN cars ON (GA_car = CA_id) 
                WHERE GA_id = :id
            ");
            $car->bindParam(":id", $id);
            $car->execute();
            $car = $car->fetch(PDO::FETCH_ASSOC);

            return array(
                "name" => $car["CA_name"], 
                "extra" => $car["GA_damage"]."% Damage\r\n".$car["L_name"],
                "user" => $car["GA_uid"]
            );
        }
        
        public function constructModule() {

            if (isset($this->methodData->bid) && isset($this->methodData->auction)) {
                $auction = $this->db->prepare("
                    SELECT
                        A_id as 'id',
                        A_user as 'owner', 
                        A_bid as 'currentBid',
                        A_buyNow as 'buyNow',
                        A_type as 'type',
                        A_qty as 'qty',
                        A_highestBidder as 'currentBidder',
                        A_start as 'start', 
                        A_start + (A_length * 3600) as 'end'
                    FROM 
                        auctions 
                    WHERE
                        A_id = :id
                    ORDER BY end ASC
                ");    
                $auction->bindParam(":id", $this->methodData->auction);
                $auction->execute();
                $auction = $auction->fetch(PDO::FETCH_ASSOC);

                $bid = $this->methodData->bid;

                if ($auction["buyNow"] > 0 && $bid > $auction["buyNow"]) {
                    $bid = $auction["buyNow"];
                }

                if ($this->user->info->US_money < $bid) {
                    $this->error("You do not have enough money to cover this bid!");
                } else if ($auction["currentBid"] >= $bid) {
                    $this->error("Please enter a higher bid!");
                } else if ($auction["owner"] == $this->user->id) {
                    $this->error("You cant bid on your own item!");
                } else if ($auction["end"] < time()) {
                    $this->error("This auction is closed!");
                } else if ($auction["currentBidder"] == $this->user->id) {
                    $this->error("You are the highest bidder already!");
                } else {

                    if ($auction["currentBidder"]) {
                        $outBid = new User($auction["currentBidder"]);
                        $outBid->set("US_money", $outBid->info->US_money + $auction["currentBid"]);
                        $outBid->newNotification("You were outbid, your money has been returned to you.");
                    }

                    $this->user->set("US_money", $this->user->info->US_money - $bid);

                    $add = "";

                    if ($bid == $auction["buyNow"]) {
                        $add = ", A_start = 0";
                    }

                    $update = $this->db->prepare("
                        UPDATE auctions SET A_bid = :bid, A_highestBidder = :u" . $add . " WHERE A_id = :id
                    ");

                    $update->bindParam(":bid", $bid);
                    $update->bindParam(":u", $this->user->id);
                    $update->bindParam(":id", $auction["id"]);
                    $update->execute();

                    $this->error("Bid placed!", "success");
                }

            }

            $this->html .= $this->page->buildElement("auctions", array(
                "auctions" => $this->getAuctions()
            ));
        }

        public function method_remove() {

            if (!isset($this->methodData->id)) {
                return $this->error("Invalid auction");
            }

            $auction = $this->db->prepare("
                SELECT
                    A_id as 'id',
                    A_user as 'owner', 
                    A_bid as 'currentBid',
                    A_buyNow as 'buyNow',
                    A_type as 'type',
                    A_qty as 'qty',
                    A_highestBidder as 'currentBidder',
                    A_start as 'start', 
                    A_start + (A_length * 3600) as 'end'
                FROM 
                    auctions 
                WHERE
                    A_id = :id
                ORDER BY end ASC
            ");    
            $auction->bindParam(":id", $this->methodData->id);
            $auction->execute();
            $auction = $auction->fetch(PDO::FETCH_ASSOC);

            if (!isset($auction["id"])) {
                return $this->error("Invalid auction");
            }

            if ($auction["owner"] != $this->user->id) {
                return $this->error("Invalid auction");
            }

            if ($auction["currentBidder"]) {
                $user = new User($auction["currentBidder"]);
                $user->set("US_money", $user->info->US_money + $auction["currentBid"]);
                $user->newNotification("An auction that you were bidding on was removed, your money has been returned to you.");
            }

            $this->db->delete("
                UPDATE auctions SET A_start = 0, A_highestBidder = 0 WHERE A_id = :id
            ", array(
                ":id" => $this->methodData->id
            ));


        }
            
        public function method_buy() {
            if (isset($this->methodData->auction)) {
                $auction = $this->db->prepare("
                    SELECT
                        A_id as 'id',
                        A_user as 'owner', 
                        A_bid as 'currentBid',
                        A_buyNow as 'buyNow',
                        A_type as 'type',
                        A_qty as 'qty',
                        A_highestBidder as 'currentBidder',
                        A_start as 'start', 
                        A_start + (A_length * 3600) as 'end'
                    FROM 
                        auctions 
                    WHERE
                        A_id = :id
                    ORDER BY end ASC
                ");    
                $auction->bindParam(":id", $this->methodData->auction);
                $auction->execute();
                $auction = $auction->fetch(PDO::FETCH_ASSOC);


                $this->methodData->bid = $auction["buyNow"];

            }
        }

        public function method_sell() {

            $type = "";

            if (isset($this->methodData->type)) $type = $this->methodData->type;

            if (isset($this->methodData->price) && isset($this->methodData->id)) {
                $invalid = false;

                if (isset($this->methodData->buyNowPrice) && $this->methodData->buyNowPrice <= $this->methodData->price) {
                    $invalid = "The Buy Now price has to be greater then the auction starting price!";
                }

                if ($this->methodData->price < 0) {
                    $invalid = "Please enter a valid starting price";
                }

                if (!$invalid) {
                    $id = abs(intval($this->methodData->id));
                    switch ($type) {
                        case "bullets":
                            if ($this->user->info->US_bullets < $id) {
                                return $this->error("You dont have this many bullets");
                            }
                            $this->user->set("US_bullets", $this->user->info->US_bullets - $id);
                        break;
                        case "points":
                            if ($this->user->info->US_points < $id) {
                                return $this->error("You dont have this many points");
                            }
                            $this->user->set("US_points", $this->user->info->US_points - $id);
                        break;
                        case "car":

                            $car = $this->db->prepare("SELECT * FROM garage WHERE GA_id = :id");
                            $car->bindParam(":id", $id);
                            $car->execute();
                            $car = $car->fetch(PDO::FETCH_ASSOC);

                            if ($car["GA_uid"] != $this->user->id) {
                                return $this->error("You do not own this!");
                            }

                            $query = $this->db->prepare("
                                UPDATE garage SET GA_uid = -1 WHERE GA_uid = :u AND GA_id = :i;
                            ");
                            $query->bindParam(':u', $this->user->id);
                            $query->bindParam(':i', $id);
                            $query->execute();
                            unset($query);
                        break;
                        case "item":

                            $item = $this->db->prepare("
                                SELECT * FROM userInventory WHERE UI_item = :id AND UI_user = :user
                            ");
                            $item->bindParam(":id", $id);
                            $item->bindParam(":user", $this->user->id);
                            $item->execute();
                            $item = $item->fetch(PDO::FETCH_ASSOC);

                            if ($item["UI_item"] != $this->user->id) {
                                return $this->error("You do not own this!");
                            }

                            $this->user->removeItem($item["UI_item"]);

                        break;
                        case "property":

                            $property = $this->db->prepare("SELECT * FROM properties WHERE PR_id = :id");
                            $property->bindParam(":id", $id);
                            $property->execute();
                            $property = $property->fetch(PDO::FETCH_ASSOC);

                            if ($property["PR_user"] != $this->user->id) {
                                return $this->error("You do not own this!");
                            }

                            $query = $this->db->prepare("
                                UPDATE properties SET PR_user = -1 WHERE PR_user = :u AND PR_id = :i;
                            ");
                            $query->bindParam(':u', $this->user->id);
                            $query->bindParam(':i', $id);
                            $query->execute();
                            unset($query);
                        break;
                    }

                    $insert = $this->db->prepare("
                        INSERT INTO auctions (
                            A_user, A_bid, A_highestBidder, A_start, A_length, A_type, A_qty, A_buyNow
                        ) VALUES (
                            :u, :min, 0, UNIX_TIMESTAMP(), :length, :type, :qty, :buyNow
                        );
                    ");

                    if (!isset($this->methodData->buyNowPrice)) { $this->methodData->buyNowPrice = 0; }

                    $insert->bindParam(":u", $this->user->id);
                    $insert->bindParam(":min", $this->methodData->price);
                    $insert->bindParam(":length", $this->methodData->length);
                    $insert->bindParam(":type", $type);
                    $insert->bindParam(":qty", $id);
                    $insert->bindParam(":buyNow", $this->methodData->buyNowPrice);
                    $insert->execute();

                    return $this->error("The auction has started!", "success");

                } else {
                    $this->error($invalid);
                }
            }

            switch ($type) {
                case "points":
                case "bullets":
                    $this->html .= $this->page->buildElement("sellQuantity");
                break;
                case "item":
                    $item = $this->db->prepare("
                        SELECT
                            UI_item as 'id',  
                            I_name as 'name'
                        FROM 
                            userInventory
                            INNER JOIN items ON (I_id = UI_item)
                        WHERE 
                            UI_user = :user
                        GROUP BY I_id
                        "
                    );

                    $item->bindParam(":user", $this->user->id);
                    $item->execute();
                    $item = $item->fetchAll(PDO::FETCH_ASSOC);

                    $this->html .= $this->page->buildElement("sellItem", array(
                        "items" => $item
                    ));
                break;
                case "car":
                    $cars = $this->db->prepare("
                        SELECT
                            GA_id as 'id',  
                            CONCAT(CA_name, ' ', GA_damage, '% ', L_name) as 'name'
                        FROM 
                            garage
                            INNER JOIN cars ON (GA_car = CA_id)
                            INNER JOIN locations ON (GA_location = L_id)
                        WHERE 
                            GA_uid = :user
                        "
                    );

                    $cars->bindParam(":user", $this->user->id);
                    $cars->execute();
                    $cars = $cars->fetchAll(PDO::FETCH_ASSOC);

                    $this->html .= $this->page->buildElement("sellItem", array(
                        "items" => $cars
                    ));
                break;
                case "property":
                    $properties = $this->db->prepare("
                        SELECT
                            PR_id as 'id',  
                            PR_module as 'module', 
                            L_name as 'location'
                        FROM 
                            properties
                            INNER JOIN locations ON (PR_location = L_id)
                        WHERE 
                            PR_user = :user
                        "
                    );

                    $properties->bindParam(":user", $this->user->id);
                    $properties->execute();
                    $properties = $properties->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($properties as $k => $v) {
                        $properties[$k]["name"] = $v["location"] . " " . $this->page->modules[$v["module"]]["name"];
                    }

                    $this->html .= $this->page->buildElement("sellItem", array(
                        "items" => $properties
                    ));
                break;
                default:
                    $this->html .= $this->page->buildElement("sellWhat");
                break;

            }

        }
        
    }

?>