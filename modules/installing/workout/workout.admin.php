<?php

    class adminModule {

        public function method_options() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("workoutResetEnergy", $this->methodData->workoutResetEnergy);
                $settings->update("workoutResetWill", $this->methodData->workoutResetWill);
                $this->html .= $this->page->buildElement("success", array(
                    "text" => "Options updated."
                ));
            }

            $output = array(
                "workoutResetEnergy" => $settings->loadSetting("workoutResetEnergy"),
                "workoutResetWill" => $settings->loadSetting("workoutResetWill")
            );

            $this->html .= $this->page->buildElement("options", $output);

        }

    }