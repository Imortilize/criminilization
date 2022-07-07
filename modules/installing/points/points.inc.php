<?php

    class points extends module {
        
        public $allowedMethods = array();
        
        public $pageName = '';
        
        public function constructModule() {



            $settings = new settings();
            $currencyDecimalSeperator = $settings->loadSetting("currencyDecimalSeperator", true, ".");

            $packages = $this->db->prepare("
                SELECT
                    ST_id as 'id', 
                    ST_desc as 'desc', 
                    ST_tag as 'tag', 
                    ST_points as 'points', 
                    ST_cost / 100 as 'cost'
                FROM store
                ORDER BY ST_cost ASC
            ");

            $packages->execute();

            $packages = $packages->fetchAll(PDO::FETCH_ASSOC);

            foreach ($packages as $key => $value) {
                $value["decimalFormattedCost"] = number_format($value["cost"], 2, $currencyDecimalSeperator, "");
                $value["formattedCost"] = number_format($value["cost"], 2);
                $value["user"] = (array) $this->user->info;
                $value["time"] = time();
                $packages[$key] = $value;
            }

            $this->html .= $this->page->buildElement("packages", array(
                "packages" => $packages
            ));
        }

        public function method_successful () {
            $this->alerts[] = $this->error("Payment received, we are processing the transaction now!", "success");
        }

        public function method_cancelled () {
            $this->alerts[] = $this->error("Transaction cancelled");
        }
        
    }


?>