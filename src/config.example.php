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

?>