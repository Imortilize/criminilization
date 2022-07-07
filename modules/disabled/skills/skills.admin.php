<?php 
	
class adminModule {

  private function getSkill($skillID = "all", $byID = true) {
            
   if ($skillID == "all") {
     $add = "";
   } else {
	  if($byID == true){
      $add = " WHERE SK_id = :id";
    }	else {
			$add = " WHERE SK_name = :id";
	  }
   }  

   $skill = $this->db->prepare("
     SELECT
		  SK_id AS 'id',
      SK_name AS 'name',  
			SK_default AS 'defaultValue',
			SK_max AS 'maxValue',
			SK_canUpdate AS 'canUpdate',
			SK_isHidden AS 'isHidden'
     FROM 
      skills
      "  . $add . " 
     ORDER BY 
       SK_name 
      ASC
      ");

    if ($skillID == "all") {
      $skill->execute();
      return $skill->fetchAll(PDO::FETCH_ASSOC);
    } else {
		  $skill->bindParam(":id", $skillID);
			$skill->execute();
      return $skill->fetch(PDO::FETCH_ASSOC);
    }
}

  private function validateSkill($skill) {
    $errors = array();
		$name = str_replace(' ', '', $skill['name']);
			
    if (strlen($skill["name"]) < 1)
    {
      $errors[] = "Skill name is to short, this must be atleast 1 characters";
    }

		if(!ctype_alpha($name))
		{
		  $errors[] = "Skill name must be all characters. No numbers or special characters.";
		}
		
		if(!intval($skill["maxValue"])) {
      $errors[] = "No max level specified";
    } 
			
		if($skill['canUpdate'] != 'y' && $skill['canUpdate'] != 'n' )
		{
		  $errors[] = "Skill upgradable field must be either the letter y or n for yes or no.";		
		}

		if($skill['isHidden'] != 'y' && $skill['isHidden'] != 'n'){
		  $errors[] = "Skill hidden field must be either the letter y or n for yes or no.";		
		}			
			
    return $errors;
            
  }

  public function method_new () {
  
   $skill = array();

   if (isset($this->methodData->submit)) {

    if(strtolower($this->getSafeName($this->methodData->name)) == "skillpointsavailable")
    {
       return $this->html = $this->page->buildElement("error", array("text" => "This stat name is reserved  ."));
    }	
    
    $skill = (array) $this->methodData;
   
    $errors = $this->validateSkill($skill);

		if ($this->getSkill($this->methodData->name, false)) 
		{
   		$errors[] = "Skill/Stat name already exists";	
    }			

    if (count($errors))
    {
      foreach ($errors as $error) 
      {
        $this->html .= $this->page->buildElement("error", array("text" => $error));
      }
    } else {
		            
		  $safeName = $this->getSafeName($skill['name']);  
					
			$insert = $this->db->prepare("
      INSERT INTO
        skills
				(
				SK_name, 
				SK_default, 
				SK_max, 
				SK_canUpdate,
				SK_isHidden
				)  
		  VALUES 
				(:name, 
				:defaultValue, 
				:maxValue, 
				:canUpdate,
				:isHidden
				);
       ");
        
      $insert->bindParam(":name", $this->methodData->name);
      $insert->bindParam(":maxValue", $this->methodData->maxValue);
      $insert->bindParam(":canUpdate", $this->methodData->canUpdate);
      $insert->bindParam(":isHidden", $this->methodData->isHidden);
      $insert->bindParam(":defaultValue", $this->methodData->defaultValue);
      $insert->execute();

		  $sett = $this->getSkillSettings($skill);

			$insert = $this->db->prepare("
			ALTER TABLE
			  `userStats` 
			ADD COLUMN 
				`US_" . strtolower($safeName) . "`  INT(11) NOT NULL DEFAULT  {$this->methodData->defaultValue}"
					);
      $insert->execute();

   	   $insert = $this->db->prepare
   	   ("
   	   ALTER TABLE
   	     `userStats` 
   	    ADD COLUMN 
					`US_max$safeName`  FLOAT(10,1) NOT NULL DEFAULT  $sett"
				);
						
        $insert->execute();

	      $this->html .= $this->page->buildElement("success", array("text" => "This stat or skill has been created."));

    }

   }

   $skill["editType"] = "new";
   $this->html .= $this->page->buildElement("skillForm", $skill);
}

public function method_edit () {

  if (!isset($this->methodData->id))
  {
     return $this->html = $this->page->buildElement("error", array("text" => "No Skill specified"));
  }

  $skill = $this->getSkill($this->methodData->id);

   if (isset($this->methodData->submit)) 
   {
     
    if($this->methodData->id == 1)
    {
     if(strtolower($this->getSafeName($this->methodData->name)) != "skillpointsavailable")
       {
        return $this->html = $this->page->buildElement("error", array("text" => "Editing this stat name will break this mod. It is hardcoded to allower players to update other stats and skills."));
      }
     }       


	    $oldSafeName = $this->getSafeName($skill['name']);
                
	  $skill = (array) $this->methodData;
     $errors = $this->validateSkill($skill);

    if (count($errors)) {
      foreach ($errors as $error)
      {
        $this->html .= $this->page->buildElement("error", array("text" => $error));
      }
    } else {
	
				
			$sett = $this->getSkillSettings($skill);
			$newSafeName = $this->getSafeName($skill['name']);
					
		  $edit = $this->db->prepare("
		  ALTER TABLE 
		    `userStats`
			CHANGE 
			  `US_" . strtolower($oldSafeName) . "`  `US_" . strtolower($newSafeName) . "` INT(11) NOT NULL DEFAULT  {$this->methodData->defaultValue},
			CHANGE
			`US_max$oldSafeName` `US_max$newSafeName` FLOAT(10,1) NOT NULL DEFAULT  $sett
			");	
				
			$edit->execute();
					
			$update = $this->db->prepare("
       UPDATE 
         skills 
       SET 
         SK_name = :name,
         SK_default = :defaultValue,
         SK_max = :maxValue,
         SK_canUpdate = :canUpdate, 
         SK_isHidden = :isHidden
       WHERE SK_id = :id
       ");

      $update->bindParam(":name", $this->methodData->name);
      $update->bindParam(":maxValue", $this->methodData->maxValue);
      $update->bindParam(":canUpdate", $this->methodData->canUpdate);
      $update->bindParam(":isHidden", $this->methodData->isHidden);
      $update->bindParam(":defaultValue", $this->methodData->defaultValue);
      $update->bindParam(":id",  $this->methodData->id);
      $update->execute();

      $this->html .= $this->page->buildElement("success", array("text" => "This skill has been updated"));

    
    }
   }

   $skill['editType'] = "edit";
   $skill['isHidden' . $skill['isHidden']] = true;
    $skill['canUpdate' . $skill['canUpdate']] = true;
    
   $this->html .= $this->page->buildElement("skillForm", $skill);
}

  public function method_delete () {

    if($this->methodData->id == 1)
    {
      return $this->html = $this->page->buildElement("error", array("text" => "Deleting this stat will break this mod. It is hardcoded to allower players to update other stats and skills."));
    }
    
    if (!isset($this->methodData->id)) {
       return $this->html = $this->page->buildElement("error", array("text" => "No stat or skill specified"));
            }

      $skill = $this->getSkill($this->methodData->id);

      if (!isset($skill["id"])) {
        return $this->html = $this->page->buildElement("error", array("text" => "This stat or skill does not exist"));
      }

      if (isset($this->methodData->commit)) {
	
        $delete = $this->db->prepare("DELETE FROM skills WHERE SK_id = :id;");
        $delete->bindParam(":id", $this->methodData->id);
        $delete->execute();

				$safeName = $this->getSafeName($skill['name']);
				$delete = $this->db->prepare
				("
				ALTER TABLE 
				  `userStats`
				DROP
				  `US_$safeName`, 
				DROP 
				  `US_max$safeName`
				");
				$delete->execute();
        
        header("Location: ?page=admin&module=skills");
	
      }


    $this->html .= $this->page->buildElement("skillDelete", $skill);
  }

  public function method_view () {
            
    $this->html .= $this->page->buildElement("skillsList", array(
             "skill" => $this->getSkill()
      ));

  }

	public function getSafeName($name){
			
		$str = explode(" ", $name);
		return implode('', array_map('ucfirst', $str));
	}
		
	public function getSkillSettings($skill){
	  $sett = 0.0 + $skill['maxValue'];
			
		if($skill['canUpdate'] == 'y'){
			$sett += 0.1;
		}
			
		if($skill['isHidden'] == 'y'){
		  $sett += 0.2;
		}
		
		return $sett;
		
	}

}