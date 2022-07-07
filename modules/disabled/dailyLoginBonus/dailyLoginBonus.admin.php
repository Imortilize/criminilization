<?php 
	
class adminModule {


private function validateRewards($data) {
    
    $errors = array();

		if(empty($data['days']))
		{
		  $errors[] = "Days must be set.";
		}
		
		if($data["money"] < 0) {
      $errors[] = "Money must be set.";
    } 
			
		if($data['bullets'] < 0 )
		{
		  $errors[] = "Bullets must be set.";		
		}
		
    if($this->methodData->carsID > 0)
    {
  	  if (!$this->getCars($this->methodData->carsID)) 
  	 {
     		$errors[] = "Invalid Car selected.";	
      }	
    }		

    if($data['days'] > 0)
    {
      $add = "";
      if($_GET['action'] == "edit")
      {
        $add = " AND DL_id!=" . $this->methodData->id;
      }
      
      $rewardDayCheck = $this->db->prepare("SELECT DL_id FROM dailyLoginBonuses WHERE DL_days=" . $this->methodData->days . $add);
      $rewardDayCheck->execute ();
    
      if($rewardDayCheck->fetch(PDO::FETCH_ASSOC))
      {
        $errors[] = "Reward day already exists.";
      }
    }
    return $errors;
            
  }

public function method_new () {
  
   $rewards = array();

   if (isset($this->methodData->submit)) {
    
    $rewards = (array) $this->methodData;
   
    $errors = $this->validateRewards($rewards);
 
    if (count($errors))
    {
      foreach ($errors as $error) 
      {
        $this->html .= $this->page->buildElement("error", array("text" => $error));
      }
    } else {
			
  	  $insert = $this->db->prepare("
      INSERT INTO
      dailyLoginBonuses
    		(
   			DL_days, 
   			DL_rewardMoney, 
    		DL_rewardBullets,
    		DL_rewardCarID
 		  	)  
 		  VALUES 
    		(
    		:days, 
    		:rewardMoney, 
    		:rewardBullets,
    		:rewardCarID
  		  )
      ");
        
      $insert->bindParam(":days", $this->methodData->days);
      $insert->bindParam(":rewardMoney", $this->methodData->money);
      $insert->bindParam(":rewardBullets", $this->methodData->bullets);
      
      $insert->bindParam(":rewardCarID", $this->methodData->carsID);
     
      $insert->execute();
		
	    $this->html .= $this->page->buildElement("success", array("text" => "This daily login reward has been created."));

    }

  }

   $rewards["editType"] = "new";
   $rewards['car'] = $this->getCars();
   $this->html .= $this->page->buildElement("dailyLoginBonusForm", $rewards);
}

public function method_edit () {
  
  if (!isset($this->methodData->id))
  {
     return $this->html = $this->page->buildElement("error", array("text" => "No Reward specified"));
  }

   $rewards = $this->getRewards($this->methodData->id);
  
  
 $selectedCar = $rewards['carID'];

   if (isset($this->methodData->submit))
   {
    $selectedCar = $this->methodData->carsID;
	  $rewards = (array) $this->methodData;
    $errors = $this->validateRewards($rewards);
  
    if (count($errors)) {
      foreach ($errors as $error)
      {
        $this->html .= $this->page->buildElement("error", array("text" => $error));
      }
    } else {

      $update = $this->db->prepare("
      UPDATE dailyLoginBonuses 
      SET 
        DL_days =:days,
        DL_rewardMoney=:money,
        DL_rewardBullets=:bullets,
        DL_rewardCarID=:carID
       WHERE
         DL_id=:id");

      $update->bindParam (":id", $this->methodData->id);      
      $update->bindParam(":days", $this->methodData->days);
      $update->bindParam(":money", $this->methodData->money);
      $update->bindParam(":bullets", $this->methodData->bullets);
      $update->bindParam(":carID", $this->methodData->carsID);
      $update->execute();
      
      
      $this->html .= $this->page->buildElement("success", array("text" => "This daily login reward has been updated"));
      
     /* $count = 0;
      
      //easier to delete all previous rewards instead of updating then since these records are only used once per user.
			do	
			{
			  $delete -> $this->db->prepare("DELETE FROM dailyLoginBonuses WHERE DL_id=:id");
			  $delete->bindParam(":id", $this->methodData->id);
			  $delete->execute ();
			  
			  
			  $count++;
  			$insert = $this->db->prepare("
        INSERT INTO
          dailyLoginBonuses
  				(
  				DL_days, 
  				DL_rewardType, 
  				DL_rewardAmoumt
  				)  
  		  VALUES 
  				(:days, 
  				:reward, 
  				:amount
				  );
         ");
        
        $insert->bindParam(":days", $this->methodData->days);
      
        if($count == 1 &&  $this->methodData->money > 0)
        {
          $insert->bindParam(":reward", "money");
          $insert->bindParam(":amount", $this->methodData->money);
           $insert->execute();
        }
        
        if($count == 2 &&  $this->methodData->bullets > 0)
        {
          $insert->bindParam(":reward", "bullets");
          $insert->bindParam(":amount", $this->methodData->money);
           $insert->execute();
        } 
        
        if($count == 3 &&  $this->methodData->carsID > 0)
        {
          $insert->bindParam(":reward", "car");
          $insert->bindParam(":amount", $this->methodData->carsID);
          $insert->execute();
        }               
			} while($count != 3); 
		
      $update->prepare("
      UPDATE dailyLoginBonuses 
      SET 
        DL_rewardMoney=:money,
        DL_rewardBullets=:bullets,
        DL_rewardCarID=:carID
       WHERE
         DL_id=:id");

      $update->bindParam (":id", $this->methodData->id);
      $insert->bindParam(":days", $this->methodData->days);
      $update->bindParam(":rewardMoney", $this->methodData->money);
      $update->bindParam(":rewardBullets", $this->methodData->bullets);
      $update->execute();*/
    
    }
  }
  
  $cars = $this->db->prepare("
  SELECT
    CA_id as 'id',  
    CA_name as 'name'
  FROM 
    cars
  ORDER BY
    CA_name, 
    CA_value"
  );
  $cars->execute ();
  while($car = $cars->fetchObject())
  {
    if($selectedCar == $car->id)
    {
      $carInfo[] = array("name" => $car->name, "id" => $car->id,  "carPicked" => "selected");
    } else {
      $carInfo[] = array("name" => $car->name, "id" => $car->id);
    }
  }  
  
   $rewards['editType'] = "edit";
   $rewards['car'] = $carInfo;
   $this->html .= $this->page->buildElement("dailyLoginBonusForm", $rewards);
}

public function method_delete () {

    if($this->methodData->id == 1)
    {
      return $this->html = $this->page->buildElement("error", array("text" => "Deleting this daily reward can not be undone.."));
    }
    
    if (!isset($this->methodData->id)) {
       return $this->html = $this->page->buildElement("error", array("text" => "No daily reward specified."));
     }

     if (isset($this->methodData->submit)) {
	
       $delete = $this->db->prepare("DELETE FROM dailyLoginBonuses WHERE DL_id = :id");
        
       $delete->bindParam(":id", $this->methodData->id);
       $delete->execute();

       header("Location: ?page=admin&module=dailyLoginBonus");
	
     }
     $reward = $this->getRewards($this->methodData->id);
     
     $this->html .= $this->page->buildElement("dailyLoginDelete",
     array("name" => "Day " . $reward['days'],
     "id" => $this->methodData->id));
  }

public function method_view () {
    
   
    $this->html .= $this->page->buildElement("dailyLoginList", array(
             "dailyLoginBonuses" => $this->getRewards()
      ));

  }

  
  
private function getRewards($rewardID = "all") {
  
  if ($rewardID == "all") {
    $add = "";
  } else {
    $add = " WHERE DL_id = :id";
  }
            
  $rewards = $this->db->prepare("
  SELECT
    DL_id AS 'id',
    DL_days As 'days',
    DL_rewardMoney AS 'money',
    DL_rewardBullets AS 'bullets',
    DL_rewardCarID AS 'carID'
  FROM 
    dailyLoginBonuses
  $add
  ORDER BY
    DL_days
      ASC"
  );

  if ($rewardID == "all") {
    $rewards->execute();
    
    while($reward = $rewards->fetchObject())
    {
      if($reward->carID > 0)
      {
        $carInfo = "some car";
      }
      else {
        $carInfo = "";
      }
      
      $rewardInfo[] = array(
      "id" => $reward->id,
      "days" => $reward->days,
      "reward" =>
      "$" . $reward->money . ", " . 
      $reward->bullets . " bullets, " .
      $carInfo);
    }
    return $rewardInfo;
    
    //return $rewards->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $rewards->bindParam(":id", $rewardID);
    $rewards->execute();
    return $rewards->fetch(PDO::FETCH_ASSOC);
  }
  
}
 
   
  
private function getCars($carsID = "all") {
  
  if ($carsID == "all") {
    $add = "";
  } else {
    $add = " WHERE CA_id = :id";
  }
            
  $cars = $this->db->prepare("
  SELECT
    CA_id as 'id',  
    CA_name as 'name'
  FROM 
    cars
  $add
  ORDER BY
    CA_name, 
    CA_value"
  );

  if ($carsID == "all") {
    $cars->execute(); 
    if (isset($this->methodData->submit))
    {
      while($car = $cars->fetchObject()) {
     if($this->methodData->carsID == $car->id)
     {
        $carInfo[] = array("name" => $car->name, "id" => $car->id,  "carPicked" => "selected");
     } else {
     $carInfo[] = array("name" => $car->name, "id" => $car->id);
     }
    }
      return $carInfo;
   }
   else{
      return $cars->fetchAll(PDO::FETCH_ASSOC);
    }
  } else {
    $cars->bindParam(":id", $carsID);
    $cars->execute();
    return $cars->fetch(PDO::FETCH_ASSOC);
  }
  
 }
 
   
 
		
}