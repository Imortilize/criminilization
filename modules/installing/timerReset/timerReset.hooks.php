<?php

    new Hook("alterModuleTemplate", function ($template) {

        if (
            $template["templateName"] == "timer" && 
            isset($template["items"]["timer"])
        )    {

            $cost = _timer($template["items"]["timer"]);

            if (!$cost) return $template;

            $template["html"] = '
            <div class="timer-reset-container">
                ' . $template["html"] . '
                    <p class="text-center">
                        <a class="btn btn-danger timer-cost-{_timer timer}" href="?page=timerReset&reset={timer}&module={_get "page"}">Reset Timer (' . $cost . ' {_setting "pointsName"})</a>
                    </p>
            </div>
            '; 

        }
        
        return $template;

    });

    function _timer($name) {
        $setting = $name . "TimerCost";
        return _setting($setting);
    }

    function _get($name) {
        return $_GET[$name];
    }