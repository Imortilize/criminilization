<?php

/**
* A page to display your terms and conditions including some sample text
*
* @package terms
* @author NIF
* @version 1.0.0
*/


class terms extends module {

	public function constructModule() {
		$settings = new Settings();
		$this->html .= $this->page->buildElement("terms", array(
			"terms" => $settings->loadSetting("termsOfUse", 1, "")
		));
	}

}
