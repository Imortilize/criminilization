<?php

/**
* A page to display your privacy policy including some sample text
*
* @package privacy
* @author NIF
* @version 1.0.0
*/


class privacy extends module {
	public function constructModule() {
		$settings = new Settings();
		$this->html .= $this->page->buildElement("privacy", array(
			"privacy" => $settings->loadSetting("privacyPolicy", 1, "")
		));
	}
}
