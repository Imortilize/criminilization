<?php

    global $db, $page;

    $lottery = $db->select("SELECT * FROM lottery WHERE LO_winner = 0 ORDER BY LO_date ASC LIMIT 0, 1");

    if ($lottery && $lottery["LO_date"] < time()) {
    	$db->update("UPDATE lottery SET LO_winner = -1 WHERE LO_date = " . $lottery["LO_date"]);

    	$users = $db->selectAll("
    		SELECT US_id, US_lotteryTickets FROM userStats WHERE US_lotteryTickets > 0
    	");

    	$total = 0;
    	foreach ($users as $user) {
    		$total += $user["US_lotteryTickets"];
    	}

    	if ($total) {

	    	$winningNumber = mt_rand(1, $total);

	    	$count = 0;
	    	foreach ($users as $user) {
	    		$count += $user["US_lotteryTickets"];
	    		if ($count >= $winningNumber) {
	    			$winner = new User($user["US_id"]);
	    			break;
	    		}
	    	}

	    	$winner->set("US_money", $winner->info->US_money + $lottery["LO_jackpot"]);
	    	$winner->newNotification(
	    		"You won " . $this->money($lottery["LO_jackpot"]) . " in the lottery"
	    	);

	    	$db->update(
	    		"UPDATE lottery SET LO_winner = ".$winner->id." WHERE LO_date = " . $lottery["LO_date"]
	    	);

	    	$users = $db->update("
	    		UPDATE userStats SET US_lotteryTickets = 0 WHERE US_lotteryTickets > 0
	    	");

    	}

    }


    new hook("casinoMenu", function () {
        return array(
            "url" => "?page=lottery", 
            "text" => "Lottery", 
            "sort" => 100
        );
    });
?>