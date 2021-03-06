<?php

    new hook("userInformation", function ($user) {
        global $page;
        $time = $user->getTimer("crime");
        if (($time-time()) > 0) {
            $page->addToTemplate('crime_timer', $time);
        } else {
            $page->addToTemplate('crime_timer', 0);
        }
        $page->addToTemplate('crime_timer', $time);
    });

    new hook("actionMenu", function ($user) {
        if ($user) return array(
            "url" => "?page=crimes", 
            "text" => "Crimes", 
            "timer" => $user->getTimer("crime"),
            "templateTimer" => "crime_timer",
            "sort" => 100
        );
    });
?>