<?php

/**
* A new home page for your game rather then the default login loginScreen.
*
* @package Home Page
* @author NIF
* @version 1.0.0
*/


	class adminModule {

		public function method_settings() {

			$settings = new settings();

			if (isset($this->methodData->submit)) {
				$settings->update("showTop4Players", $this->methodData->showTop4Players);
				$settings->update("showLatestNews", $this->methodData->showLatestNews);
				$settings->update("showLatestNews", $this->methodData->showLatestNews);
				$settings->update("loginScreenshot1", $this->methodData->loginScreenshot1);
				$settings->update("loginScreenshot2", $this->methodData->loginScreenshot2);
				$settings->update("loginScreenshot3", $this->methodData->loginScreenshot3);
				$settings->update("loginScreenshot1text", $this->methodData->loginScreenshot1text);
				$settings->update("loginScreenshot2text", $this->methodData->loginScreenshot2text);
				$settings->update("loginScreenshot3text", $this->methodData->loginScreenshot3text);
				$settings->update("loginCustomBBCode", $this->methodData->loginCustomBBCode);

				$this->html .= $this->page->buildElement("success", array(
					"text" => "Options updated."
				));
			}

			$output = array(
				"showTop4Players" => $settings->loadSetting("showTop4Players"),
				"showLatestNews" => $settings->loadSetting("showLatestNews"),
				"showLatestNews" => $settings->loadSetting("showLatestNews"),
				"loginCustomBBCode" => $settings->loadSetting("loginCustomBBCode"),
				"loginScreenshot1" => $settings->loadSetting("loginScreenshot1"),
				"loginScreenshot2" => $settings->loadSetting("loginScreenshot2"),
				"loginScreenshot3" => $settings->loadSetting("loginScreenshot3"),
				"loginScreenshot1text" => $settings->loadSetting("loginScreenshot1text"),
				"loginScreenshot2text" => $settings->loadSetting("loginScreenshot2text"),
				"loginScreenshot3text" => $settings->loadSetting("loginScreenshot3text")
			);

			$this->html .= $this->page->buildElement("options", $output);

		}

	}