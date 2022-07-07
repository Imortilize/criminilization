<?php

    class adminModule {

        public function method_settings() {

            $settings = new settings();

            $timers = $this->db->prepare("
                SELECT UT_desc FROM userTimers GROUP BY UT_desc
            ");
            $timers->execute();
            $timers = $timers->fetchAll(PDO::FETCH_ASSOC);


            if (isset($this->methodData->submit)) {
                foreach ($timers as $t) {
                    $timer = $t["UT_desc"];
                    if (isset($this->methodData->$timer)) {
                        $settings->update($timer . "TimerCost", $this->methodData->$timer);
                    }
                } 
                $this->page->alert("Timers updated!", "success");
            }

            $output = array();

            foreach ($timers as $t) {
                $timer = $t["UT_desc"];

                if (in_array($timer, array(
                    "forumMute", "laston", "signup", "killed"
                ))) continue;

                $output[] = array(
                    "name" => $timer,
                    "value" => $settings->loadSetting($timer . "TimerCost")
                );
            }

            $this->html .= $this->page->buildElement("settings", array(
                "timers" => $output
            ));

        }

    }
