<?php

	function _getLevelExp($level, $exp) {	
		return ceil(($level + $level) * $level * $exp);
	}

    class adminModule {

        public function method_options () {

        	$s = new Settings();

        	if (isset($this->methodData->structure)) {
        		$s->update("levelGenStructure", $this->methodData->structure);
        		$s->update("levelGenExp", $this->methodData->exp);
        		$s->update("levelGenMoney", $this->methodData->money);
        		$s->update("levelGenBullets", $this->methodData->bullets);
        		$s->update("levelGenHealthBase", $this->methodData->healthBase);
        		$s->update("levelGenHealth", $this->methodData->health);
        		$s->update("levelGenQty", $this->methodData->qty);

        		$this->html .= $this->page->buildElement("success", array(
        			"text" => "Options Updated"
        		));
        	}

            $options = array(
            	"structure" => $s->loadSetting("levelGenStructure", 1, "Level %level%"),
            	"exp" => $s->loadSetting("levelGenExp", 1, 1.75),
            	"money" => $s->loadSetting("levelGenMoney", 1, 250),
            	"bullets" => $s->loadSetting("levelGenBullets", 1, 10),
            	"healthBase" => $s->loadSetting("levelGenHealthBase", 1, 2500),
            	"health" => $s->loadSetting("levelGenHealth", 1, 500),
            	"qty" => $s->loadSetting("levelGenQty", 1, 250)
            );

            $this->html .= $this->page->buildElement("options", $options);

            if (isset($this->methodData->generateLevels)) {
            	$level = 0;

            	$query = $this->db->prepare("
					INSERT INTO ranks (R_id, R_name, R_exp, R_cashReward, R_bulletReward, R_health) VALUES (:level, :name, :exp, :money, :bullets, :health)
					ON DUPLICATE KEY UPDATE 
						R_name = :name, 
						R_exp = :exp, 
						R_cashReward = :money, 
						R_bulletReward = :bullets, 
						R_health = :health;
            	");

            	$lastExp = 0;

            	$table = array();

            	while ($level < $options["qty"]) {

            		$exp = _getLevelExp($level, $options["exp"]);

            		$data = array(
	            		":level" => $level + 1,
	            		":exp" => $exp + $lastExp,
	            		":money" => $options["money"] * $level,
	            		":bullets" => $options["bullets"] * $level,
	            		":health" => $options["health"] * $level + $options["healthBase"],
	            		":name" => str_replace("%level%", ($level + 1), $options["structure"])
            		);

            		$lastExp = $data[":exp"];

            		$query->execute($data);

            		$data[":diff"] = $exp;
            		$table[] = $data;

	            	$this->db->update("
	            		UPDATE userStats SET US_rank = :level WHERE US_exp > :exp
	            	", array(
	            		":exp" => $data[":exp"],
	            		":level" => $data[":level"]
	            	));

            		$level++;
            	}

            	$this->db->update("
            		UPDATE userStats SET US_rank = :max WHERE US_rank > :max
            	", array(
            		":max" => $options["qty"]
            	));

            	$this->db->update("
            		DELETE FROM ranks WHERE R_id > :max
            	", array(
            		":max" => $options["qty"]
            	));


        		$this->html .= $this->page->buildElement("success", array(
        			"text" => "Levels Generated"
        		));

        		$this->html .= $this->page->buildElement("table", array(
        			"rows" => $table
        		));
            }

        }

    }
