<?php

    class adminModule {

        public function method_settings() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("pointsCashCost", $this->methodData->pointsCashCost);
                $settings->update("pointsBulletsCost", $this->methodData->pointsBulletsCost);
                $settings->update("pointsHealthCost", $this->methodData->pointsHealthCost);

                foreach ($this->methodData as $key => $value) {
                    $data = explode("-", $key);
                    if ($data[0] == "car") {
                        $this->db->update("
                            UPDATE cars SET CA_points = :points WHERE CA_id = :id
                        ", array(
                            ":points" => $value, 
                            ":id" => $data[1]
                        ));
                    }
                }

            }


            $output = array(
                "pointsCashCost" => $settings->loadSetting("pointsCashCost"),
                "pointsBulletsCost" => $settings->loadSetting("pointsBulletsCost"),
                "pointsHealthCost" => $settings->loadSetting("pointsHealthCost")
            );

            $output["cars"] = $this->db->selectAll("
                SELECT 
                    CA_id as 'id', 
                    CA_name as 'name', 
                    CA_points as 'cost'
                FROM cars;
            ");

            $this->html .= $this->page->buildElement("settings", $output);

        }

    }
