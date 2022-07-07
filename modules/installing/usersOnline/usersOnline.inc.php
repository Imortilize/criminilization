<?php

    class usersOnline extends module {
        
        public $allowedMethods = array();
        
        public $pageName = 'Users Online';
        
        public function constructModule() {
            
            
            $durations = array();
            
            $intval = 900;
            
            unset($users);
            $users = array();

            $online = $this->db->prepare("
                SELECT * FROM userTimers 
                WHERE UT_desc = 'laston' AND UT_time > ".(time()-$intval)." 
                ORDER BY UT_time DESC
            ");
            $online->execute();

            $links = new Hook("profileLink");
            
            while ($row = $online->fetch(PDO::FETCH_ASSOC)) {

                $userOnline = new user($row['UT_user']);
                if (isset($userOnline->info->U_id)) {
                    $users[] = array(
                        "profileLinks" => $links->run($userOnline), 
                        "user" => $userOnline->user,
                        "date" => $this->date($row["UT_time"]),
                        "laston" => $row["UT_time"],
                        "gang" => $userOnline->getGang(),
                        "rank" => $userOnline->rank->R_name,
                        "id" => $userOnline->info->U_id
                    );
                }

            }
            
            $this->html .= $this->page->buildElement("usersOnline", array("users" => $users));

            
        }
        
    }

