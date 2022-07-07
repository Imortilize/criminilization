<?php

    class adminModule {
		
        private function getAward($id = null) {
			$settings = new settings();
            $awards = $this->db->prepare("
                SELECT
                    AW_id as 'id',  
					AW_img as 'img',
                    AW_name as 'name',  
					AW_desc as 'desc',
					AW_type as 'type',
					AW_required as 'required',
                    AW_money as 'rmoney',
					AW_bullets as 'rbullets',
					AW_points as 'rpoints',
					AW_hidden as 'hidden'
                FROM awards
				" . ($id ? "
				WHERE AW_id = :id" : "") . "
                ORDER BY AW_name"
            );
			if ($id) {
				$awards->bindParam(':id', $id);
			}
			$awards->execute();
			if (!$id) {
				return $awards->fetchAll(PDO::FETCH_ASSOC);
			}
			return $awards->fetch(PDO::FETCH_ASSOC);
        }
	
        private function validateAward($award) {
            $errors = array();

            if (strlen($award["name"]) < 4) {
                $errors[] = "Award name is too short, this must be at least 4 characters";
            }
			
            return $errors;
            
        }

        public function method_newAward() {

            $awards = array();

            if (isset($this->methodData->submit)) {
                $awards = (array) $this->methodData;
                $errors = $this->validateAward($awards);
                
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array("text" => $error));
                    }
                } else {
                    $insert = $this->db->prepare("
                        INSERT INTO awards 
						(AW_name, AW_desc, AW_type, AW_required, AW_money, AW_bullets, AW_points, AW_hidden)  
						VALUES (:name, :desc, :type, :required, :rmoney, :rbullets, :rpoints, :hidden);
                    ");
                    $insert->bindParam(":name", $this->methodData->name);
					$insert->bindParam(":desc", $this->methodData->desc);
					$insert->bindParam(":type", $this->methodData->type);
					$insert->bindParam(":required", $this->methodData->required);
					$insert->bindParam(":rmoney", $this->methodData->rmoney);
					$insert->bindParam(":rbullets", $this->methodData->rbullets);
					$insert->bindParam(":rpoints", $this->methodData->rpoints);
					$insert->bindParam(":hidden", $this->methodData->hidden);    
                    $insert->execute();

                    $this->html .= $this->page->buildElement("success", array(
                        "text" => "This award has been created"
                    ));

                }

            }
            $awards["editType"] = "new";
            $this->html .= $this->page->buildElement("awardForm", $awards);
        }

        public function method_editAward() {
			
			if (!isset($this->methodData->id)) {
				$this->page->redirectTo("admin", array("module"=>"awards"));
				return;
			}	

            $awards = $this->getAward($this->methodData->id);

			if (!isset($awards["id"])) {
				$this->page->redirectTo("admin", array("module"=>"awards","action"=>"viewAward"));
				return;
            }
			
            if (isset($this->methodData->submit)) {
                $awards = (array) $this->methodData;
                $errors = $this->validateAward($awards);

                if (count($errors)) {
                    foreach ($errors as $error) {
                        $this->html .= $this->page->buildElement("error", array(
                            "text" => $error
                        ));
                    }
                } else {
                    $update = $this->db->prepare("
                        UPDATE awards SET  
							AW_name = :name,  
							AW_desc = :desc,
							AW_type = :type,
							AW_required = :required,
							AW_money = :rmoney,
							AW_bullets = :rbullets,
							AW_points = :rpoints,
							AW_hidden = :hidden
						WHERE AW_id = :id
                    ");
					$update->bindParam(":name", $this->methodData->name);
					$update->bindParam(":desc", $this->methodData->desc);
					$update->bindParam(":type", $this->methodData->type);
					$update->bindParam(":required", $this->methodData->required);
					$update->bindParam(":rmoney", $this->methodData->rmoney);
					$update->bindParam(":rbullets", $this->methodData->rbullets);
					$update->bindParam(":rpoints", $this->methodData->rpoints);
					$update->bindParam(":hidden", $this->methodData->hidden); 
                    $update->bindParam(":id", $this->methodData->id);
					$update->execute();

                    $this->html .= $this->page->buildElement("success", array(
                        "text" => "This award has been updated"
                    ));
                }
            }
			
			if (isset($this->methodData->upload)) {
				if (isset($_FILES['awardImg']) && $_FILES['awardImg']['error'] === UPLOAD_ERR_OK) {
					$fileTmpPath = $_FILES['awardImg']['tmp_name'];
					$fileName = $_FILES['awardImg']['name'];
					$fileSize = $_FILES['awardImg']['size'];
					$fileType = $_FILES['awardImg']['type'];
					$fileNameCmps = explode(".", $fileName);
					$fileExtension = strtolower(end($fileNameCmps));
					$newFileName = $awards["id"] . '.' . $fileExtension;
					$allowedfileExtensions = array('jpg', 'gif', 'png');
					if (in_array($fileExtension, $allowedfileExtensions)) {
						$uploadFileDir = 'modules/installed/awards/images/';
						$dest_path = $uploadFileDir . $newFileName;
						if(move_uploaded_file($fileTmpPath, $dest_path)){
							$update = $this->db->prepare("
								UPDATE awards SET  
									AW_img = :image  
								WHERE AW_id = :id
							");
							$update->bindParam(":image", $newFileName);
							$update->bindParam(":id", $awards["id"]);
							$update->execute();
							$this->html .= $this->page->buildElement("success", array(
								"text" => "This award image has been uploaded"
							));
						} else {
							$this->page->buildElement("error", array(
								"text" => "There was an error uploading the award image. Please make sure the upload directory is writable by the web server."
							));
						}
					}
				}
            }
            $awards["editType"] = "edit";
            $this->html .= $this->page->buildElement("awardForm", $awards);
        }

        public function method_deleteAward() {

			$award = $this->getAward($this->methodData->id);
			if (!isset($this->methodData->id, $award["id"])) {
				$this->page->redirectTo("admin", array("module"=>"awards","action"=>"viewAward"));
				return;
			}
            if (isset($this->methodData->commit)) {
				$delete = $this->db->prepare("
					DELETE aw,ua FROM awards aw
					LEFT JOIN userAwards ua ON ua.UA_award = aw.AW_id
					WHERE AW_id = :id
                ");
                $delete->bindParam(":id", $this->methodData->id);
                $delete->execute();
                $this->page->redirectTo("admin", array("module"=>"awards","action"=>"viewAward"));
            }
            $this->html .= $this->page->buildElement("awardDelete", $award);
        }
		
        public function method_viewAward() {
			
			$settings = new settings();
		   
            $this->html .= $this->page->buildElement("awardList", array(
                "awards" => $this->getAward()
            ));

        }
    }