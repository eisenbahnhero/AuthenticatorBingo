<?php

	//Get current User 
	$current_user = null;
	
	switch($config["auth_mode"]){
		
		case "Windows":
			if(isset($_SERVER['REMOTE_USER'])){
				$current_user = $_SERVER['REMOTE_USER'];
				$current_user = str_replace("\\", "", $current_user);
				$current_user = str_replace("group", "", $current_user);
			}
			break;
			
		case "IP":
			if(isset($_SERVER["REMOTE_ADDR"])){
				$current_user = $_SERVER['REMOTE_ADDR'];
				$current_user = str_replace(".", "_", $current_user);
			}
			break;
	}
	
	if($current_user == null or strlen($current_user) <= 0){
		die("--- NO ACCESS ---");
	}

?>