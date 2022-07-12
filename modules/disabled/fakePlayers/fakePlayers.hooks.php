<?php
new hook("actionMenu", function ($user) {
    if ($user) {
        $settings = new settings();
        $settings->loadSetting('ocBots', true, 0);
        $settings->loadSetting('botsBullets', true, 0);
        $settings->loadSetting('botsAttack', true, 0);

        $bots = $user->db->selectAll("SELECT * FROM users where U_bot = 1");
        foreach ($bots as $bot) {
            $botUser = new user($bot['U_id']);
            $botUser->checkRank();
            $location = $user->db->select("SELECT * FROM locations order by RAND() limit 1");
            if ($settings->loadSetting("ocBots", true, 0)) {
                $isInOc = $user->db->select("SELECT * FROM ocusers where OCU_user = :user ", array(
                    ":user" => $botUser->info->US_id
                ));
                if (isset($isInOc['OCU_id'])){
                    $inOc = $user->db->select("SELECT * FROM oc where OC_id = :id ", array(
                        ":id" => $isInOc['OCU_oc']
                    ));
                    $botUser->set("US_location", $inOc['OC_location']);
                }
            }
            if ($botUser->checkTimer('jail')) {
                if ($botUser->checkTimer('crime')) {
                    $chance = mt_rand(1, 10);
                    if ($chance <= 5) {
                        $botUser->add("US_exp", 1);
                        $botUser->add("US_money", 100);
                    }else{
                        $botUser->updateTimer('jail', 100, true);
                    }
                    $botUser->updateTimer('crime', 5*60, true);
                }
                if ($botUser->checkTimer('theft')) {
                    $chance = mt_rand(1, 5);
                    if ($chance <= 2) {
                        $botUser->add("US_exp", 2);
                    }else{
                        $botUser->updateTimer('jail', 200, true);
                    }
                    $botUser->updateTimer('theft', 8*60, true);
                }
                if ($botUser->checkTimer('chase')) {
                    $chance = mt_rand(1, 15);
                    if ($chance <= 5) {
                        $botUser->add("US_exp", 2);
                        $botUser->add("US_money", 250);
                    }else{
                        $botUser->updateTimer('jail', 300, true);
                    }
                    $botUser->updateTimer('chase', 8*60, true);
                }
                $botUser->set("US_location", $location['L_id']);
            }
            if ($settings->loadSetting("botsAttack", true, 0)) {
                if ($botUser->info->US_rank > 4 && $botUser->checkTimer('botKillTime')) {
                    $randomUser = $user->db->select("SELECT * FROM users where U_userLevel = 1 AND U_status = 1 order by RAND() limit 1");
                    $userRandom = new User($randomUser['U_id']);
                    $userRandom->add("US_health", 1);
                    $userRandom->newNotification($botUser->info->U_name . " tried to kill you!");
                    $botUser->updateTimer('botKillTime', 3*24*60*60, true);
                }
            }
            if ($settings->loadSetting("ocBots", true, 0)) {
                $invites = $user->db->selectAll("SELECT * FROM ocusers where OCU_user = :user AND OCU_status = -1", array(
                    ":user" => $botUser->info->US_id
                ));
                foreach ($invites as $invite) {
                    if ($invite['OCU_role'] == 4) {
                        $item = mt_rand(6, 10);
                    }else{
                        $item = mt_rand(2, 4);
                    }
                    $user->db->update("UPDATE ocusers SET OCU_status = :item WHERE OCU_id = :id ", array(
                        ":item" => $item,
                        ":id" => $invite["OCU_id"],
                    ));
                }
            }
            if ($settings->loadSetting("botsBullets", true, 0)) {
                if ($botUser->checkTimer('bullets')) {
                    $currentLocation = $user->db->select("SELECT * FROM locations where L_id = :id", array(
                        ":id" => $user->info->US_location
                    ));
                    if ($currentLocation['L_bullets'] > 2000) {
                        $user->db->update("UPDATE locations SET L_bullets = L_bullets - 100 WHERE L_id= :loc", array(
                            ":loc" => $botUser->info->US_location
                        ));
                        $property = new Property($botUser, "bullets");
                        $owner = $property->getOwnership();
                        if ($owner["user"]) {
                            if ($owner['cost'] > 0) {
                                $cost = $owner['cost'] * 100;
                            }else{
                                $cost = 150 * 100;
                            }
                            $profit = $cost * 0.5;
                            $property->updateProfit($profit);
                            $ownerUser = new user($owner["user"]["id"]);
                            $ownerUser->add("US_money", $profit);
                        }
                        $botUser->add("US_bullets", 50);
                        $botUser->updateTimer('bullets', 1000, true);
                    }
                }
            }
            $botUser->updateTimer('laston', 1, true);
        }
    }
    return array();
});
