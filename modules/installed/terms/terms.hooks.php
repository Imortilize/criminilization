<?php

/**
* A page to display your terms and conditions including some sample text
*
* @package terms
* @author NIF
* @version 1.0.0
*/

    new hook("loginMenu", function () {
        return array(
            "url" => "?page=terms", 
            "text" => "Terms & Conditions", 
            "sort" => 300
        );
    });
