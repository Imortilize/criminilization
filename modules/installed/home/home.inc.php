<?php

/**
* A new home page for your game rather then the default login screen.
*
* @package Home Page
* @author NIF
* @version 1.0.0
*/


class home extends module {

	public function constructModule() {
		$settings = new Settings();
		$showTop4 = $settings->loadSetting("showTop4Players", true, 1);
		$showNews = $settings->loadSetting("showLatestNews", true, 1);
		$showLogin = $settings->loadSetting("showLogin", true, 1);


		if ($showNews) {
			
            $news = $this->db->select("
                SELECT * FROM gameNews INNER JOIN users ON (GN_author = U_id) ORDER BY GN_date DESC LIMIT 0, 1
            ");
                
            $author = new user($news['GN_author']);
            
            $news = array(
                "title" => $news['GN_title'],
                "authorID" => $news['U_id'],
                "user" => $author->user,
                "date" => $this->date($news['GN_date']),
                "text" => $news['GN_text']
            );
                
        } else {
        	$news = false;
        }

            
		if ($showTop4) {
			$top4 = $this->db->selectAll("
				SELECT U_id FROM users INNER JOIN userStats ON (U_id = US_id) WHERE U_status = 1 AND U_userLevel = 1 ORDER BY US_exp DESC LIMIT 0, 4
			");
			foreach ($top4 as $key => $user) {
				$user = new User($user["U_id"]);

				$top4[$key] = array(
					"user" => $user->user
				);
			}
		} else {
			$top4 = false;
		}

		$this->html .= $this->page->buildElement("home", array(
			"login" => $showLogin,
			"news" => $news,
			"top4" => $top4, 
			"loginCustomBBCode" => $settings->loadSetting("loginCustomBBCode", true, ""), 
			"loginScreenshot1" => $settings->loadSetting("loginScreenshot1", true, ""), 
			"loginScreenshot2" => $settings->loadSetting("loginScreenshot2", true, ""), 
			"loginScreenshot3" => $settings->loadSetting("loginScreenshot3", true, ""), 
			"loginScreenshot1text" => $settings->loadSetting("loginScreenshot1text", true, ""), 
			"loginScreenshot2text" => $settings->loadSetting("loginScreenshot2text", true, ""), 
			"loginScreenshot3text" => $settings->loadSetting("loginScreenshot3text", true, "")
		));
	}

}
