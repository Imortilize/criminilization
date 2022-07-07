<?php

/**
* A page to display your terms and conditions including some sample text
*
* @package terms
* @author NIF
* @version 1.0.0
*/


class adminModule {

	public function method_terms() {

		$settings = new settings();

		if (isset($this->methodData->submit)) {
			$settings->update("termsOfUse", $this->methodData->termsOfUse);
			$this->html .= $this->page->buildElement("success", array(
				"text" => "Options updated."
			));
		}

		$output = array(
			"termsOfUse" => $settings->loadSetting("termsOfUse")
		);

		$this->html .= $this->page->buildElement("options", $output);

	}

}