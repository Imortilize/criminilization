<?php


    new hook("moneyMenu", function () {
        return array(
            "url" => "?page=escrow", 
            "text" => "Escrow", 
            "sort" => 100
        );
    });

    new Hook("escrowItem", function () {
        return array( 
            "id" => "money",
            "type" => "qty", 
            "name" => function ($item) {
                if (!isset($item["qty"])) $item["qty"] = 0; 
                return array(
                    "desc" => "Money", 
                    "info" => "$" . number_format($item["qty"])
                );
            },
            "validate" => function ($user, $qty) {
                return $user->info->US_money >= $qty;
            }, 
            "remove" => function ($user, $qty) {
                $user->set("US_money", $user->info->US_money - $qty);
            }, 
            "add" => function ($user, $qty) {
                $user->set("US_money", $user->info->US_money + $qty);
            }
        );
    });

    new Hook("escrowItem", function () {
        return array(
            "id" => "bullets",
            "type" => "qty", 
            "name" => function ($item) {
                if (!isset($item["qty"])) $item["qty"] = 0; 
                return array(
                    "desc" => "Bullets", 
                    "info" => number_format($item["qty"])
                );
            },
            "validate" => function ($user, $qty) {
                return $user->info->US_bullets >= $qty;
            }, 
            "remove" => function ($user, $qty) {
                $user->set("US_bullets", $user->info->US_bullets - $qty);
            }, 
            "add" => function ($user, $qty) {
                $user->set("US_bullets", $user->info->US_bullets + $qty);
            }
        );
    });

    new Hook("escrowItem", function () {
        return array(
            "id" => "points",
            "type" => "qty", 
            "name" => function ($item) {
                if (!isset($item["qty"])) $item["qty"] = 0; 
                return array(
                    "desc" => "Points", 
                    "info" => number_format($item["qty"])
                );
            },
            "validate" => function ($user, $qty) {
                return $user->info->US_points >= $qty;
            }, 
            "remove" => function ($user, $qty) {
                $user->set("US_points", $user->info->US_points - $qty);
            }, 
            "add" => function ($user, $qty) {
                $user->set("US_points", $user->info->US_points + $qty);
            }
        );
    });

    new Hook("escrowItem", function () {
        return array(
            "id" => "property",
            "type" => "list", 
            "name" => function ($id) {
                global $db, $page, $user;

                $userProperties = $db->selectAll("
                    SELECT
                        PR_id as 'id',
                        CONCAT(L_name, ' ', PR_module) as 'name'
                    FROM properties 
                    INNER JOIN locations ON L_id = PR_location
                    WHERE PR_user = :user 
                ", array(
                    ":user" => $user->id
                ));

                if (!$id) return array(
                    "desc" => "Property", 
                    "options" => $userProperties,
                    "info" => ""
                );

                $property = $db->select("
                    SELECT * 
                    FROM properties 
                    INNER JOIN locations ON L_id = PR_location
                    WHERE PR_id = :id", array(
                    ":id" => $id["qty"]
                ));

                return array(
                    "desc" => $page->modules[$property["PR_module"]]["name"],
                    "info" => $property["L_name"]
                );
            },

            "validate" => function ($user, $id) {
                global $db, $page;
                $userProperties = $db->select("
                    SELECT PR_id as 'id' FROM properties WHERE PR_id = :id 
                ", array(
                    ":id" => $id
                ));
                return $userProperties["id"] && $userProperties["id"] == $user->info->U_id;
            }, 
            "remove" => function ($user, $id) {
                global $db, $page;
                $db->update("
                    UPDATE properties SET PR_user = -1 WHERE PR_id = :id 
                ", array(
                    ":id" => $id
                ));
            }, 
            "add" => function ($user, $id) {
                global $db, $page;
                $db->update("
                    UPDATE properties SET PR_user = :user WHERE PR_id = :id 
                ", array(
                    ":user" => $user->info->U_id,
                    ":id" => $id
                ));
            }
        );
    });

    new Hook("escrowItem", function () {
        return array(
            "id" => "car",
            "type" => "list", 
            "name" => function ($id) {

                global $db, $page, $user;
                $userCars = $db->selectAll("
                    SELECT
                        GA_id as 'id',
                        CONCAT(CA_name, ' [', L_name, '] (', GA_damage, '%)') as 'name'
                    FROM garage
                    INNER JOIN cars ON CA_id = GA_car
                    INNER JOIN locations ON L_id = GA_location
                    WHERE GA_uid = :user 
                ", array(
                    ":user" => $user->id
                ));

                if (!$id) return array(
                    "desc" => "Car", 
                    "options" => $userCars,
                    "info" => ""
                );

                $car = $db->select("
                    SELECT 
                        CA_name as 'name',
                        CONCAT(L_name, ' (', GA_damage, '%)') as 'info'
                    FROM garage
                    INNER JOIN cars ON CA_id = GA_car
                    INNER JOIN locations ON L_id = GA_location
                    WHERE GA_id = :id", array(
                    ":id" => $id["qty"]
                ));

                return array(
                    "desc" => $car["name"],
                    "info" => $car["info"]
                );
            },

            "validate" => function ($user, $id) {
                global $db, $page;
                $userCar = $db->select("
                    SELECT GA_uid as 'id' FROM garage WHERE GA_id = :id 
                ", array(
                    ":id" => $id
                ));
                return $userCar["id"] == $user->info->U_id;
            }, 
            "remove" => function ($user, $id) {
                global $db, $page;
                $db->update("
                    UPDATE garage SET GA_uid = -1 WHERE GA_id = :id 
                ", array(
                    ":id" => $id
                ));
            }, 
            "add" => function ($user, $id) {
                global $db, $page;
                $db->update("
                    UPDATE garage SET GA_uid = :user WHERE GA_id = :id 
                ", array(
                    ":user" => $user->info->U_id,
                    ":id" => $id
                ));
            }
        );
    });

    new Hook("escrowItem", function () {
        return array(
            "id" => "item",
            "type" => "listQty", 
            "name" => function ($id) {
                global $db, $page, $user;

                $userInventory = $db->selectAll("
                    SELECT
                        UI_item as 'id',
                        I_name as 'name'
                    FROM userInventory
                    INNER JOIN items ON I_id = UI_item
                    WHERE UI_user = :user 
                ", array(
                    ":user" => $user->id
                ));

                if (!$id) return array(
                    "desc" => "Item", 
                    "options" => $userInventory,
                    "info" => ""
                );

                $items = new Items();
                $item = $items->getItem($id["qty"]["item"]);

                return array(
                    "desc" => $item["name"],
                    "info" => $id["qty"]["qty"]
                );
            },

            "validate" => function ($user, $id) {
                return $user->hasItem($id["item"], $id["qty"]);
            }, 
            "remove" => function ($user, $item) {
                $user->removeItem($item["item"], $item["qty"]);
            }, 
            "add" => function ($user, $item) {
                $user->addItem($item["item"], $item["qty"]);
            }
        );
    });