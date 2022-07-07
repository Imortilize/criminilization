<?php

    new hook("gangMenu", function ($user) {
        if ($user && $user->info->US_gang) {
            return array(
                "sort" => 53,
                "url" => "?page=gangBusiness", 
                "text" => "Businesses"
            );
        }
    });

    new hook("gangPermission", function ($user) {
        return array(
            "name" => "Buy Businesses", 
            "description" => "This gives this gang member the ability to buy businesses for their gang members", 
            "key" => "buyBusinesses"
        );
    });