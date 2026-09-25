<?php
	
	#Title of application
	$config["title"] = "Authenticator Bingo";
	
	#Data dir
	$config["data_dir"] = "data/";

	#Send events to exchange dir
	$config["send_events_to_exchange_dir"] = false;
	$config["exchange_dir"] = "exchange/";

	#Send events to a webhook trigger (POST) as json
	$config["send_events_to_webhook"] = false;
	$config["webhook_url"] = "your-webhook-url";

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

	#Viewing page settings (To only allow viewing of the game without registration)
	$config["viewing_enabled"] = false;		# If true, the game can be viewed without registration. If false, only registered players can view the game.
	$config["viewing_token"] = "1234";	# Token for viewing the game. (/view.php?token=1234). A Token is required.

?>