<?php

    class adminModule {

        public function method_options() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("missionMinExp", $this->methodData->missionMinExp);
                $settings->update("missionMaxExp", $this->methodData->missionMaxExp);
                $settings->update("missionMinMoney", $this->methodData->missionMinMoney);
                $settings->update("missionMaxMoney", $this->methodData->missionMaxMoney);
                $settings->update("missionMinBullets", $this->methodData->missionMinBullets);
                $settings->update("missionMaxBullets", $this->methodData->missionMaxBullets);
               
                $this->html .= $this->page->buildElement("success", array(
                    "text" => "Detective options updated."
                ));
            }

            $output = array(
                "missionMinExp" => $settings->loadSetting("missionMinExp"),
                "missionMaxExp" => $settings->loadSetting("missionMaxExp"),
                "missionMinMoney" => $settings->loadSetting("missionMinMoney"),
                "missionMaxMoney" => $settings->loadSetting("missionMaxMoney"),
                "missionMinBullets" => $settings->loadSetting("missionMinBullets"),
                "missionMaxBullets" => $settings->loadSetting("missionMaxBullets")
               
            );

            $this->html .= $this->page->buildElement("options", $output);

        }

    }
