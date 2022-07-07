<?php

    new hook("moneyMenu", function ($user) {
        return array(
            "url" => "?page=stocks", 
            "text" => "Stocks"
        );
    });