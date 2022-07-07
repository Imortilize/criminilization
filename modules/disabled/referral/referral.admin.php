<?php

    class adminModule {

        private function getRank($rankID = "all") {
            if ($rankID == "all") {
                $add = "";
            } else {
                $add = " WHERE R_id = :id";
            }
            
            $rank = $this->db->prepare("
                SELECT
                    R_id as 'id',  
                    R_name as 'name',  
                    R_exp as 'exp',  
                    I_name as 'itemName',
                    R_refferalCash as 'cash',  
                    R_refferalBullets as 'bullets',  
                    R_refferalItem as 'item',  
                    R_refferalPoints as 'points',  
                    R_limit as 'limit'
                FROM 
                    ranks
                    LEFT OUTER JOIN items ON (I_id = R_refferalItem)
                    " . $add . "
                ORDER BY R_exp ASC"
            );

            if ($rankID == "all") {
                $rank->execute();
                return $rank->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $rank->bindParam(":id", $rankID);
                $rank->execute();
                return $rank->fetch(PDO::FETCH_ASSOC);
            }
        }

        public function method_viewReferrals () {
            $referrals = $this->db->prepare("
                SELECT 
                    FROM_UNIXTIME(UT_time) as 'date', 
                    u1.U_id as 'id', 
                    u1.U_name as 'name', 
                    R_name as 'rank', 
                    u2.U_name as 'refName',
                    u2.U_id as 'refID'
                FROM users u1
                INNER JOIN users u2 ON (u1.U_referral = u2.U_id)
                INNER JOIN userStats ON (u1.U_id = US_id)
                INNER JOIN userTimers ON (UT_user = u1.U_id AND UT_desc = 'signup')
                INNER JOIN ranks ON (US_rank = R_id)
            ");
            $referrals->execute();
            $referrals = $referrals->fetchAll(PDO::FETCH_ASSOC);

            $this->html .= $this->page->buildElement("referralList", array(
                "referrals" => $referrals
            ));

        }

        public function method_editRank () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No rank ID specified"));
            }

            $rank = $this->getRank($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $rank = (array) $this->methodData;
                
                $update = $this->db->prepare("
                    UPDATE 
                        ranks 
                    SET 
                        R_refferalCash = :cash, 
                        R_refferalBullets = :bullets, 
                        R_refferalItem = :item, 
                        R_refferalPoints = :points
                    WHERE 
                        R_id = :id
                ");
                $update->bindParam(":cash", $this->methodData->cash);
                $update->bindParam(":bullets", $this->methodData->bullets);
                $update->bindParam(":item", $this->methodData->item);
                $update->bindParam(":points", $this->methodData->points);
                $update->bindParam(":id", $this->methodData->id);
                $update->execute();

                $this->html .= $this->page->buildElement("success", array(
                    "text" => "This rank has been updated"
                ));

            }

            $rank["editType"] = "edit";
            $this->html .= $this->page->buildElement("rankForm", $rank);
        }

        public function method_viewRank () {
            
            $this->html .= $this->page->buildElement("rankList", array(
                "ranks" => $this->getRank()
            ));

        }

    }