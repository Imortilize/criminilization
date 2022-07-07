<?php
new hook("accountMenu", function () {
    global $page;
    $page->registerTemplateFile('https://code.iconify.design/2/2.2.1/iconify.min.js');
    return array(
        "url" => "?page=loggedin",
        "text" => "Game News",
        "sort" => 0
    );
});
