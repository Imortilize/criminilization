<?php

    class loggedin extends module {

        public $allowedMethods = array();

        public function constructModule() {

            $news = $this->db->prepare("
                SELECT * FROM gameNews INNER JOIN users ON (GN_author = U_id) ORDER BY GN_date DESC LIMIT 0, 6
            ");
            $news->execute();
            $articleInfo = array();
            while ($newsArticle = $news->fetch(PDO::FETCH_ASSOC)) {

                $author = new user($newsArticle['GN_author']);

                if ($newsArticle['GN_type'] == "news"){
                    $icon = 'bx:bx-news';
                    $color = "text-secondary";
                }elseif ($newsArticle['GN_type'] == "bug"){
                    $icon = 'akar-icons:bug';
                    $color = "text-danger";
                }elseif ($newsArticle['GN_type'] == "offer"){
                    $icon = 'bx:bxs-offer';
                    $color = "text-primary";
                }elseif ($newsArticle['GN_type'] == "update"){
                    $icon = 'carbon:update-now';
                    $color = "text-success";
                }elseif ($newsArticle['GN_type'] == "feature"){
                    $icon = 'grommet-icons:new';
                    $color = "text-info";
                }else{
                    $icon = 'zondicons:announcement';
                    $color = "text-warning";
                }

                $articleInfo[] = array(
                    "id" => $newsArticle['GN_id'],
                    "title" => $newsArticle['GN_title'],
                    "authorID" => $newsArticle['U_id'],
                    "user" => $author->user,
                    "date" => _ago($newsArticle['GN_date']),
                    "text" => $newsArticle['GN_text'],
                    "icon" => $icon,
                    "color" => $color,
                );

            }

            $this->html .= $this->page->buildElement('newsArticle', array(
                "user" => $this->user->info->U_name,
                "news" => $articleInfo,
            ));

        }

    }

