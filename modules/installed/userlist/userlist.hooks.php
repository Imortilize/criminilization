<?php
new hook("locationMenu", function ($user) {
    if ($user) return array(
        "url" => "?page=userlist",
        "sort" => 1002,
        "text" => "City List"
    );
});
