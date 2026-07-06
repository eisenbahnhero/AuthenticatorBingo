<?php

    $all_user_events = array();

    function add_event(string $type, array $data){
        global $all_user_events;
        $data["type"] = $type;
        $data["timestamp"] = time();
        $all_user_events[] = $data;
    }

    function push_events_to_exchange_dir() {
        global $all_user_events, $config;
        if(sizeof($all_user_events) == 0) return;

        foreach($all_user_events as $event){
            $out_file = $config["exchange_dir"] . uniqid() . ".json";
			$json = json_encode($event, JSON_PRETTY_PRINT);
			file_put_contents($out_file, $json);
        }
    }

    function push_events_to_webhook() {
        global $all_user_events, $config;
        if(sizeof($all_user_events) == 0) return;

		$body = json_encode([
		    "events" => $all_user_events
		]);
		$ch = curl_init($config["webhook_url"]);
		curl_setopt_array($ch, [
			CURLOPT_POST           => true,
		    CURLOPT_POSTFIELDS     => $body,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_HTTPHEADER     => [
				"Content-Type: application/json",
				"Content-Length: " . strlen($body)
			]
		]);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error    = curl_error($ch);

		curl_close($ch);
		if ($error) {
			echo "cURL Error to Webhook: " . $error;
		}
    }

    function push_events() {
        global $all_user_events, $config;
        if(sizeof($all_user_events) == 0) return;

        echo "<pre>";
        print_r($all_user_events);
        echo "</pre>";

        if($config["send_events_to_exchange_dir"]){
            push_events_to_exchange_dir();
        }

        if($config["send_events_to_webhook"]){
            push_events_to_webhook();
        }
    }

    function get_events() {
        global $all_user_events;
        return $all_user_events;
    }

?>