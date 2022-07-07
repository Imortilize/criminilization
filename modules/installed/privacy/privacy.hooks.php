<?php

/**
* A page to display your privacy policy including some sample text
*
* @package privacy
* @author NIF
* @version 1.0.0
*/


new hook("loginMenu", function () {
    return array(
        "url" => "?page=privacy", 
        "text" => "Privacy Policy", 
        "sort" => 360
    );
});