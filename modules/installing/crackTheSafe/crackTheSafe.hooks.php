<?php
    new hook("locationMenu", function ($user) {
        if ($user) return array(
            "url" => "?page=crackTheSafe", 
            "timer" => $user->getTimer("crackTheSafe"),
            "text" => "Crack The Safe"
        );
    });
?>