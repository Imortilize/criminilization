<?php

class adminModule {

    private function getVehicles($vehicleID = "all") {
        if ($vehicleID == "all") {
            $add = "";
        } else {
            $add = " WHERE V_id = :id";
        }

        $sql = "
            SELECT
                V_id as 'id',
                V_name as 'name',
                V_price as 'cost',
                V_range as 'range',
                V_fuel as 'fuel',
                V_units as 'units',
                V_rank as 'rank',
                V_max as 'max'
            FROM vehicles" . $add . "
            ORDER BY V_id";

        if ($vehicleID == "all") {
            return $this->db->selectAll($sql);
        } else {
            return $this->db->select($sql, array(
                ":id" => $vehicleID
            ));
        }
    }

    private function validateVehicles($vehicles) {
        $errors = array();

        if (strlen($vehicles["name"]) < 3) {
            $errors[] = "Vehicle name is to short, this must be at least 3 characters";
        }


        if (!intval($vehicles["cost"])) {
            $errors[] = "No cost specified";
        }

        return $errors;

    }

    public function method_new () {

        $vehicles = array();

        if (isset($this->methodData->submit)) {
            $vehicles = (array) $this->methodData;
            $errors = $this->validateVehicles($vehicles);

            if (count($errors)) {
                foreach ($errors as $error) {
                    $this->html .= $this->page->buildElement("error", array("text" => $error));
                }
            } else {
                $this->db->insert("
                    INSERT INTO vehicles (V_name, V_range, V_fuel, V_price, V_units, V_rank, V_max)  VALUES (:name, :range, :fuel, :cost, :units, :rank, :max);
                ", array(
                    ":name" => $this->methodData->name,
                    ":range" => $this->methodData->range,
                    ":fuel" => $this->methodData->fuel,
                    ":cost" => $this->methodData->cost,
                    ":units" => $this->methodData->units,
                    ":max" => $this->methodData->max,
                    ":rank" => $this->methodData->rank
                ));

                $this->html .= $this->page->buildElement("success", array("text" => "This vehicle has been created"));

            }

        }

        $vehicles["editType"] = "new";
        $this->html .= $this->page->buildElement("vehiclesForm", $vehicles);
    }

    public function method_edit () {

        if (!isset($this->methodData->id)) {
            return $this->html = $this->page->buildElement("error", array("text" => "No vehicle ID specified"));
        }

        $vehicles = $this->getVehicles($this->methodData->id);

        if (isset($this->methodData->submit)) {
            $vehicles = (array) $this->methodData;
            $errors = $this->validateVehicles($vehicles);

            if (count($errors)) {
                foreach ($errors as $error) {
                    $this->html .= $this->page->buildElement("error", array("text" => $error));
                }
            } else {
                $this->db->update("
                    UPDATE vehicles SET V_name= :name, V_range = :range, V_fuel = :fuel, V_price = :cost, V_units = :units, V_rank = :rank, V_max = :max WHERE V_id = :id
                ", array(
                    ":name" => $this->methodData->name,
                    ":range" => $this->methodData->range,
                    ":fuel" => $this->methodData->fuel,
                    ":cost" => $this->methodData->cost,
                    ":units" => $this->methodData->units,
                    ":rank" => $this->methodData->rank,
                    ":max" => $this->methodData->max,
                    ":id" => $this->methodData->id
                ));

                $this->html .= $this->page->buildElement("success", array("text" => "This vehicle has been updated"));

            }

        }

        $vehicles["editType"] = "edit";
        $this->html .= $this->page->buildElement("vehiclesForm", $vehicles);
    }

    public function method_delete () {

        if (!isset($this->methodData->id)) {
            return $this->html = $this->page->buildElement("error", array("text" => "No vehicle ID specified"));
        }

        $locations = $this->getVehicles($this->methodData->id);

        if (!isset($locations["id"])) {
            return $this->html = $this->page->buildElement("error", array("text" => "This vehicle does not exist"));
        }

        if (isset($this->methodData->commit)) {
            $this->db->delete("
                DELETE FROM vehicles WHERE V_id = :id;
            ", array(
                ":id" => $this->methodData->id
            ));

            header("Location: ?page=admin&module=vehicles");

        }


        $this->html .= $this->page->buildElement("locationsDelete", $locations);
    }

    public function method_view () {

        $this->html .= $this->page->buildElement("vehiclesList", array(
            "vehicles" => $this->getVehicles()
        ));

    }

}
