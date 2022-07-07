<?php
    class referral extends module {
        
        public $allowedMethods = array();

        public function constructModule () {

            $users = $this->db->prepare("
                SELECT * FROM users INNER JOIN userTimers ON (UT_user = U_id AND UT_desc = 'signup') WHERE U_referral = :u ORDER BY UT_time DESC
            ");
            $users->bindParam(":u", $this->user->id);
            $users->execute();

            $users = $users->fetchAll(PDO::FETCH_ASSOC);

            $referrals = array();

            foreach ($users as $user) {
                $u = new User($user["U_id"]);
                $rank = $u->getRank();
                $referrals[] = array(
                    "user" => $u->user, 
                    "rank" => $rank->R_name, 
                    "signup" => $this->date($u->getTimer("signup"))
                );
            }

            $host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

            $parts = explode("?", $host);

            $host = $parts[0] . "?page=register&ref=" . $this->user->id;

            $this->html .= $this->page->buildElement("referrals", array(
                "referrals" => $referrals, 
                "host" => $host
            ));

        }

    }