<?php
class adminModule {

    public function method_new ()
    {
        $settings = new settings();
        $passowrdBots = $settings->loadSetting("botsPassword", true, "harbzali@gmail.com");

        require __DIR__.'/names.php';
        if (isset($this->methodData->submit)) {
            for ($i=1;$this->methodData->bots >= $i; $i++) {
                $random_name = generateName();
                $email = $random_name.'@'.$random_name.'.bot';
                $password = $passowrdBots;
                $checkUser = @new user(null, $random_name);
                if(isset($checkUser->info->U_id)){
                    $random_name = $random_name.$i;
                    $email = $random_name.$i.'@'.$random_name.'.bot';
                }
                $addUser = $this->db->prepare("
                    INSERT INTO users (U_name, U_email, U_userLevel, U_status, U_bot)
                    VALUES (:username, :email, 1, 1, 1)
                ");
                $addUser->bindParam(':username', $random_name);
                $addUser->bindParam(':email', $email);
                $addUser->execute();
                $id = $this->db->lastInsertId();

                $user = new user();
                $encryptedPassword = $user->encrypt($id . $password);
                $addUserPassword = $this->db->prepare("
                    UPDATE users SET U_password = :password WHERE U_id = :id
                ");
                $addUserPassword->bindParam(':id', $id);
                $addUserPassword->bindParam(':password', $encryptedPassword);
                $addUserPassword->execute();
                $this->db->query("INSERT INTO userStats (US_id) VALUES (" . $id . ")");
            }
            $this->html .= $this->page->buildElement("success", array("text" => "You have successfully added ".number_format($this->methodData->bots).' bots'));
        }

        $botsInGame = $this->db->select("SELECT
            SUM(U_bot) as 'bots'
            FROM users
        ");

        $output = array(
            "bots" => number_format((int) $botsInGame['bots'])
        );

        $this->html .= $this->page->buildElement("newBot", $output);
    }

    public function method_settings () {
        $settings = new settings();

        if (isset($this->methodData->submit)) {
            $settings->update("botsPassword", $this->methodData->botsPassword);
            $settings->update("ocBots", $this->methodData->ocBots);
            $settings->update("botsBullets", $this->methodData->botsBullets);
            $settings->update("botsAttack", $this->methodData->botsAttack);

            $this->html .= $this->page->buildElement("success", array(
                "text" => "Fake players settings updated."
            ));
        }

        $output = array(
            "botsPassword" => $settings->loadSetting("botsPassword", true, "harbzali@gmail.com"),
            "ocBots" => $settings->loadSetting("ocBots", true, 0),
            "botsBullets" => $settings->loadSetting("botsBullets", true, 0),
            "botsAttack" => $settings->loadSetting("botsAttack", true, 0),
        );
        $this->html .= $this->page->buildElement("settings", $output);
    }

    public function method_toUser() {

        if (isset($this->methodData->submit)) {
            $user = new user(null, $this->methodData->user);
            if (!isset($user->info->U_id)) {
                return $this->html .= $this->page->buildElement('error', array(
                    "text" => "This bot does not exist"
                ));
            }
            if ($user->info->U_bot == 0) {
                return $this->html .= $this->page->buildElement('error', array(
                    "text" => "This player is not a bot"
                ));
            }

            $this->db->update("UPDATE users set U_bot = 0 where U_id = :user", array(
                ":user" => $user->id
            ));
            $this->html .= $this->page->buildElement("success", array(
                "text" => "The bot was successfully changed to a user."
            ));
        }

        $this->html .= $this->page->buildElement("toUser");
    }


}
