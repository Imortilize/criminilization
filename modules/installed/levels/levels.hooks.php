<?php

	new Hook("userInformation", function ($user) {
        global $page;
        if ($user) {
        	$nextRank = $user->nextRank();
			if (!is_null($nextRank)) {
				if (isset($nextRank->R_exp) && ($user->info->US_exp > $nextRank->R_exp)) {
					$page->addToTemplate("levelUp", true);
				} else {
					$page->addToTemplate("levelUp", false);
				}
			}
        }
	});