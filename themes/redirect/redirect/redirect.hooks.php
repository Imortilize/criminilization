<?php

new Hook("moduleLoad", function ($moduleToLoad) {
  global $user;
  
  $minutes = 60;
  $time = time() + (60 * $minutes);
  
  //user is logged in
  if(!empty($user))
  {
    if($moduleToLoad != "login" && $moduleToLoad != "logout" && $moduleToLoad != "register" && $moduleToLoad != "forgotPassword")
    {
      setcookie("module", $moduleToLoad, $time); 
    }
  }
  
  return $moduleToLoad;

});

new hook("userAction", function ($data) {
			
  global $db, $user;
  
  if($data['module'] == "login" && $data["success"] == true)
  {
    
    if(empty($_COOKIE["module"]))
    {
      return;
    }
    
    $lastPage = $_COOKIE["module"];
    if(!empty($lastPage))
    {
      if($lastPage != "login" && $lastPage != "logout" && $lastPage != "register" && $lastPage != "forgotPassword")
      {
        header("Location: ?page=$lastPage");
        exit(); 
      }
    }
  }
});	
  


  
?>