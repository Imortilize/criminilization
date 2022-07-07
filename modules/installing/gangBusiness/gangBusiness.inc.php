<?php

    class gangBusiness extends module {
        
        public $allowedMethods = array(
        	'id'=>array('type'=>'get'),
            'user'=>array('type'=>'post'),
            'business'=>array('type'=>'post')
        );
        
        public $pageName = '';
        
        public function constructModule() {
                
            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }

            $g = new Gang($this->user->info->US_gang);

            $max = floor($g->gang["level"] / 5) + 1;

        	$businesses = $this->db->selectAll("
        		SELECT
                    BU_name as 'name',  
                    BU_rank as 'rank',  
                    BU_payout as 'payout',  
                    BU_payoutTime as 'payoutTime',  
                    BU_cost as 'cost', 
                    UBU_id as 'id',  
                    UBU_lastPayout as 'lastPayout'
                FROM businesses
                INNER JOIN userBusinesses ON (BU_id = UBU_business AND UBU_user = :user)
        	", array(
        		":user" => $this->user->id
        	));

        	foreach ($businesses as $key => $business) {
        		$business["cooldown"] = $this->timeLeft($business["payoutTime"]);
        		$business["nextPayout"] = $business["lastPayout"] + $business["payoutTime"];
        		$businesses[$key] = $business;
        	}

            $canBuyBusinesses = $g->can("buyBusinesses");

            if ($canBuyBusinesses) {
                $buyOpts = array(
                    "users" => $g->gang["members"], 
                    "businesses" => $this->db->selectAll("
                        SELECT
                            BU_id as 'id',  
                            BU_name as 'name',  
                            BU_rank as 'rank',  
                            BU_payout as 'payout',  
                            BU_payoutTime as 'payoutTime',  
                            BU_cost as 'cost'
                        FROM businesses
                        WHERE BU_rank <= :rank
                        ORDER BY BU_rank ASC, BU_payout ASC
                    ", array(
                        ":rank" => $g->gang["level"]
                    ))
                );
                foreach ($buyOpts["businesses"] as $key => $business) {
                    $business["cooldown"] = $this->timeLeft($business["payoutTime"]);
                    $buyOpts["businesses"][$key] = $business;
                }

                foreach ($buyOpts["users"] as $k => $v) {
                    $v["max"] = $max;
                    $v["owned"] = $this->db->select("
                        SELECT COUNT(*) as 'count' FROM userBusinesses WHERE UBU_user = :user
                    ", array(
                        ":user" => $v["user"]["id"]
                    ))["count"];

                    $buyOpts["users"][$k] = $v;

                    if ($v["max"] <= $v["owned"]) {
                        unset($buyOpts["users"][$k]);
                    }

                }

            } else {
                $buyOpts = array();
            }

            $this->html .= $this->page->buildElement("businesses", array(
            	"buyOpts" => $buyOpts,
                "businesses" => $businesses, 
                "businessCount" => count($businesses), 
                "canBuyBusinesses" => $canBuyBusinesses,
                "max" => $max
            ));
        }

        public function method_collect() {
                
            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }
        	
        	$business = $this->db->select("
        		SELECT * 
        		FROM businesses 
                INNER JOIN userBusinesses ON (BU_id = UBU_business AND UBU_user = :user)
        		WHERE UBU_id = :id
        	", array(
        		":user" => $this->user->id,
        		":id" => $this->methodData->id
        	));

        	if (!(int) $business["UBU_lastPayout"]) {
        		return $this->error("You dont own this business!");
        	}

        	if ($business["UBU_lastPayout"] + $business["BU_payoutTime"] > time()) {
        		return $this->error("You cant collect from this businesses yet!");
        	}

            $this->db->update("
                UPDATE gangs SET G_money = G_money + :money WHERE G_id = :id
            ", array(
                ":id" => $this->user->info->US_gang, 
                ":money" => $business["BU_payout"]
            ));

        	$this->error(
        		"You collected " . $this->money($business["BU_payout"]) . " from " . $business["BU_name"] ." and sent it to your " . _setting("gangName") . " bank!", 
        		"success"
        	);

        	$this->db->update("
        		UPDATE userBusinesses SET UBU_lastPayout = UNIX_TIMESTAMP()
        		WHERE UBU_id = :c
        	", array(
        		":c" => $this->methodData->id
        	));

        }

        public function method_buy() {
                
            if (!$this->user->info->US_gang) {
                return $this->error("You are not part of a " . _setting("gangName"));
            }

            if (!isset($this->methodData->user)) {
                return $this->error("Please select a user!");
            }

            if (!isset($this->methodData->business)) {
                return $this->error("Please select a business!");
            }

            $u = new User($this->methodData->user);

            if (!$u) {
                return $this->error("This user does not exist!");
            }

            if ($u->info->US_gang != $this->user->info->US_gang) {
                return $this->error("This member is not part of your " . _setting("gangName"));
            }

            $g = new Gang($this->user->info->US_gang);
            
            if (!$g->can("buyBusinesses")) {
                return $this->error("You dont have permission to do this!");
            }

            $max = floor($g->gang["level"] / 5) + 1;

            $userBusinesses = $this->db->selectAll("
                SELECT * 
                FROM userBusinesses
                WHERE UBU_user = :user
            ", array(
                ":user" => $this->methodData->user
            ));

            if (count($userBusinesses) >= $max) {
                return $this->error("This user cant manage any more businesses!");
            }
        	
        	$business = $this->db->select("
        		SELECT * 
        		FROM businesses 
        		WHERE BU_id = :id
        	", array(
        		":id" => $this->methodData->business
        	));

        	if ($business["BU_cost"] > $g->gang["G_money"]) {
        		return $this->error("Your " . _setting("gangName") . " cant afford this!");
        	}

        	if ($business["BU_rank"] > $g->gang["G_level"]) {
        		return $this->error("You can't buy this yet!");
        	}


            $this->db->update("
                UPDATE gangs SET G_money = G_money - :money WHERE G_id = :id
            ", array(
                ":id" => $this->user->info->US_gang, 
                ":money" => $business["BU_cost"]
            ));

        	$this->db->insert("
        		INSERT INTO userBusinesses (UBU_user, UBU_business, UBU_lastPayout) VALUES (:u, :c, UNIX_TIMESTAMP());
        	", array(
        		":u" => $u->id, 
        		":c" => $business["BU_id"]
        	));

            $this->error("You brought a business!", "success");

        }
        
    }

?>