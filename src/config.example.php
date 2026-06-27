<?php
	
	#Title of application
	$config["title"] = "Authenticator Bingo";
	
	#Data and exchange directories
	$config["data_dir"] = "data/";
	$config["exchange_dir"] = "exchange/";
	$config["write_alerts_to_exchange_dir"] = false;

	#Authentication mode
	$config["auth_mode"] = "IP"; # IP | Windows

	#ACL
	$config["use_acl"] = false;
	$config["acl_allowed_players"] = array(
		"127_0_0_1",
		"127_0_0_2",
		"127_0_0_3",
		"127_0_0_4",
		"127_0_0_5"
	);

?>