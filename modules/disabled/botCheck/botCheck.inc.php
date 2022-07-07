<?php

    class botCheck extends module {
        
        public $allowedMethods = array(
            "code" => array("type" => "post")
        );

        public function newCaptcha() {
            $_SESSION['_CAPTCHA'] = simple_php_captcha();
        }
        
        public function method_check () {
            $this->construct = false;

            if (!isset($this->methodData->code)) {
                return $this->error("Please enter a code!");
            }
            
            if ($_SESSION["_CAPTCHA"]["code"] != $this->methodData->code) {
                $this->error("Invalid code!");
                return $this->constructModule();
            }

            $this->error("Correct code entered, you may continue.", "success");

            $_SESSION["actions"] = 0;
            $_SESSION["maxActions"] = mt_rand(50, 100);

            header("Location:?page=" . $_SESSION["lastAction"]);
            exit;
        }

        public function constructModule() {
            $this->newCaptcha();
            $this->html .= $this->page->buildElement("botCheck", $_SESSION['_CAPTCHA']);
        }
        
    }

?>