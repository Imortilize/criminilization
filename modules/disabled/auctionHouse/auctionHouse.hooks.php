<?php

    global $db, $page;

    $auctions = $db->prepare("
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
            A_paidOut = 0 AND 
            A_start + (A_length * 3600) < UNIX_TIMESTAMP()
        ORDER BY end ASC;
    ");    
    $auctions->execute();
    $auctions = $auctions->fetchAll(PDO::FETCH_ASSOC);

    $auctionsUpdate = $db->prepare("
        UPDATE auctions SET A_paidOut = 1 
        WHERE A_paidOut = 0 AND A_start + (A_length * 3600) < UNIX_TIMESTAMP();
    ");
    $auctionsUpdate->execute();

    if (count($auctions)) {
        foreach ($auctions as $auction) {

            $seller = new User($auction["owner"]);

            if ((int) $auction["currentBidder"]) {
                $winner = new User($auction["currentBidder"]);
            } else {
                $winner = $seller;
            }


            $id = $auction["qty"];
            
            switch ($auction["type"]) {
                case "bullets":
                    $winner->set("US_bullets", $winner->info->US_bullets + $id);
                    $sold = "$id bullets";
                break;
                case "item":
                    $items = new Items();
                    $item = $items->getItem($id);
                    $winner->addItem($id);
                    $sold = $item["name"];
                break;
                case "points":
                    $winner->set("US_points", $winner->info->US_points + $id);
                    $sold = "$id points";
                break;
                case "car":

                    $item = $db->prepare("
                        SELECT * FROM garage 
                        INNER JOIN cars ON (CA_id = GA_car)  
                        WHERE GA_id = :id
                    ");
                    $item->bindParam(":id", $id);
                    $item->execute();
                    $item = $item->fetch(PDO::FETCH_ASSOC);

                    $query = $db->prepare("
                        UPDATE garage SET GA_uid = :u WHERE GA_id = :i;
                    ");
                    $query->bindParam(':u', $winner->id);
                    $query->bindParam(':i', $id);
                    $query->execute();
                    unset($query);
                    $sold = $item["CA_name"];
                break;
                case "property":
                    $item = $db->prepare("
                        SELECT * FROM properties 
                        WHERE PR_id = :id
                    ");
                    $item->bindParam(":id", $id);
                    $item->execute();
                    $item = $item->fetch(PDO::FETCH_ASSOC);

                    $query = $db->prepare("
                        UPDATE properties SET PR_user = :u WHERE PR_id = :i;
                    ");
                    $query->bindParam(':u', $winner->id);
                    $query->bindParam(':i', $id);
                    $query->execute();
                    
                    switch ($item["PR_module"]) {
                        case "blackjack":
                            $sold = "blackjack";
                        break;
                        case "bullets":
                            $sold = "bullet factory";
                        break;
                        default:
                            $sold = "a property";
                        break;
                    }
                break;
            }



            if ((int) $auction["currentBidder"]) {
                $winner = new User($auction["currentBidder"]);
                $seller->set("US_money", $seller->info->US_money + $auction["currentBid"]);
                $seller->newNotification("Your auction of " . $sold . " has been sold, you were transferred " . $this->money($auction["currentBid"]));
                $winner->newNotification("You won your auction, " . $sold . " was transferred to you!");
            } else {
                $winner = $seller;
                $winner->newNotification("Your auction did not sell, " . $sold . " was returned to you.");
            }


        }
    }

        new hook("moneyMenu", function () {
            return array(
                "url" => "?page=auctionHouse", 
                "text" => "Auction House", 
                "sort" => 10000
            );
        });

?>