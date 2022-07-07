<?php

/**
* This module allows people to get married
*
* @package Marriage
* @author Chris Day
* @version 1.0.0
*/


    new hook("userKilled", function ($users) {
        $shooter = $users["shooter"];
        $killed = $users["killed"];

        if ($killed->info->US_married) {
            $marriedTo = new User($killed->info->US_married);
            $marriedTo->newNotification("You have become widowed!");
            $marriedTo->set("US_married", 0);
            $killed->set("US_married", 0);
        }

    });

    new hook("accountMenu", function () {
        return array(
            "url" => "?page=marriage", 
            "text" => "Marriage"
        );
    });


    new Hook("profileStat", function ($user) {
        global $page;
        $s = new Settings();
        if (!$user) return;

        if ($user->info->US_married > 0) {
            $u = new User($user->info->US_married);
            $stat = $page->username($u);
        } else {
            $stat = "Single";
        }

        return array(
            "text" => "Marriage Status",
            "stat" => $stat
        );
    });