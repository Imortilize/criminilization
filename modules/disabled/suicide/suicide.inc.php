<?php
    class suicide extends module {
        
        public $allowedMethods = array(
        	"password" => array( "type" => "POST" )
        );
        

        public function constructModule() {

        	$this->html .= $this->page->buildElement("commitSuicide");
        }
            
        public function method_commit() {

            if (!isset($this->methodData->password)) {
                return $this->error("Please enter your password");
            }

            $pass = $this->user->encrypt($this->user->id . $this->methodData->password);

            if ($pass != $this->user->info->U_password) {
                return $this->error("Invalid password!");
            }

            $this->error("You have commited suicide!", "warning");
            $this->user->set("U_status", 0);
            $this->user->set("US_shotBy", $this->user->id);
            $this->user->updateTimer("killed", time());
            $this->construct = false;
            
        }

        

    }

?>