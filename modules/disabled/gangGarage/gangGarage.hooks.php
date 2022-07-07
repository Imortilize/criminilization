<?php
    new hook("gangMenu", function ($user) {
        if ($user && $user->info->US_gang) {
	        return array(
	        	"sort" => 50,
	            "url" => "?page=gangGarage", 
	            "text" => "Garage"
	        );
        }

    });

    new hook("gangPermission", function ($user) {
        return array(
            "name" => "Take Cars", 
            "description" => "This gives this gang member the ability to take cars from the garage", 
            "key" => "takeCar"
        );
    });