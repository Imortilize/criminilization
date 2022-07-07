<?php

    class adminModule {

        public function method_cars() {

            if (isset($this->methodData->cars)) {
                foreach ($this->methodData->cars as $car => $data) {
                    $id = (int) trim($car);

                    $this->db->update("
                        UPDATE cars SET CA_pcSuccess = :success, CA_pcContinue = :continue, CA_pcFail = :fail WHERE CA_id = :id
                    ", array(
                        ":id" => $id, 
                        ":success" => $data["success"],
                        ":continue" => $data["continue"],
                        ":fail" => $data["fail"]
                    ));

                }   

                $this->html .= $this->page->buildElement("success", array(
                    "text" => "Cars updated."
                ));
            }

            $output = array();

            $output["cars"] = $this->db->selectAll("
                SELECT 
                    CA_id as 'id',
                    CA_name as 'name',
                    CA_value as 'value',
                    CA_pcSuccess as 'success',
                    CA_pcContinue as 'continue',
                    CA_pcFail as 'fail'
                FROM cars
                ORDER BY CA_value DESC
            ");

            $this->html .= $this->page->buildElement("cars", $output);
        }

        public function method_options() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("pcMinReward", $this->methodData->minReward);
                $settings->update("pcMaxReward", $this->methodData->maxReward);
                $settings->update("pcCooldown", $this->methodData->cooldown);
                $settings->update("pcJailTime", $this->methodData->jailTime);
                $settings->update("pcMinDamage", $this->methodData->minDamage);
                $settings->update("pcExpGain", $this->methodData->expGain);
                $settings->update("pcMaxDamage", $this->methodData->maxDamage);

               
                $this->html .= $this->page->buildElement("success", array(
                    "text" => "Options updated."
                ));
            }

            $output = array(
                "minReward" => $settings->loadSetting("pcMinReward"),
                "maxReward" => $settings->loadSetting("pcMaxReward"),
                "cooldown" => $settings->loadSetting("pcCooldown"),
                "jailTime" => $settings->loadSetting("pcJailTime"),
                "minDamage" => $settings->loadSetting("pcMinDamage"),
                "expGain" => $settings->loadSetting("pcExpGain"),
                "maxDamage" => $settings->loadSetting("pcMaxDamage")
            );

            $this->html .= $this->page->buildElement("options", $output);

        }

    }
