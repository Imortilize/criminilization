<?php

    class adminModule {

        public function method_options() {

            $settings = new settings();

            if (isset($this->methodData->submit)) {
                $settings->update("lotteryMax", $this->methodData->lotteryMax);
                $settings->update("lotteryTax", $this->methodData->lotteryTax);
                $settings->update("lotteryCost", $this->methodData->lotteryCost);
                $settings->update("lotteryTime", $this->methodData->lotteryTime);
                $this->html .= $this->page->buildElement("success", array(
                    "text" => "Options updated."
                ));
            }

            $output = array(
                "lotteryMax" => $settings->loadSetting("lotteryMax", false),
                "lotteryTax" => $settings->loadSetting("lotteryTax", false),
                "lotteryCost" => $settings->loadSetting("lotteryCost", false),
                "lotteryTime" => $settings->loadSetting("lotteryTime", false)
            );

            $this->html .= $this->page->buildElement("options", $output);

        }

    }