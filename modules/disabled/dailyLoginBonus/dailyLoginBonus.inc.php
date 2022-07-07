<?php

class dailyLoginBonus extends module
{
  
   public $allowedMethods = array(
      "collect" => array( "type" => "GET"),
      "id" => array( "type" => "GET" )
   );
        
    public $pageName = 'Daily Login Rewards';
  
  
	public function method_collect(){
		
  $reward = $this->db->prepare
  ("
  SELECT
    DLR_collected AS collected,
    DL_rewardMoney AS money,
    DL_rewardBullets AS bullets,
    DL_rewardCarID AS carID
  FROM 
    dailyLoginRewardLog 
  LEFT OUTER JOIN 
    dailyLoginBonuses ON (DL_id = DLR_bonusID) 
  WHERE 
    DLR_id=:id AND 
    DLR_userID=:userID
    ");
   
  $reward->bindParam(":id", $this->methodData->id);
  $reward->bindParam(":userID", $this->user->id);
  $reward->execute();
  
  $rec = $reward->fetchObject();

  if(empty($rec))
  {
    $msgType = "error";
    $msg = 'The reward you are looking for can not be found.';
  }
  else
  {
    if($rec->collected == 'y')
    {
       $msgType = "info";
       $msg = 'The reward you are looking for has already been collected.';
    }
    else
    {
      $msgType = "success";
      $msg = "You have just collected:";
       if($rec->money != 0)
       {
         $this->user->set("US_money", $this->user->info->US_money + $rec->money);
         $msg .= "<br>Money: " . $rec->money;
       }
       
       if($rec->bullets != 0)
       {
         $this->user->set("US_bullets", $this->user->info->US_bullets + $rec->bullets);
         $msg .= "<br>Bullets: " . $rec->bullets;
       }
       
       if($rec->carID != 0)
       {
         
         $carDamage = mt_rand(1, 50); 
         $insert = $this->db->prepare("
         INSERT INTO garage 
         (
           GA_uid, 
           GA_car, 
           GA_damage, 
           GA_location
         ) 
         VALUES
         (
           :uid,
           :car,
           :damage,
           :loc
         )
         ");
        $insert->bindParam(':uid', $this->user->info->US_id);
        $insert->bindParam(':loc', $this->user->info->US_location);
        $insert->bindParam(':car', $rec->carID);
        $insert->bindParam(':damage', $carDamage);
        $insert->execute();
        
        $msg .= "<br>Anew car.";
       }
       
       $msg .= "<br><br><br>Check you inventory.";
       
       $update = $this->db->prepare("UPDATE dailyLoginRewardLog SET DLR_collected='y' WHERE DLR_id=:id");
       $update->bindParam(":id", $this->methodData->id);
       $update->execute();
       
    }
  }
    
   $this->html .= $this->page->buildElement($msgType, array("text" => $msg));
}
     
	
 public function constructModule()
    {
      if($this->methodData->action != "collect")
      {
        $msgType = "error";
        $msg = "You trying to cheat, ay?";
        
        $this->html .= $this->page->buildElement($msgType, array("text" => $msg));
    
      }
    }
}

?>