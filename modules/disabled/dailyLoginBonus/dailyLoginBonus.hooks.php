<?php

new Hook("userAction", function ($data) {
  global $db, $user;



  if($data['module'] == "login")
  {
    
    $days = 1;
    $today = mktime(1, 1, 1, date('m'), date('d'), date('Y'));
    $today = date("m d Y", $today);
      
    $select = $db->prepare ("SELECT LL_id, LL_lastDay, LL_days FROM dailyLoginLog WHERE LL_userID=:id");
    
    $select->bindParam(":id", $data['user']);
    $select->execute();
    
    $record =  $select->fetch(PDO::FETCH_ASSOC);

    //if exist, we dont want to create another record
    if($record)
    {
      
      $yesterday = mktime(1, 1, 1, date("m"), date('d')-1, date('Y') );
      
      $yesterday = date("m d Y", $yesterday);
      //if last date logged in not today
      if( $record['LL_lastDay'] != $today)
      {
        $days = 1;
        
        //if last day logged in is yesterday
        if($record['LL_lastDay'] == $yesterday)
        {
          
          $days = $record['LL_days'] + 1;
        }
        
        //update record
        $update = $db->prepare("UPDATE dailyLoginLog
          SET 
            LL_lastDay=:today,
            LL_days=:day
          WHERE LL_userID=:id");
          
        $update->bindParam (":day", $days);
        $update->bindParam(":today", $today);
        $update->bindParam(":id", $data['user']);
        $update->execute ();
      }
      
      
    }
    else //no log off user ever logging in
    {
      
      //var_dump($data);
    
      
      //create login record
      $insert = $db->prepare("INSERT INTO dailyLoginLog
      (
      LL_userID,
      LL_lastDay,
      LL_days
      )
      VALUES
      (
      :id,
      :day,
      '1'
      )");
  
     $insert->bindParam(":id", $data['user']);
     $insert->bindParam(":day", $today);
     $insert->execute();
     
     $days = 1;
    }
         
    
   //issue reward ?
   $rec = $db->prepare ("
   SELECT 
     DL_id AS id
   FROM 
     dailyLoginBonuses
   WHERE 
     DL_days=:days
   ");
           
    $rec->bindParam(":days", $days);
    $rec->execute(); 
      
    $bonus = $rec->fetch(PDO::FETCH_ASSOC);
    if($bonus) 
    {
      //check if user received bonus
      $rec = $db->prepare ("
      SELECT 
        DLR_id AS id
      FROM
        dailyLoginRewardLog
      WHERE 
        DLR_userID=:userID
       AND
       DLR_bonusID=:bonusID
      ORDER BY 
        DLR_id
      DESC LIMIT 1
     ");
     
     $rec->bindParam(":userID", $data['user']);
     $rec->bindParam(":bonusID", $bonus['id']);
     $rec->execute ();
     $reward = $rec->fetchObject();
     
    //user didn't get reward for day
     if(!$reward)
     {
      $insert = $db->prepare("
      INSERT INTO
      dailyLoginRewardLog
      (
        DLR_userID,
        DLR_bonusID
      )
      VALUES
      (
        :userID,
        :bonusID
      )
     ");
              
      $insert->bindParam(":userID", $data['user']);
              
      $insert->bindParam(":bonusID", $bonus['id']);
      $insert->execute();
          
     
      $rec = $db->prepare ("
      SELECT 
        DLR_id AS id
      FROM
        dailyLoginRewardLog
      ORDER BY 
        DLR_id
      DESC LIMIT 1
     ");
      $rec->execute ();
      $reward = $rec->fetchObject();
              
      $msg = 'You have just received a login bonus for logging in ' . $days . 'days in a row. <a href="?page=dailyLoginBonus&action=collect&id=' . $reward->id . '">[ Collect your reward ]</a>';
              
      $user = new user($data['user']);
      $user->newNotification($msg);
     }
    }
              
    
  }
  
});

?>