<?php
new hook("locationMenu", function ($user) {
    if ($user) return array(
        "url" => "?page=vehicles",
        "sort" => 1001,
        "text" => "Vehicles"
    );
});
