<?php

    class adminModule {

        public function method_options() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("russianRoulettePrize", $this->methodData->russianRoulettePrize);
                $settings->update("russianRouletteRank", $this->methodData->russianRouletteRank);
                $this->html .= $this->page->buildElement("success", array(
                    "text" => "Options updated."
                ));
            }

            $output = array(
                "ranks" => $this->db->selectAll("
                    SELECT R_id as 'id', R_name as 'name' 
                    FROM ranks ORDER BY R_exp ASC
                "),
                "russianRoulettePrize" => $settings->loadSetting("russianRoulettePrize"),
                "russianRouletteRank" => $settings->loadSetting("russianRouletteRank")
            );

            $this->html .= $this->page->buildElement("options", $output);

        }

    }