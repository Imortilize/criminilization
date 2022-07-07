<?php

    class organizedCrime extends module {

        public $allowedMethods = array(
            "id" => array( "type" => "GET"),
            "user" => array( "type" => "POST"),
            "role" => array( "type" => "POST"),
            "item" => array( "type" => "POST")
        );

        public $ocThresholds = array();
        
        public $pageName = '';

        public $roles = array("", "Leader", "Weapons", "Explosives", "Driver");
        public $roleDescriptions = array("", "leader", "weapons expert", "explosives expert", "driver");
        public $thresholdDesc = array("", "Novice", "Journeyman", "Adept", "Expert", "Master");

        public function constructModule() {

            if (!$this->user->checkTimer('oc')) {
                $time = $this->user->getTimer('oc');
                $crimeError = array(
                    "timer" => "oc",
                    "text"=>"You cant attempt another organized crime untill your timer is up!",
                    "time" => $this->user->getTimer("oc")
                );
                $this->html .= $this->page->buildElement('timer', $crimeError);
            }

            $inOC = $this->db->prepare("SELECT * FROM ocUsers WHERE OCU_user = :user AND OCU_status != -1");
            $inOC->bindParam(":user", $this->user->id);
            $inOC->execute();
            $inOC = $inOC->fetch(PDO::FETCH_ASSOC);

            if ($inOC && $inOC["OCU_id"]) {
                return header("Location:?page=organizedCrime&action=roles");
            }

            $types = $this->db->prepare("
                SELECT 
                    OCT_id as 'id', 
                    OCT_name as 'name', 
                    OCT_cooldown as 'cooldown',
                    OCT_cost as 'cost',
                    OCT_successEXP as 'successEXP', 
                    OCT_failedEXP as 'failedEXP', 
                    OCT_minCash as 'minCash', 
                    OCT_maxCash as 'maxCash'
                FROM 
                    ocTypes
                ORDER BY OCT_cost ASC
            ");
            $types->execute();
            $types = $types->fetchAll(PDO::FETCH_ASSOC);

            foreach ($types as $key => $value) {
                $value["cooldown"] = $this->timeLeft($value["cooldown"]);
                $types[$key] = $value;
            }

            $invites = $this->db->prepare("
                SELECT 
                    OCT_name as 'typeName', 
                    OC_leader as 'leader', 
                    OCU_role as 'role',
                    L_name as 'locationName',
                    OCU_id as 'inviteID'
                FROM 
                    ocUsers
                    INNER JOIN oc ON OC_id = OCU_oc
                    INNER JOIN ocTypes ON OCT_id = OC_type 
                    INNER JOIN locations ON OC_location = L_id 
                WHERE 
                    OCU_user = :user AND 
                    OCU_status = -1
            ");
            $invites->bindParam(":user", $this->user->id);
            $invites->execute();
            $invites = $invites->fetchAll(PDO::FETCH_ASSOC);

            foreach ($invites as $key => $value) {
                $u = new User($value["leader"]);
                $value["user"] = $u->user;
                $value["roleName"] = $this->roleDescriptions[$value["role"]];
                $invites[$key] = $value;
            }

            $this->html .= $this->page->buildElement("startOC", array(
                "types" => $types,
                "invites" => $invites
            ));
        }

        public function method_commit() {

            $settings = new Settings();

            $oc = $this->getUserOC();

            if (!$oc["isLeader"]) {
                $this->error("You are not the leader of this OC"); 
                return $this->method_roles();
            }

            if (!$oc["ready"]) {
                $this->error("You are not ready to commit this OC");
                return $this->method_roles();
            }   

            $chance = 25;

            foreach ($oc["members"] as $key => $value) {
                if (is_array($value["status"])) {
                    $chance += 25 / 4 * $value["status"]["level"]; 
                }
            }

            $money = round(mt_rand($oc["minCash"], $oc["maxCash"]) / 100 * $chance);
            $exp = round($oc["baseEXP"] / 100 * $chance);
            $failedEXP = round($oc["failedEXP"] / 100 * $chance);

            $success = mt_rand(1, 10000) / 100 < $chance;

            foreach ($oc["members"] as $member) {

                $u = new User($member["user"]["id"]);

                if ($success) {
                    $u->set("US_money", $u->info->US_money + $money);
                    $u->set("US_exp", $u->info->US_exp + $exp);

                    $u->newNotification($this->page->buildElement("successNotification", array(
                        "money" => $money, 
                        "exp" => $exp
                    )));
                } else {
                    $u->newNotification("Your OC failed!");
                    $u->set("US_exp", $u->info->US_exp + $failedEXP);
                    $money = 0;
                }

                $u->updateTimer("oc", $oc["cooldown"], true);

                $actionHook = new hook("userAction");
                $action = array(
                    "user" => $u->id, 
                    "module" => "oc", 
                    "id" => $member["role"], 
                    "success" => $success, 
                    "reward" => $money
                );
                $actionHook->run($action);

            }

            if ($success) {
                $this->error("You have successfully committed the OC", "success");
            } else {
                $this->error("Your OC failed!", "error");
            }

            $this->deleteOC($oc["id"]);

        }


        public function method_invite() {
            $accept = $this->methodData->id > 0;
            $id = abs($this->methodData->id);



            $invite = $this->db->prepare("
                SELECT * FROM ocUsers WHERE OCU_id = :id
            ");
            $invite->bindParam(":id", $id);
            $invite->execute();
            $invite = $invite->fetch(PDO::FETCH_ASSOC);

            if (!$invite["OCU_id"]) {
                return $this->error("This invite does not exist!");
            }

            if ($invite["OCU_user"] != $this->user->id) {
                return $this->error("This is not your invite!");
            }

            if ($accept) {
                if (!$this->user->checkTimer("oc")) {
                    return $this->error("You cant join an OC yet!");
                }
                $update = $this->db->prepare("UPDATE ocUsers SET OCU_status = 0 WHERE OCU_id = :id");
            } else {
                $update = $this->db->prepare("UPDATE ocUsers SET OCU_status = -1, OCU_user = 0 WHERE OCU_id = :id");
            }
            $update->bindParam(":id", $id);
            $update->execute();

        }

        public function method_leave() {
            $oc = $this->getUserOC();

            if ($oc && !$oc["isLeader"]) {

                $user = false;
                foreach ($oc["members"] as $member) {
                    if ($member && $member["user"]) {
                        if ($member["user"]["id"] == $this->user->id) $user = $member;
                    } 
                }
                $this->kick($this->user->id, $oc["id"], $user["item"]);
            }
        }

        public function method_disband() {
            $oc = $this->getUserOC();

            if ($oc["isLeader"]) {
                foreach ($oc["members"] as $member) {
                    if ($member["user"]) $this->kick($member["user"]["id"], $oc["id"], $member["item"]);
                }
                $this->deleteOC($oc["id"]);
            }
        }
        
        public function method_kick() {

            $oc = $this->getUserOC();

            if (!$oc["isLeader"]) {
                return $this->error("You are not the leader of this OC");
            }

            $user = false;

            foreach ($oc["members"] as $member) {
                if ($member["user"]["id"] == $this->methodData->user) $user = $member;
            }

            if (!$user) {
                return $this->error("This user is not part of this OC");
            }

            $this->kick($this->methodData->user, $oc["id"], $user["item"]);

        }

        public function method_roles() {

            $oc = $this->getUserOC();

            if (!$oc) {
                return $this->error("You are not part of an OC");
            }

            if (isset($this->methodData->item)) {
                $thisUser = $oc["thisUser"];
                $valid = false;

                foreach ($thisUser["inventory"] as $item) {
                    if ($thisUser["role"] != 4) {
                        if ($item["id"] == $this->methodData->item) {
                            $valid = $item;
                            $itemID = $item["id"]; 
                        }
                    } else {
                        if ($item["car"] == $this->methodData->item) {
                            $valid = $item;
                            $itemID = $item["id"]; 
                        }
                    }
                }
                

                if ($valid) {

                    if (!isset($valid["cost"])) $valid["cost"] = 0;

                    if ($valid["cost"] > $this->user->info->US_money) {
                        $this->error("You can't afford this item!");
                    } else {

                        if ($thisUser["role"] == 4) {
                            $this->removeCar($this->user->id, $this->methodData->item);
                        }

                        $update = $this->db->prepare("
                            UPDATE ocUsers SET OCU_status = :i WHERE OCU_user = :u AND OCU_oc = :id 
                        ");
                        $update->bindParam(":i", $itemID);
                        $update->bindParam(":u", $this->user->id);
                        $update->bindParam(":id", $oc["id"]);
                        $update->execute();
                        
                        $oc = $this->getUserOC();

                        $this->error("You are now ready!", "success");

                        $this->user->set("US_money", $this->user->info->US_money - $valid["cost"]);

                    }

                } else {
                    $this->error("You do not have this item!");
                }

            }

            $this->construct = false;

            if (isset($this->methodData->user)) {

                $userIsInOC = false;
                $user = new User(null, $this->methodData->user);

                foreach ($oc["members"] as $member) {
                    if (isset($user->info->U_id)) {
                        if (isset($member["user"]["id"]) && $member["user"]["id"] == $user->info->U_id) $userIsInOC = true;
                    }
                }

                if (!$oc["isLeader"]) {
                    $this->error("You are not the leader of this OC");
                } else if ($userIsInOC) {
                    $this->error("This user is already part of this OC");
                } else if (!isset($user->info->U_id)) {
                        $this->error("A user with this username does not exist");
                } else {

                    $role = $this->db->prepare("
                        SELECT * FROM ocUsers WHERE OCU_user = 0 AND OCU_oc = :oc AND OCU_role = :role LIMIT 0, 1
                    ");
                    $role->bindParam(":role", $this->methodData->role);
                    $role->bindParam(":oc", $oc["id"]);
                    $role->execute();
                    $role = $role->fetch(PDO::FETCH_ASSOC);

                    if (!$role["OCU_id"]) {
                        $this->error("There are no open roles of this type");
                    } else {
                        $update = $this->db->prepare("
                            UPDATE ocUsers SET OCU_user = :u WHERE OCU_id = :id
                        ");
                        $update->bindParam(":u", $user->info->U_id);
                        $update->bindParam(":id", $role["OCU_id"]);
                        $update->execute();
                        $this->error($user->info->U_name . " has been invited!", "success");
                        $oc = $this->getOC($oc["id"]);

                        $user->newNotification($this->page->buildElement("notification", array(
                            "username" => $this->user->info->U_name     
                        )));

                    }

                }
            }

            $this->html .= $this->page->buildElement("ocRoles", $oc);

        }

        public function method_plan() {

            if (!$this->user->checkTimer("oc")) {
                return $this->error("You cant start an OC yet!");
            }

            $type = $this->db->prepare("
                SELECT 
                    OCT_id as 'id', 
                    OCT_cost as 'cost', 
                    OCT_name as 'name', 
                    OCT_cooldown as 'cooldown',
                    OCT_successEXP as 'successEXP', 
                    OCT_failedEXP as 'failedEXP', 
                    OCT_minCash as 'minCash', 
                    OCT_maxCash as 'maxCash', 
                    OCU_id as 'existingOC'
                FROM 
                    ocTypes
                    LEFT OUTER JOIN ocUsers ON OCU_user = :user
                WHERE
                    OCT_id = :id
            ");
            $type->bindParam(":user", $this->user->id);
            $type->bindParam(":id", $this->methodData->id);
            $type->execute();
            $type = $type->fetch(PDO::FETCH_ASSOC);

            if (!$type["id"]) {
                return $this->error("This OC does not exist");
            }

            if ($type["existingOC"]) {
                return $this->error("You are already part of a OC");
            }
            
            if ($type["cost"] > $this->user->info->US_money) {
                return $this->error("You can't afford to plan this OC!");
            }  

            $insert = $this->db->prepare("
                INSERT INTO oc (
                    OC_leader, 
                    OC_type, 
                    OC_location, 
                    OC_time
                ) VALUES (
                    :user, 
                    :type, 
                    :location, 
                    UNIX_TIMESTAMP()
                );
            ");
            $insert->bindParam(":user", $this->user->id);
            $insert->bindParam(":location", $this->user->info->US_location);
            $insert->bindParam(":type", $type["id"]);
            $insert->execute();

            $id = $this->db->lastInsertId();

            $role = 1;
            $i = 0;
            foreach (array("leaders", "weapons", "explosives", "drivers") as $k => $t) {

                if ($role == 1 && $i == 0) {
                    $u = $this->user->id;
                    $s = 4;
                } else {
                    $u = 0;
                    $s = -1;
                }

                $insert = $this->db->prepare("
                    INSERT INTO ocUsers (
                        OCU_oc,
                        OCU_user,
                        OCU_role,
                        OCU_status
                    ) VALUES (
                        $id, 
                        $u, 
                        $role, 
                        $s
                    );
                ");
                $insert->execute();

                $i++;

                $role++;
            }

            $this->user->set("US_money", $this->user->info->US_money - $type["cost"]);

            return header("Location:?page=organizedCrime&action=roles");

        }


        public function deleteOC($oc) {
            $del = $this->db->prepare("
                DELETE FROM oc WHERE OC_id = :id;
                DELETE FROM ocUsers WHERE OCU_oc = :id;
            ");
            $del->bindParam(":id", $oc);
            $del->execute();
        }

        public function getOC($id) {
            $oc = $this->db->prepare("
                SELECT 
                    OC_id as 'id', 
                    OC_leader as 'leader', 
                    OC_type as 'ocType', 
                    OC_time as 'startTime',
                    OC_location as 'location',
                    L_name as 'locationName',
                    OCT_name as 'typeName', 
                    OCT_cooldown as 'cooldown',
                    OCT_cost as 'cost',
                    OCT_minCash as 'minCash', 
                    OCT_maxCash as 'maxCash', 
                    OCT_successEXP as 'baseEXP', 
                    OCT_failedEXP as 'failedEXP' 
                FROM 
                    oc 
                    INNER JOIN ocTypes ON (OCT_id = OC_type)
                    INNER JOIN locations ON (L_id = OC_location)
                WHERE 
                    OC_id = :id
            ");
            $oc->bindParam(":id", $id);
            $oc->execute();
            $oc = $oc->fetch(PDO::FETCH_ASSOC);

            $members = $this->db->prepare("
                SELECT 
                    OCU_user as 'user', 
                    OCU_role as 'role', 
                    OCU_status as 'item',
                    OCU_status as 'status'
                FROM 
                    ocUsers
                WHERE 
                    OCU_oc = :id
            ");
            $members->bindParam(":id", $id);
            $members->execute();

            $members = $members->fetchAll(PDO::FETCH_ASSOC);

            $ready = true;
            $full = true;
            $thisUser = array();

            foreach ($members as $key => $value) {
                $value["leader"] = false;
                $value["desc"] = $this->roles[$value["role"]];
                if ($value["user"]) {
                    $user =  new User($value["user"]);

                    $value["user"] = $user->user;
                    $value["leader"] = $oc["leader"] == $user->user["id"];
                    if ($this->user->id == $user->user["id"]) $thisUser = $value;

                    if ($value["status"] == -1) {
                        $value["status"] = "Invited";
                        $ready = false;
                    } else if ($user->info->US_location != $oc["location"]) {
                        $value["status"] = "Traveling";
                        $ready = false;
                    } else if ($value["status"] == 0) {
                        $value["status"] = "Selecting Item";
                        $ready = false;
                    } else {
                        $value["status"] = $this->getItem($value["role"], $value["status"]);
                    }
                } else {
                    $ready = false;
                    $value["status"] = "Waiting";
                    $value["empty"] = true;
                    $full = false;
                } 

                $members[$key] = $value;
            }

            $oc["members"] = $members;
            $oc["isLeader"] = $this->user->id == $oc["leader"];
            $oc["ready"] = $ready;
            $oc["full"] = $full;

            $thisUser["inventory"] = $this->getItems($oc, $thisUser);

            $oc["thisUser"] = $thisUser;

            return $oc;

        }

        public function getItem($role, $item) {
            switch ($role) {
                case 1:
                    return "Planning";
                break;
                case 2:
                case 3:
                    $s = new Settings();
                    if ($role == 2) {
                        $key = "ocWep" . $item;
                    } else {
                        $key = "ocExp" . $item;
                    }
                    return array(
                        "id" => $item,
                        "level" => $item,
                        "name" => $s->loadSetting($key . "Name"),
                        "cost" => $s->loadSetting($key . "Cost")
                    );
                break;
                case 4:
                    $car = $this->db->select("
                        SELECT 
                            CA_id as 'id', 
                            CA_value as 'level', 
                            CA_name as 'name', 
                            'car' as 'img'
                        FROM 
                            cars 
                        WHERE CA_id = :id
                    ", array(
                        ":id" => $item
                    ));

                    $value = $car["level"];

                    if ($value > $this->_settings->loadSetting("ocCar4level")) {
                        $level = 4;
                    } else if ($value > $this->_settings->loadSetting("ocCar3level")) {
                        $level = 3;
                    } else if ($value > $this->_settings->loadSetting("ocCar2level")) {
                        $level = 2;
                    } else {
                        $level = 1;
                    }

                    $car["level"] = $level;

                    return $car;
                break;
            }
        }

        public function getItems($oc, $user) {

            $s = new Settings();

            if (!$user) return;

            switch ($user["role"]) {
                case 2:
                    return array(
                        array(
                            "id" => 1,
                            "name" => $s->loadSetting("ocWep1Name"),
                            "cost" => $s->loadSetting("ocWep1Cost")
                        ),
                        array(
                            "id" => 2,
                            "name" => $s->loadSetting("ocWep2Name"),
                            "cost" => $s->loadSetting("ocWep2Cost")
                        ),
                        array(
                            "id" => 3,
                            "name" => $s->loadSetting("ocWep3Name"),
                            "cost" => $s->loadSetting("ocWep3Cost")
                        ),
                        array(
                            "id" => 4,
                            "name" => $s->loadSetting("ocWep4Name"),
                            "cost" => $s->loadSetting("ocWep4Cost")
                        ),
                    );
                break;
                case 3:
                    return array(
                        array(
                            "id" => 1,
                            "name" => $s->loadSetting("ocExp1Name"),
                            "cost" => $s->loadSetting("ocExp1Cost")
                        ),
                        array(
                            "id" => 2,
                            "name" => $s->loadSetting("ocExp2Name"),
                            "cost" => $s->loadSetting("ocExp2Cost")
                        ),
                        array(
                            "id" => 3,
                            "name" => $s->loadSetting("ocExp3Name"),
                            "cost" => $s->loadSetting("ocExp3Cost")
                        ),
                        array(
                            "id" => 4,
                            "name" => $s->loadSetting("ocExp4Name"),
                            "cost" => $s->loadSetting("ocExp4Cost")
                        ),
                    );
                break;
                case 4:
                    $query = $this->db->prepare("
                        SELECT 
                            CA_name as 'name', 
                            CA_id as 'id', 
                            GA_id as 'car'
                        FROM garage 
                        INNER JOIN cars ON (GA_car = CA_id)
                        WHERE GA_damage = 0 AND GA_uid = :u AND GA_location = :l
                    ");
                    $query->bindParam(":l", $oc["location"]);
                    $query->bindParam(":u", $user["user"]["id"]);
                    $query->execute();

                    return $query->fetchAll(PDO::FETCH_ASSOC);
                break;
            }
        }

        public function getUserOC() {

            $inOC = $this->db->prepare("
                SELECT * FROM ocUsers WHERE OCU_user = :user AND OCU_status != -1
            ");
            $inOC->bindParam(":user", $this->user->id);
            $inOC->execute();
            $inOC = $inOC->fetch(PDO::FETCH_ASSOC);

            if (!$inOC["OCU_oc"]) {
                return false;
            }

            return $this->getOC($inOC["OCU_oc"]);
        }

        public function kick($user, $oc, $item) {
            
            if ($item > 0) {
                $this->addItem($oc, $user, $item);
            }

            $update = $this->db->prepare("UPDATE ocUsers SET OCU_user = 0, OCU_status = -1 WHERE OCU_oc = :o AND OCU_user = :u");
            $update->bindParam(':u', $user);
            $update->bindParam(':o', $oc);
            $update->execute();

            $u = new User($user);

            $u->newNotification("You were removed from your OC, any costs were returned to you.");

        }

        public function addItem($ocID, $user, $item) {

            $u = new User($user);
            $oc = $this->getOC($ocID);

            foreach ($oc["members"] as $member) {
                if ($member["user"] && $member["user"]["id"] == $user) {
                    switch  ($member["role"]) {
                        case 1:
                            $u->set("US_money", $u->info->US_money + $oc["cost"]);
                        break; 
                        case 2: 
                        case 3: 
                            $item = $this->getItem($member["role"], $item);
                            $u->set("US_money", $u->info->US_money + $item["cost"]);
                        break; 
                        case 4: 
                            $this->addCar($user, $member["status"]["id"], $oc["location"]);
                        break;
                    }
                }
            }

        }
        
        public function addCar($user, $car, $location) {

            $query = $this->db->prepare("
                    INSERT INTO garage (GA_uid, GA_car, GA_damage, GA_location) VALUES (:u, :c, 0, :l); 
                ");
            $query->bindParam(':u', $user);
            $query->bindParam(':c', $car);
            $query->bindParam(':l', $location);
            $query->execute();

        }
        
        public function removeCar($user, $car) {

            $query = $this->db->prepare("
                DELETE FROM garage WHERE GA_uid = :u AND GA_id = :i; 
            ");
            $query->bindParam(':u', $user);
            $query->bindParam(':i', $car);
            $query->execute();

        }

        public function buildArray($count) {
            $rtn = array();
            $i = 0;
            while ($i < $count) {
                $rtn[] = $i;
                $i++;
            }
            return $rtn;
        }

    }

?>