<?php

    class timerReset extends module {

        public $allowedMethods = array(
            'reset'=>array('type'=>'get'),
            'module'=>array('type'=>'get')
        );

        public function constructModule() {    

            $settings = new Settings();

            if (isset($this->methodData->reset) && isset($this->methodData->module)) {

                $cost = abs(intval($settings->loadSetting($this->methodData->reset . "TimerCost")));

                if (!$cost) {
                    return $this->html .= $this->page->buildElement("error", array(
                        "text" => "You cant reset this timer"
                    ));
                }

                if ($cost > $this->user->info->US_points) {
                    return $this->html .= $this->page->buildElement("error", array(
                        "text" => "You can't afford to reset this timer"
                    ));
                }

                $this->user->set("US_points", $this->user->info->US_points - $cost);
                $this->user->updateTimer($this->methodData->reset, time()-1);

                $this->page->redirectTo($this->methodData->module);
                
            }
        }

    }

?>