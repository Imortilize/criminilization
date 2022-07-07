<?php

    class adminModule {

        public function method_options() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {

                $inputs = array("scavenge_rounds", "scavenge_chance_failed", "scavenge_chance_points", "scavenge_chance_money", "scavenge_chance_bullets", "scavenge_reward_min_points", "scavenge_reward_min_money", "scavenge_reward_min_bullets", "scavenge_reward_max_points", "scavenge_reward_max_money", "scavenge_reward_max_bullets");

                foreach ($inputs as $input) {
                    $this->methodData->$input = abs(intval($this->methodData->$input));
                }

                $total = $this->methodData->scavenge_chance_failed + $this->methodData->scavenge_chance_points + $this->methodData->scavenge_chance_money + $this->methodData->scavenge_chance_bullets;

                if ($total != 100) {
                    $this->error("All the chances must add up to 100!");

                } else if ($this->methodData->scavenge_reward_min_points > $this->methodData->scavenge_reward_max_points) {
                    $this->error("Minimum money reward must be less then the max reward!");

                } else if ($this->methodData->scavenge_reward_min_money > $this->methodData->scavenge_reward_max_money) {
                    $this->error("Minimum points reward must be less then the max reward!");

                } else if ($this->methodData->scavenge_reward_min_bullets > $this->methodData->scavenge_reward_max_bullets) {
                    $this->error("Minimum bullets reward must be less then the max reward!");

                } else {

                    $settings->update("scavenge_rounds", $this->methodData->scavenge_rounds);
                    $settings->update("scavenge_chance_failed", $this->methodData->scavenge_chance_failed);
                    $settings->update("scavenge_chance_points", $this->methodData->scavenge_chance_points);
                    $settings->update("scavenge_chance_money", $this->methodData->scavenge_chance_money);
                    $settings->update("scavenge_chance_bullets", $this->methodData->scavenge_chance_bullets);
                    $settings->update("scavenge_reward_min_points", $this->methodData->scavenge_reward_min_points);
                    $settings->update("scavenge_reward_min_money", $this->methodData->scavenge_reward_min_money);
                    $settings->update("scavenge_reward_min_bullets", $this->methodData->scavenge_reward_min_bullets);
                    $settings->update("scavenge_reward_max_points", $this->methodData->scavenge_reward_max_points);
                    $settings->update("scavenge_reward_max_money", $this->methodData->scavenge_reward_max_money);
                    $settings->update("scavenge_reward_max_bullets", $this->methodData->scavenge_reward_max_bullets);
                    $this->html .= $this->page->buildElement("success", array(
                        "text" => "Garage options updated."
                    ));
                }
            }

            $output = array(
                "scavenge_rounds" => $settings->loadSetting("scavenge_rounds"),
                "scavenge_chance_failed" => $settings->loadSetting("scavenge_chance_failed"),
                "scavenge_chance_points" => $settings->loadSetting("scavenge_chance_points"),
                "scavenge_chance_money" => $settings->loadSetting("scavenge_chance_money"),
                "scavenge_chance_bullets" => $settings->loadSetting("scavenge_chance_bullets"),
                "scavenge_reward_min_points" => $settings->loadSetting("scavenge_reward_min_points"),
                "scavenge_reward_min_money" => $settings->loadSetting("scavenge_reward_min_money"),
                "scavenge_reward_min_bullets" => $settings->loadSetting("scavenge_reward_min_bullets"),
                "scavenge_reward_max_points" => $settings->loadSetting("scavenge_reward_max_points"),
                "scavenge_reward_max_money" => $settings->loadSetting("scavenge_reward_max_money"),
                "scavenge_reward_max_bullets" => $settings->loadSetting("scavenge_reward_max_bullets")
            );

            $this->html .= $this->page->buildElement("scavengeOptions", $output);

        }

    }

?>