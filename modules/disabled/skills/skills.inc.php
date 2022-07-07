<?php
	
	class skills extends module {
        
        
	public $pageName = 'Skills';
  public $allowedMethods = array('txtData'=>array('type'=>'post'),
 'hiddenName' =>array('type'=>'post')		);
	
	public function constructModule() {
            
            
     $skills = $this->db->prepare("
      SELECT
			 SK_id AS 'id',
       SK_name AS 'name',  
	  	 SK_default AS 'defaultValue',
			 SK_max AS 'maxValue',
			 SK_canUpdate AS 'canUpdate',
			 SK_isHidden AS 'isHidden'
      FROM 
       skills 
			WHERE 
			 SK_canUpdate='y'
      ORDER BY 
       SK_name 
      ASC"
      );

			$skills->execute();
      $skillInfo = array();
			$count = 0;
			
    while($skill = $skills->fetchObject()) {
       $count++;

      switch($count){
				case 1:
					$skillColor = "text-success";
					break;
				case 2:
					$skillColor = "text-warning";
					break;
				case 3:
					$skillColor = "text-info";
					break;
				case 4:
					$skillColor = "text-danger";
					break;
			}
				
	  $safeName = $this->getSafeName($skill->name);

	  if($skill->name != "Skill Points Available") {
      $skillInfo[] = array(
       "name" => $skill->name,
       "skillColor" => $skillColor,
			  "skillValue" => floor($this->user->info->{"US_" . strtolower($safeName)}),
				"safeName" => $safeName
			);
		}
		
		if($count == 4) $count = 0;
		
	 }
	    
	 $pointsLeft  = $this->user->info->US_SkillPointsAvailable; 
	
   $this->html .=  $this->page->buildElement("skillHolder",
     array(
     "pointsLeft" => $pointsLeft,
		  "skills" => $skillInfo)
	   );
	
	}
	
	public function method_update() {	
		
	$skills = $this->db->prepare("
   SELECT
		SK_id AS 'id',
    SK_name AS 'name',  
	  SK_default AS 'defaultValue',
		SK_max AS 'maxValue',
		SK_canUpdate AS 'canUpdate',
		SK_isHidden AS 'isHidden'
   FROM 
     skills 
	 WHERE 
	   SK_canUpdate='y'
   ORDER BY 
     SK_name
   ASC"
  );

	$skills->execute();

  $skillinfo = array();
	$count = 0;
  $counter = 0;
	$allowedPoints = $this->user->info->US_SkillPointsAvailable;
	
  while($skill = $skills->fetchObject()) {	
	
	  $safeName = $this->getSafeName($skill->name);				
		$updates = array();
					
		if($skill->name != "Skill Points Available") {
		  
		  if(empty($this->methodData->txtData[$counter]))
		  {
		    $num = 0;
		  } 
		  else {
		   $num = abs(intval($this->methodData->txtData[$counter]));
		  }
		  
		  if($num < 0)
			{
			  return $this->error("You cant enter a number below 0.");
			}
			
			$count += $num;
			$counter++;

      if(isset($updateQuery))
      {
			$updateQuery .= "US_" .strtolower($safeName) ." = US_" . strtolower($safeName) . " + $num, ";
      }
      else{
        $updateQuery = "US_" .strtolower($safeName) ." = US_" . strtolower($safeName) . " + $num, ";
      }
      
      
		}
		
	 }
//debug($updateQuery);
	  if($count <= $allowedPoints) {
	    
		  if($count == 0) return $this->error("You can't update your stat with 0");
			    
		$updateQuery .= "US_SkillPointsAvailable = US_SkillPointsAvailable - $count";
		$update = $this->db->prepare("UPDATE `userStats` SET $updateQuery WHERE US_id={$this->user->info->US_id}");
				$update->execute();
				
	  header("Location: ?page=skills");
	
		$txt =  array("text" => "Your skill stats have been updated");
	
		$this->alerts[] =$this->page->buildElement('success', $txt);
	  }
		else
		{
      return $this->error("You dont have that many points to distribute.");
		}

	}
		
			
	private function getSafeName($name){
			
		$str = explode(" ", $name);
		return implode('', array_map('ucfirst', $str));
	}
}
?>