<?php
	require_once 'src/config.php';
	require_once 'src/events.php';
    $current_user = null;
	require_once 'src/game.php';
	require_once 'src/achievements.php';

	//Check folders
	if(!is_dir($config["data_dir"])){
        echo "Creating data directory...\n";
		mkdir($config["data_dir"]);
	}
    if(!is_dir($config["exchange_dir"]) && $config["send_events_to_exchange_dir"]){
        echo "Creating exchange directory...\n";
        mkdir($config["exchange_dir"]);
    }
    $file = $config["data_dir"] . "/../achievement_snapshot.json";
    $file_exists = file_exists($file);

    echo "Start check...\n";

    //Initialize game history
    echo "Loading game history...\n";
    $game_history = get_all_games();
	Achievement::setGameHistory($game_history);

    //Load old achievement snapshot
    echo "Loading old achievement snapshot...\n";
    $old_snap = $file_exists ? json_decode(file_get_contents($file), true) : null;

    //Get achievement snapshot
    echo "Getting current achievement snapshot...\n";
    $curr_snap = getAchievementSnapshot();

    //Compare achievement snapshots and push events if there are changes
    if($file_exists){
        echo "Comparing achievement snapshots...\n";
        compareAchievementSnapshots($old_snap, $curr_snap);
        push_events();
    }

    //Save current achievement snapshot
    echo "Saving current achievement snapshot...\n";
    $snap = json_encode($curr_snap, JSON_PRETTY_PRINT);
    file_put_contents($file, $snap);

?>