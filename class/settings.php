<?php

    /*
        THIS IS A CORE ENGINE FILE IT IS STRONGLY RECOMMENDED THAT YOU DO NOT EDIT THIS FILE!
     */

    if (!class_exists("settings")) {

        /**
         * Generate an MD5 hash string from the contents of a directory.
         *
         * @param string $directory
         * @return boolean|string
         */
        function hashDirectory($directory)
        {
            if (! is_dir($directory))
            {
                return false;
            }
         
            $files = array();
            $dir = dir($directory);
         
            while (false !== ($file = $dir->read()))
            {
                if ($file != '.' and $file != '..')
                {
                    if (is_dir($directory . '/' . $file))
                    {
                        $files[] = hashDirectory($directory . '/' . $file);
                    }
                    else
                    {
                        $files[] = md5_file($directory . '/' . $file);
                    }
                }
            }
         
            $dir->close();
         
            return md5(implode('', $files));
        }
        
        function _setting($setting) {
            $settings = new settings();
            return $settings->loadSetting($setting, false);
        }

        class settings {

            private $page, $user, $db, $debug = false;
            
            public function __construct() {
                
                global $page, $user, $db;
                
                $this->db = $db;

                if ($user) {
                    $this->user = $user;
                }
                
                if ($page) {
                    $this->page = $page;
                }
                
            }
            
            public function loadSetting($setting, $makeIfNotExist = true, $value = '') {
            
                $select = $this->db->prepare("SELECT * FROM settings WHERE S_desc = :desc");
                $select->bindParam(":desc", $setting);
                $select->execute();
                $row = $select->fetch(PDO::FETCH_ASSOC);
                
                if (empty($row['S_desc'])) {
                    if ($makeIfNotExist) {
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }
                        $this->makeSetting($setting, $value);
                        return $value;
                    } else {
                        return false;
                    }
                } else {
                    $value = @json_decode($row['S_value'], true);
                    if (is_array($value)) {
                        return $value;
                    } else {
                        return $row['S_value'];
                    }
                }
            }
            
            public function makeSetting ($setting, $value) {
            
                if(!$this->loadSetting($setting, false)) {
                    
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }

                    $delete = $this->db->prepare("DELETE FROM settings WHERE S_desc = :desc");
                    $delete->bindParam(":desc", $setting);
                    $delete->execute();
                    
                    $insert = $this->db->prepare("INSERT INTO settings (S_desc, S_value) VALUES (:desc, :value)");
                    $insert->bindParam(":desc", $setting);
                    $insert->bindParam(":value", $value);
                    $insert->execute();
                }
                
            }
            
            public function update($setting, $value) {
                    
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                
                $this->loadSetting($setting);
                
                $update = $this->db->prepare("UPDATE settings SET S_value = :value WHERE S_desc = :desc");
                $update->bindParam(":value", $value);
                $update->bindParam(":desc", $setting);
                $update->execute();
            
            }
            
        }
    }

?>