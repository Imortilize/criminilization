<?php

    class adminModule {

        public function method_options() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("crushBullets", $this->methodData->crushBullets);
                $this->html .= $this->page->buildElement("success", array(
                    "text" => "Garage options updated."
                ));
            }

            $output = array(
                "crushBullets" => $settings->loadSetting("crushBullets")
            );

            $this->html .= $this->page->buildElement("garageOptions", $output);

        }

    }

?>