<?php

/**
* A new home page for your game rather then the default login screen.
*
* @package Home Page
* @author NIF
* @version 1.0.0
*/

	
    
new hook("loginMenu", function () {
    return array(
        "url" => "?page=home", 
        "text" => "Home", 
        "sort" => 50
    );
});

new hook("loginPage", function () {
	return "home";
});	
