<?php

	new Hook("userInformation", function ($user) {
        global $page;

        if ($user) {

	        $secondsBetweenGain = 60;
	        $maxStamina = 15000;
	        $timer = "statRegen";
	        $last = $user->getTimer($timer);
	        $seconds = (time() - $last);
        	$times = floor($seconds / $secondsBetweenGain);

        	if ($times) {

	        	$newEnergy = $times + $user->info->US_energy;
	        	$maxEnergy = $user->info->US_maxEnergy;
	    		if ($newEnergy > $maxEnergy) $newEnergy = $maxEnergy;

	        	$newWill = $times + $user->info->US_will;
	        	$maxWill = $user->info->US_maxWill;
	    		if ($newWill > $maxWill) $newWill = $maxWill;

	    		$user->set("US_energy", $newEnergy);
	    		$user->set("US_will", $newWill);
	    		$user->updateTimer($timer, $last + ($times * $secondsBetweenGain));
				
				$page->addToTemplate("energy", $newEnergy);
				$page->addToTemplate("will", $newWill);


        	}

			$energyPerc = round($user->info->US_energy / $user->info->US_maxEnergy * 100, 2);
			$page->addToTemplate("energy", $user->info->US_energy);
			$page->addToTemplate("maxEnergy", $user->info->US_maxEnergy);
			$page->addToTemplate("energyPerc", number_format($energyPerc, 2));

			$willPerc = round($user->info->US_will / $user->info->US_maxWill * 100, 2);
			$page->addToTemplate("will", $user->info->US_will);
			$page->addToTemplate("maxWill", $user->info->US_maxWill);
			$page->addToTemplate("willPerc", number_format($willPerc, 2));

        }

	});

	new Hook("rankUp", function ($info) {
		$u = new User($info["user"]);
		$u->set("US_maxWill", $u->info->US_maxWill + 2);
		$u->set("US_will", $u->info->US_will + 2);
		$u->set("US_maxEnergy", $u->info->US_maxEnergy + 2);
		$u->set("US_energy", $u->info->US_energy + 2);
	});

    new hook("actionMenu", function () {
        return array(
            "url" => "?page=workout", 
            "text" => "Workout", 
            "sort" => 300
        );
    });
?>