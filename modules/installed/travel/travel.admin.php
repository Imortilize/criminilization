<?php

    class adminModule {

        private function getLocations($locationsID = "all") {
            if ($locationsID == "all") {
                $add = "";
            } else {
                $add = " WHERE L_id = :id";
            }

            $sql = "
                SELECT
                    L_id as 'id',
                    L_name as 'name',
                    L_distance as 'distance'
                FROM locations" . $add . "
                ORDER BY L_name";

            if ($locationsID == "all") {
                return $this->db->selectAll($sql);
            } else {
                return $this->db->select($sql, array(
                    ":id" => $locationsID
                ));
            }
        }

        public function upload($id) {
            if (!$_FILES["image"]) {
                return $this->html = $this->page->buildElement("error", array("text" => "No image file specified"));
            }

            if (isset($_FILES["image"])) {
                $new = __DIR__ . "/images/" . $id . ".jpg";
                move_uploaded_file($_FILES["image"]["tmp_name"], $new);
            }
        }

        private function validateLocations($locations) {
            $errors = array();

            if (strlen($locations["name"]) < 3) {
                $errors[] = "Location name is to short, this must be at least 5 characters";
            }

            return $errors;
        }

        public function method_new () {

            $locations = array();

            if (isset($this->methodData->submit)) {
                $locations = (array) $this->methodData;
                $errors = $this->validateLocations($locations);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $distance = $this->methodData->latitude.','.$this->methodData->longitude;

                    $insert = $this->db->prepare("
                        INSERT INTO locations (L_name, L_distance)  VALUES (:name, :distance);
                    ");
                    $insert->bindParam(":name", $this->methodData->name);
                    $insert->bindParam(":distance", $distance);                     
                    $insert->execute();

                    // Upload the image as required
                    $this->upload($this->db->lastInsertId());

                    // Display that it has successfully been updated
                    $this->html .= $this->page->buildElement("success", array("text" => "This location has been created"));
                }
            }

            $locations["editType"] = "new";
            $this->html .= $this->page->buildElement("locationsForm", $locations);
        }

        public function method_edit () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No location ID specified"));
            }

            $locations = $this->getLocations($this->methodData->id);

            if (isset($this->methodData->submit)) {
                $locations = (array) $this->methodData;
                $errors = $this->validateLocations($locations);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $distance = $this->methodData->latitude.','.$this->methodData->longitude;
                    $update = $this->db->prepare("
                        UPDATE locations SET L_name = :name, L_distance = :distance WHERE L_id = :id
                    ");
                    $update->bindParam(":name", $this->methodData->name); 
                    $update->bindParam(":distance", $distance);
                    $update->bindParam(":id", $this->methodData->id);
                    $update->execute();

                    // Upload the image
                    $this->upload($this->methodData->id);

                    // Display the location has been updated
                    $this->html .= $this->page->buildElement("success", array("text" => "This location has been updated"));
                }
            }

            $locations["editType"] = "edit";
            if (isset($locations['distance'])) {
                $locations['distance'] = explode(',', $locations['distance']);
                $locations['latitude'] = $locations['distance'][0];
                $locations['longitude'] = $locations['distance'][1];
            }
            $this->html .= $this->page->buildElement("locationsForm", $locations);
        }

        public function method_delete () {

            if (!isset($this->methodData->id)) {
                return $this->html = $this->page->buildElement("error", array("text" => "No location ID specified"));
            }

            $locations = $this->getLocations($this->methodData->id);

            if (!isset($locations["id"])) {
                return $this->html = $this->page->buildElement("error", array("text" => "This location does not exist"));
            }

            if (isset($this->methodData->commit)) {
                $delete = $this->db->delete("
                    DELETE FROM locations WHERE L_id = :id;
                ", array(
                    ":id" => $this->methodData->id
                ));
                $delete->execute();

                header("Location: ?page=admin&module=locations");

            }

            $this->html .= $this->page->buildElement("locationsDelete", $locations);
        }

        public function method_view () {

            $this->html .= $this->page->buildElement("locationsList", array(
                "locations" => $this->getLocations()
            ));
        }
    }
