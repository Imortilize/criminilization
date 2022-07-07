<?php
class adminModule
{
    private function getTutorial($tutorialID = "all")
    {
        if ($tutorialID == "all") {
            $add = "";
        } else {
            $add = " WHERE T_id = :id";
        }

        $tutorials = $this->db->prepare("
            SELECT
                T_id as 'id',
                T_module as 'mod',
                T_text as 'text'
            FROM tutorials" . $add . "
            ORDER BY T_id"
        );

        if ($tutorialID == "all") {
            $tutorials->execute();
            return $tutorials->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $tutorials->bindParam(":id", $tutorialID);
            $tutorials->execute();
            return $tutorials->fetch(PDO::FETCH_ASSOC);
        }
    }

    private function validateTutorial($tutorial) {
        $errors = array();

        if (strlen($tutorial["text"]) < 60) {
            $errors[] = "Tutorial name is to short, this must be atleast 60 characters";
        }

        return $errors;
    }

    public function method_new () {
        $tutorial = array();

        if (isset($this->methodData->submit)) {
            $tutorial = (array) $this->methodData;
            $errors = $this->validatetutorial($tutorial);

            if (count($errors)) {
                foreach ($errors as $error) {
                    $this->html .= $this->page->buildElement("error", array("text" => $error));
                }
            }else {
                $insert = $this->db->prepare("
                    INSERT INTO tutorials (T_module, T_text)  VALUES (:module, :text);
                ");
                $insert->bindParam(":module", $this->methodData->mods);
                $insert->bindParam(":text", $this->methodData->text);
                $insert->execute();


                $this->html .= $this->page->buildElement("success", array("text" => "This tutorial has been created"));
            }
        }

        $tutorial["editType"] = "new";
        $modules = array();
        foreach ($this->page->modules as $key => $mod) {
            $mod['id'] = $key;
            if (isset($mod["requireLogin"]) && $mod["requireLogin"]) {
                if (!isset($mod["moduleCantBeDisabled"])) {
                    $modules[] = array(
                        "id" => $mod['id'],
                        "name" => $mod['name'],
                    );
                }
            }
        }
        $tutorial["modules"] = $modules;
        $this->html .= $this->page->buildElement("tutorialForm", $tutorial);
    }

    public function method_edit () {
        if (!isset($this->methodData->id)) {
            return $this->html = $this->page->buildElement("error", array("text" => "No tutorial ID specified"));
        }

        $tutorial = $this->getTutorial($this->methodData->id);

        if (isset($this->methodData->submit)) {
            $tutorial = (array) $this->methodData;
            $errors = $this->validateTutorial($tutorial);

            if (count($errors)) {
                foreach ($errors as $error) {
                    $this->html .= $this->page->buildElement("error", array("text" => $error));
                }
            } else {
                $update = $this->db->prepare("
                    UPDATE tutorials SET T_module = :module, T_text = :text WHERE T_id = :id
                ");
                $update->bindParam(":module", $this->methodData->mods);
                $update->bindParam(":text", $this->methodData->text);
                $update->bindParam(":id", $this->methodData->id);
                $update->execute();

                $this->html .= $this->page->buildElement("success", array("text" => "This tutorial has been updated"));
            }
        }

        $tutorial["editType"] = "edit";
        $modules = array();
        foreach ($this->page->modules as $key => $mod) {
            $mod['id'] = $key;
            if (isset($mod["requireLogin"]) && $module["requireLogin"]) {
                if (!isset($mod["moduleCantBeDisabled"])) {
                    $mod[] = array(
                        "id" => $mod['id'],
                        "name" => $mod['name'],
                    );
                }
            }
        }
        $tutorial["modules"] = $modules;
        $this->html .= $this->page->buildElement("tutorialForm", $tutorial);
    }

    public function method_delete () {

        if (!isset($this->methodData->id)) {
            return $this->html = $this->page->buildElement("error", array("text" => "No tutorial ID specified"));
        }

        $tutorial = $this->getTutorial($this->methodData->id);

        if (!isset($property["id"])) {
            return $this->html = $this->page->buildElement("error", array("text" => "This tutorial does not exist"));
        }

        if (isset($this->methodData->commit)) {
            $delete = $this->db->prepare("
                DELETE FROM tutorials WHERE T_id = :id;
            ");
            $delete->bindParam(":id", $this->methodData->id);
            $delete->execute();
            header("Location: ?page=admin&module=tutorial");
        }

        $this->html .= $this->page->buildElement("tutorialDelete", $tutorial);
    }

    public function method_view () {
        $this->html .= $this->page->buildElement("tutorialsList", array(
            "tutorial" => $this->getTutorial()
        ));
    }

}
