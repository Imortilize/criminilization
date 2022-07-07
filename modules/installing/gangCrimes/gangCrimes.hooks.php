<?php

    new hook("userInformation", function ($user) {
        global $page;
        $time = $user->getTimer("gangCrimes");
        if (($time-time()) > 0) {
            $page->addToTemplate('gangCrimes_timer', $time);
        } else {
            $page->addToTemplate('gangCrimes_timer', 0);
        }
    });

    new hook("gangMenu", function ($user) {
        if ($user && $user->info->US_gang) {
            return array(
                "sort" => 55,
                "url" => "?page=gangCrimes", 
                "text" => "Gang Crimes"
            );
        }
    });

    new hook("gangMenu", function ($user) {
        if ($user && $user->info->US_gang) {

            $g = new Gang($user->info->US_gang);

            if (!$g->can("viewIncome")) return false;

            return array(
                "sort" => 56,
                "url" => "?page=gangCrimes&action=income", 
                "text" => "View Crime Income"
            );
        }

    });

    new hook("gangPermission", function ($user) {
        return array(
            "name" => "View Crime Income", 
            "description" => "This gives this gang member the ability to view how much other members are earning from gang crimes", 
            "key" => "viewIncome"
        );
    });

    new hook("joinGang", function ($user) {
        $user->set("US_gangCash", 0);
        $user->set("US_gangBullets", 0);
    });