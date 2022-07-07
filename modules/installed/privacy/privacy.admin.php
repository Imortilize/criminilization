<?php

/**
* A page to display your privacy policy including some sample text
*
* @package privacy
* @author NIF
* @version 1.0.0
*/


class adminModule {

	public function method_privacy() {

		$settings = new settings();

		if (isset($this->methodData->submit)) {
			$settings->update("privacyPolicy", $this->methodData->privacyPolicy);
			$this->html .= $this->page->buildElement("success", array(
				"text" => "Options updated."
			));
		}

		$output = array(
			"privacyPolicy" => $settings->loadSetting("privacyPolicy")
		);

		$this->html .= $this->page->buildElement("options", $output);

	}

}