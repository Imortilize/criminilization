<?php

    new hook("userInformation", function ($user) {
        global $page;
        $time = $user->getTimer("oc");
        if (($time-time()) > 0) {
            $page->addToTemplate('oc_timer', $time);
        } else {
            $page->addToTemplate('oc_timer', 0);
        }
    });

    new hook("actionMenu", function () {
        return array(
            "url" => "?page=organizedCrime", 
            "text" => "Organized Crime", 
            "sort" => 200
        );
    });
?>
