<?php

    new hook("accountMenu", function () {
        return array(
            "url" => "?page=referral", 
            "text" => "Referral", 
            "sort" => 900
        );
    });

    new Hook("newUser", function ($id) {
        if (!isset($_GET["ref"])) return; 
        $user = new User($id);
        $ref = new User($_GET["ref"]);
        if (isset($ref->info->U_id)) {
            $user->set("U_referral", $ref->info->U_id);
        }
    });

    new Hook("rankUp", function ($u) {

        global $page;

        $user = new User($u["user"]);

        if ($user->info->U_referral) {

            $rank = $user->db->prepare("SELECT * FROM ranks WHERE R_id = :id");
            $rank->bindParam(":id", $user->info->US_rank);
            $rank->execute();
            $rank = $rank->fetch(PDO::FETCH_ASSOC);

            $ref = new User($user->info->U_referral);

            $rewards = array();

            if ($rank["R_refferalCash"]) {
                $ref->set("US_money", $ref->info->US_money + $rank["R_refferalCash"]);
                $rewards[] = $page->money($rank["R_refferalCash"]);
            }

            if ($rank["R_refferalBullets"]) {
                $ref->set("US_bullets", $ref->info->US_bullets + $rank["R_refferalBullets"]);
                $rewards[] = number_format($rank["R_refferalBullets"]) . " Bullets";
            }

            if ($rank["R_refferalPoints"]) {
                $ref->set("US_points", $ref->info->US_points + $rank["R_refferalPoints"]);
                $rewards[] = number_format($rank["R_refferalPoints"]) . " Points";
            }

            if ($rank["R_refferalItem"]) {

                $select = $user->db->prepare("SELECT * FROM userItems WHERE UI_user = :u AND UI_item = :i");
                $select->bindParam(':u', $ref->id);
                $select->bindParam(':i', $rank["R_refferalItem"]);
                $select->execute();
                $exists = $select->fetch(PDO::FETCH_ASSOC);

                if (isset($exists["UI_qty"])) {
                    $query = $user->db->prepare("
                        UPDATE userItems SET UI_qty = UI_qty + 1 WHERE UI_user = :u AND UI_item = :i;
                    ");
                } else {
                    $query = $user->db->prepare("
                        INSERT INTO userItems (UI_qty, UI_user, UI_item) VALUES (1, :u, :i);
                    ");
                }

                $query->bindParam(':u', $ref->id);
                $query->bindParam(':i', $rank["R_refferalItem"]);
                $query->execute();

                $item = $user->db->prepare("SELECT * FROM items WHERE I_id = :i");
                $item->bindParam(':i', $rank["R_refferalItem"]);
                $item->execute();
                $item = $item->fetch(PDO::FETCH_ASSOC);

                $rewards[] = "1x " . $item["I_name"];

            }

            if (count($rewards)) {
                $msg = $user->info->U_name . " just reached " . $rank["R_name"] . " for this you received: <ul>
                    <li>".implode("</li><li>", $rewards)."</li>
                </ul>";
                $ref->newNotification($msg);
            }

        }

    });

?>