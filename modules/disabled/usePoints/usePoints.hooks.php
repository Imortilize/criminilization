<?php

    new hook("pointsMenu", function () {
        $pointsName = _setting("pointsName");
        return array(
            "url" => "?page=usePoints", 
            "text" => $pointsName . ' Shop'
        );
    });

    new Hook("itemMetaData", function () {
        global $db;
        return array(
            "id" => "pointShopCost", 
            "sort" => 4,
            "width" => 4,
            "label" => "Cost to buy in point shop", 
            "type" => "number",
            "validate" => function ($value) { return true; }
        );
    });