<?php

new hook("printPage", function ($p) {
    global $page, $user, $db;
    if ($user){
        $tutorials = $db->select("SELECT * FROM `tutorials` where `T_module` = :module", array(
            ":module" => $page->loadedModule["id"]
        ));
        $game = $page->getPageItem("game");
        if (isset($tutorials['T_id'])) {
            $tutorial = '<div class="panel panel-default">
                <div class="panel-heading" data-toggle="collapse" data-target="#tut_'.$tutorials['T_module'].'" aria-expanded="false">
                    <span>Tutorial</span>
                    <i class="glyphicon glyphicon-arrow-down float-right" style="font-size: 15px;"></i>
                </div>
                <div class="collapse" id="tut_'.$tutorials['T_module'].'">
                    <div class="panel-body">'.$tutorials['T_text'].'</div>
                </div>
            </div>';
            $p->pageHTML = str_replace($game, $tutorial.$game, $p->pageHTML);
        }
    }

});
