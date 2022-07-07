<?php
    class russianRoulette extends module {
        
        public $allowedMethods = array(
        	"csfr" => array( "type" => "POST" )
        );
        
        public $pageName = '';

        public function getPrize() {
            $prize = $this->_settings->loadSetting("russianRoulettePrize", true, 50000000);

            $ranks = $this->db->select("
                SELECT COUNT(*) as 'count' FROM ranks WHERE R_exp < :exp
            ", array(
                ":exp" => $this->user->info->US_exp
            ));

            $prize *= $ranks["count"];
            return $prize;
        }

        public function constructModule() {

            if (!isset($_SESSION["csfr"])) {
                $_SESSION["csfr"] = sha1(mt_rand(1, 1000000000));
            }

            $rank = $this->db->select("
                SELECT * FROM ranks WHERE R_id = :rank
            ", array(
                ":rank" => $this->_settings->loadSetting("russianRouletteRank", true, 1)
            ));

        	$this->html .= $this->page->buildElement("russianRoulette", array(
                "rank" => $rank["R_name"],
                "prize" => $this->getPrize(), 
                "csfr" => $_SESSION["csfr"]
        	));
        }
            
        public function method_pull() {

            if ($_SESSION["csfr"] != $this->methodData->csfr) {
                return $this->error("Invalid page request!");
            }

            $rank = $this->db->select("
                SELECT * FROM ranks WHERE R_id = :rank
            ", array(
                ":rank" => $this->_settings->loadSetting("russianRouletteRank", true, 1)
            ));

            if ($rank["R_exp"] > $this->user->info->US_exp) {
                return $this->error("You must be a " . $rank["R_name"] . " to play russian roulette!");
            }

            $shot = mt_rand(1, 6) == 1;

            if ($shot) {
                $this->error("It was the chamber with the bullet and you killed yourself!");
                $this->user->set("U_status", 0);
                $this->user->set("US_shotBy", $this->user->id);
                $this->user->updateTimer("killed", time());
                $this->construct = false;
            } else {
                $prize = $this->getPrize();

                $this->error("Lucky Git, that was'nt the chamber with the bullet and you won $" . number_format($prize), "success");

                $this->user->set("US_money", $this->user->info->US_money + $prize);

            }

        }

        

    }

?>