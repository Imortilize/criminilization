<?php
class adminModule {

    public function method_view () {

        $logs = $this->db->selectAll("SELECT * FROM logs");
        $logsList = array();
        foreach ($logs as $log) {
            $user = new user($log['L_user']);
            $logsList[] = array(
                "id" => $log['L_id'],
                "user" => $user->user,
                "action" => $log['L_text'],
                "module" => $log['L_module'],
                "date" => date("jS M H:i", $log["L_date"]),
            );
        }

        $this->html .= $this->page->buildElement("main", array(
            "log" => $logsList
        ));
    }

}
