<?php
    class workout extends module {
        
        public $allowedMethods = array(
        	"times" => array( "type" => "POST" ),
        	"stat" => array( "type" => "POST" )
        );
        
        public $pageName = '';

        public function constructModule() {
        	$this->html .= $this->page->buildElement("workout", array(
    			"energy" => $this->user->info->US_energy,
    			"maxEnergy" => $this->user->info->US_maxEnergy,
                "energyResetCost" => $this->_settings->loadSetting("workoutResetEnergy", 1, 10),
                "willResetCost" => $this->_settings->loadSetting("workoutResetWill", 1, 5),
        		"stats" => array(
        			"strength" => $this->user->info->US_strength,
					"agility" => $this->user->info->US_agility,
					"guard" => $this->user->info->US_guard,
					"labour" => $this->user->info->US_labour, 
					"total" => $this->user->info->US_strength + $this->user->info->US_agility + $this->user->info->US_guard + $this->user->info->US_labour
        		)
        	));
        }

        public function method_resetEnergy() {
            $cost = $this->_settings->loadSetting("workoutResetEnergy", 1, 10);
            if ($this->user->info->US_points < $cost) {
                return $this->error("You can't afford this!");
            }
            $this->user->set("US_points", $this->user->info->US_points - $cost);
            $this->user->set("US_energy", $this->user->info->US_maxEnergy);
            $this->error("Your energy has been reset to 100%", "success");
        }

        public function method_resetWill() {
            $cost = $this->_settings->loadSetting("workoutResetWill", 1, 5);
            if ($this->user->info->US_points < $cost) {
                return $this->error("You can't afford this!");
            }
            $this->user->set("US_points", $this->user->info->US_points - $cost);
            $this->user->set("US_will", $this->user->info->US_maxWill);
            $this->error("Your will has been reset to 100%", "success");
        }

        public function method_train() {

        	$times = abs(intval($this->methodData->times));

        	if (!$times || $times > $this->user->info->US_energy) {
        		return $this->error("You don't have enough energy to train this many times!");
        	}
        	switch ((int) $this->methodData->stat) {
        		case 1: 
        			$name = "strength";
        			$stat = "US_strength"; 
        		break;
        		case 2: 
        			$name = "agility";
        			$stat = "US_agility"; 
        		break;
        		case 3: 
        			$name = "guard";
        			$stat = "US_guard"; 
        		break;
        		case 4: 
        			$name = "labour";
        			$stat = "US_labour"; 
        		break;
        		default: 
        			return $this->error("This stat does not exist!");
        	}

        	$will = $this->user->info->US_will;

        	$totalGain = 0;

        	while ($times > 0) {
        		$willUsed = mt_rand(1, 3);
        		if ($willUsed > $will ) $willUsed = $will;
        		$gain = round(
        			mt_rand(1, 3) / mt_rand(800, 1000) * mt_rand(800, 1000) * (($will + 20) / 150)
        		, 4);

        		$will -= $willUsed;
        		$totalGain += $gain;
        		$times--;
        	}

        	$times = abs(intval($this->methodData->times));

        	$this->user->set($stat, $this->user->info->$stat + $totalGain);
        	$this->user->set("US_will", $will);
        	$this->user->set("US_energy", $this->user->info->US_energy - $times);

        	$this->error($this->page->buildElement("trainSuccess", array(
        		"stat" => $name,
				"times" => $times,
				"gain" => $totalGain
        	)), "success");

        }

    }

?>