<?php

    class escrow extends module {
        
        public $allowedMethods = array(
            'user' => array('type'=>'request'),
            'item' => array('type'=>'get'),
            'remove' => array('type'=>'get'),
            'qty' => array('type'=>'request'),
            'password' => array('type'=>'post'),
            'type' => array('type'=>'post')
        );

        public function constructModule() {

            $data = array();

            if ($this->user->info->US_escrowWith) {
                $user = new User($this->user->info->US_escrowWith);
                if ($user->info->US_escrowWith == $this->user->id) {
                    return $this->method_negotiate();
                } else {
                    $data["user"] = $user->user;
                }
            }

            $invites = $this->db->selectAll("
                SELECT * FROM userStats WHERE US_escrowWith = :id
            ", array(
                ":id" => $this->user->id
            ));

            $data["invites"] = array();

            foreach ($invites as $invite) {
                $user = new User($invite["US_id"]);
                $data["invites"][] = array(
                    "id" => $invite["US_id"],
                    "user" => $user->user
                );
            }

            $this->html .= $this->page->buildElement('inviteUser', $data);

        }

        public function method_accept() {
            if (!isset($this->methodData->user)) {
                return $this->error("This user does not exist");
            }

            $user = new User($this->methodData->user);

            if (!isset($user->info->U_id)) {
                return $this->error("This user does not exist");
            }

            if ($user->info->U_id == $this->user->id) {
                return $this->error("You can't send an invite to yourself!");
            }

            if ($user->info->US_escrowWith != $this->user->id) {
                return $this->error("This user has not invited you!");
            }

            if (
                $user->info->US_escrowWith == $this->user->info->US_id &&
                $this->user->info->US_escrowWith == $user->info->US_id
            ) {
                return $this->error("You have already accepted the invite!");
            }


            $user->set("US_escrowStatus", 0);
            $user->set("US_escrowItems", "");

            $this->user->set("US_escrowItems", "");
            $this->user->set("US_escrowStatus", 0);
            $this->user->set("US_escrowWith", $user->id);

            $user->newNotification($this->page->buildElement("acceptNotification", array(
                "user" => $this->user->user
            )));

            $this->error("You have accepted the request", "success");

        }

        public function method_decline() {
            if (!isset($this->methodData->user)) {
                return $this->error("This user does not exist");
            }

            $user = new User($this->methodData->user);

            if (!isset($user->info->U_id)) {
                return $this->error("This user does not exist");
            }

            if ($user->info->U_id == $this->user->id) {
                return $this->error("You can't send an invite to yourself!");
            }

            if ($user->info->US_escrowWith != $this->user->id) {
                return $this->error("This user has not invited you!");
            }

            $user->set("US_escrowStatus", 0);
            $user->set("US_escrowItems", "");
            $user->set("US_escrowWith", 0);

            $user->newNotification($this->page->buildElement("declineNotification", array(
                "user" => $this->user->user
            )));

            $this->error("You have declined the request", "warning");

        }

        public function method_negotiate() {
            $this->construct = false;

            $user = new User($this->user->info->US_escrowWith);

            if ($user->info->US_escrowWith != $this->user->info->US_id) {
                return $this->error("This user is not ready to negotiate!");
            }

            $this->html .= $this->page->buildElement('escrow', array(
                "users" => array(
                    array(
                        "user" => $this->user->user, 
                        "items" => $this->buildEscrowItems($this->user->info->US_escrowItems, true),
                        "status" =>$this->user->info->US_escrowStatus == 1
                    ),
                    array(
                        "user" => $user->user, 
                        "items" => $this->buildEscrowItems($user->info->US_escrowItems),
                        "status" =>$user->info->US_escrowStatus == 1
                    )
                )
            ));
            
        }

        public function buildEscrowItems($json, $isUser = false) {
            if (!$json) $json = "[]";
            $data = json_decode($json, true);
            $itemInfo = $this->getEscrowItems();
            foreach ($data as $key => $item) {
                $info = $itemInfo[$item["type"]]["name"]($item);
                $item["name"] = $info["desc"];
                $item["info"] = $info["info"];
                $item["isUser"] = $isUser;
                $data[$key] = $item;
            }

            return $data;
        }

        public function getEscrowItems() {
            $hook = new Hook("escrowItem");
            $items = $hook->run();

            $data = array();
            foreach ($items as $item) {
                $data[$item["id"]] = $item;
            }

            return $data;
        }

        public function cancleEscrow() {

            $return = false;

            if ($this->user->info->US_escrowWith) {
                $user = new User($this->user->info->US_escrowWith);

                if ($user->info->US_escrowWith == $this->user->id) {

                    $iGet = $this->buildEscrowItems($this->user->info->US_escrowItems);
                    $theyGet = $this->buildEscrowItems($user->info->US_escrowItems);

                    foreach ($iGet as $item) {
                        $itemInfo = $this->getEscrowItems()[$item["type"]];
                        $itemInfo["add"]($this->user, $item["qty"]);
                    }

                    foreach ($theyGet as $item) {
                        $itemInfo = $this->getEscrowItems()[$item["type"]];
                        $itemInfo["add"]($user, $item["qty"]);
                    }

                    $user->set("US_escrowStatus", 0);
                    $user->set("US_escrowItems", "");
                    $user->set("US_escrowWith", 0);

                    $return = true;

                }

                $user->newNotification($this->page->buildElement("cancleNotification", array(
                    "user" => $this->user->user, 
                    "return" => $return
                )));
                $this->user->set("US_escrowItems", "");
                $this->user->set("US_escrowStatus", 0);
                $this->user->set("US_escrowWith", 0);
            }

        }

        public function process() {

            $user = new User($this->user->info->US_escrowWith);

            if ($user->info->US_escrowWith != $this->user->info->US_id) {
                return $this->error("This user is not ready to negotiate!");
            }

            if ($user->info->US_escrowStatus == 1 && $this->user->info->US_escrowStatus == 1) {
                $iGet = $this->buildEscrowItems($user->info->US_escrowItems);
                $theyGet = $this->buildEscrowItems($this->user->info->US_escrowItems);

                foreach ($iGet as $item) {
                    $itemInfo = $this->getEscrowItems()[$item["type"]];
                    $itemInfo["add"]($this->user, $item["qty"]);
                }

                foreach ($theyGet as $item) {
                    $itemInfo = $this->getEscrowItems()[$item["type"]];
                    $itemInfo["add"]($user, $item["qty"]);
                }

                $this->user->set("US_escrowItems", "");
                $this->user->set("US_escrowStatus", 0);
                $this->user->set("US_escrowWith", 0);

                $user->set("US_escrowItems", "");
                $user->set("US_escrowStatus", 0);
                $user->set("US_escrowWith", 0);

                $this->user->newNotification("Your escrow has been completed, the items were send successfully!");
                $user->newNotification("Your escrow has been completed, the items were send successfully!");

            }

        }

        public function method_submit() {
            if (isset($this->methodData->password)) {
                $encrypt = $this->user->encrypt($this->user->info->U_id . $this->methodData->password);
                if ($encrypt != $this->user->info->U_password) {
                    return $this->error("The password you entered is incorrect");
                }

                $this->user->set("US_escrowStatus", 1);
                $this->error("You have submited your offer!", "success");

                $this->process();

            } else {
                $this->html .= $this->page->buildElement("submitOffer", array());
            }
        }

        public function method_cancle() {
            if ($this->user->info->US_escrowWith) {
                $this->cancleEscrow();
                $this->error("You have cancled the escrow!", "success");
            }
        }

        public function method_invite() {
            if (!isset($this->methodData->user)) {
                return $this->error("This user does not exist");
            }

            $user = new User(null, $this->methodData->user);

            if (!isset($user->info->U_id)) {
                return $this->error("This user does not exist");
            }

            if ($user->info->U_id == $this->user->id) {
                return $this->error("You can't send an invite to yourself!");
            }

            $this->cancleEscrow();
            $this->user->set("US_escrowWith", $user->info->U_id);

            $user->newNotification($this->page->buildElement("inviteNotification", array(
                "user" => $this->user->user
            )));

            $this->error("You have sent the escrow request!", "success");

        }

        public function method_remove() {

            $user = new User($this->user->info->US_escrowWith);

            if ($user->info->US_escrowWith != $this->user->info->US_id) {
                return $this->error("This user is not ready to negotiate!");
            }

            $data = $this->buildEscrowItems($this->user->info->US_escrowItems);

            foreach ($data as $key => $value) {
                if (
                    isset($this->methodData->qty) && 
                    isset($this->methodData->item) && 
                    $value["qty"]["qty"] == $this->methodData->qty && 
                    $value["qty"]["item"] == $this->methodData->item && 
                    $value["type"] == $this->methodData->remove
                ) {
                    unset($data[$key]);
                    break;
                } else if (
                    $value["qty"] == $this->methodData->qty && 
                    $value["type"] == $this->methodData->remove
                ) {
                    unset($data[$key]);
                    break;
                }
            }

            $itemInfo = $this->getEscrowItems()[$this->methodData->remove];
            $itemInfo["add"]($this->user, $value["qty"]);

            $user = new User($this->user->info->US_escrowWith);
            $user->set("US_escrowStatus", 0);
            $this->user->set("US_escrowStatus", 0);

            $this->user->set("US_escrowItems", json_encode($data));
        }

        public function method_add() {

            $user = new User($this->user->info->US_escrowWith);

            if ($user->info->US_escrowWith != $this->user->info->US_id) {
                return $this->error("This user is not ready to negotiate!");
            }

            $data = $this->buildEscrowItems($this->user->info->US_escrowItems);
            
            $set = false;

            $itemInfo = $this->getEscrowItems()[$this->methodData->type];
            
            $valid = $itemInfo["validate"]($this->user, $this->methodData->qty);

            if (!$valid) {
                return $this->error("You dont have this to offer!");
            }

            $qty = abs(intval($this->methodData->qty));


            if ($itemInfo["type"] == "listQty") {
                $itemInfo["remove"]($this->user, $this->methodData->qty);
            } else if ($itemInfo["type"] == "qty") {
                $itemInfo["remove"]($this->user, $this->methodData->qty);
                foreach ($data as $key => $value) {
                    if ($value["type"] == $this->methodData->type) {
                        $data[$key]["qty"] += $qty;
                        $set = true;
                    }
                }
            } else if ($itemInfo["type"] == "list") {
                $itemInfo["remove"]($this->user, $this->methodData->qty);
                foreach ($data as $key => $value) {
                    if (
                        $value["type"] == $this->methodData->type && 
                        $value["qty"] == $qty
                    ) {
                        $set = true;
                    }
                }
            }
            
            $user = new User($this->user->info->US_escrowWith);
            $user->set("US_escrowStatus", 0);
            $this->user->set("US_escrowStatus", 0);

            if (!$set) {
                $data[] = array(
                    "qty" => $this->methodData->qty, 
                    "type" => $this->methodData->type
                );
            } 


            $this->user->set("US_escrowItems", json_encode($data));
        }
        
        public function method_clear() {
            $this->user->set("US_escrowItems", "[]");
        }

        public function method_addItem() {
            $items = $this->getEscrowItems();

            $html = "";

            foreach ($items as $item) {
                $item["isQty"] = $item["type"] == "qty";
                $item["isList"] = $item["type"] == "list";
                $item["isListQty"] = $item["type"] == "listQty";
                $item["info"] = $item["name"](array());
                
                $html .= $this->page->buildElement("item", $item);
            }

            $this->html .= $this->page->buildElement("addItem", array(
                "items" => $html
            ));

        }   
        
    }
 