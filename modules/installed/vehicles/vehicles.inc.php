<?php

class vehicles extends module {

    public $allowedMethods = array(
        'id'=>array('type'=>'get'),
    );

    public function constructModule() {

        $vehicles = $this->db->selectAll("SELECT * FROM vehicles order by V_id");
        foreach ($vehicles as $vehicle) {
            $data[] = array(
                "id" => $vehicle['V_id'],
                "name" => $vehicle['V_name'],
                "range" => $vehicle['V_range'],
                "max" => $vehicle['V_max'],
                "fuel" => $vehicle['V_fuel'],
                "cost" => $vehicle['V_price'],
                "units" => $vehicle['V_units'],
                "rank" => $vehicle['V_rank'],
                "own" => $this->user->info->US_vehicle == $vehicle['V_id']
            );
        }

        $this->html .= $this->page->buildElement('vehicles', array(
            "vehicles" => $data
        ));
    }

    public function method_buy () {
        $id = abs(intval($this->methodData->id));
        $vehicle = $this->db->select("SELECT * FROM vehicles where V_id = :id", array(
            ":id" => $id
        ));
        if (!isset($vehicle['V_id'])) {
            return $this->error("This vehicle does not exist");
        }
        if ($this->user->info->US_vehicle == $id) {
            return $this->error("You already own this vehicle");
        }
        if ($this->user->info->US_rank < $vehicle['V_rank']) {
            return $this->error("This vehicle require level ".$vehicle['V_rank']);
        }
        if ($this->user->info->US_money < $vehicle['V_price']) {
            return $this->error('You need '.$this->money($vehicle['V_price'])." to buy ".$vehicle['V_name']);
        }

        $this->user->set("US_vehicle", $vehicle['V_id']);
        $this->user->subtract("US_money", $vehicle['V_price']);
        $this->success("You paid ".$this->money($vehicle['V_price'])." to buy ".$vehicle['V_name']);
    }

}


