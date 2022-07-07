<?php
class userlist extends module {

    public function constructModule() {
        $perPage = 20;

        $maxPages = ceil($this->db->select("
                SELECT COUNT(*) as 'count' FROM userStats WHERE US_location = :location
            ", array(
                ":location" => $this->user->info->US_location
            ))["count"] / $perPage);

        $page = 1;
        if (isset($this->methodData->p)) $page = abs(intval($this->methodData->p));
        $from = ($page - 1) * $perPage;

        $round = new Round();

        $pages = array();

        $i = 0;
        while ($i < $maxPages) {
            $p = $i + 1;
            $pages[] = array(
                "page" => $p,
                "active" => $p == $page
            );
            $i++;
        }

        $select = $this->db->selectAll("
                SELECT
                    *
                FROM
                    userStats
                    INNER JOIN users ON (US_id = U_id)
                WHERE
                    U_userLevel = 1 AND
                    U_status != 0 AND
                    U_round = :round AND
                    US_location = :location
                ORDER BY US_exp DESC
                LIMIT " . $from . ", " . $perPage . ";
            ", array(
            ":round" => $round->id,
            ":location" => $this->user->info->US_location
        ));
        $users = array();
        $i = 1;
        foreach ($select as $row) {
            $u = new User($row['U_id']);
            $users[] = array(
                "number" => $i,
                "user" => $u->user,
                "id" => $row['U_id'],
                "rank" => $u->info->US_rank,
                "active" => $u->getTimer('laston'),
                "family" => $u->getGang(),
            );
            $i++;
        }

        $this->html .= $this->page->buildElement('userlist', array(
            "user" => $users,
            "location" => $this->getLocation($this->user->info->US_location),
            "pages" => $pages
        ));
    }

}
