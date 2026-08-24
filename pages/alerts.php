<?php

    function echo_alert($alert){
        echo "<div class='alert-bar'>" . $alert . "</div>"; 
    }

    foreach($alert_message as $event){
        switch($event["type"]){

            case "new_player":
                echo_alert("Neuer Spieler registriert: " . $event["player"] . " (Methode: " . $event["method"] . ")");
                break;

            case "new_number_marked":
                echo_alert("Neue Zahl markiert: " . $event["number"]);
                break;

            case "number_marked_again":
                $who = $current_game->who_had_marked($event["number"]);
                echo_alert("Die Zahl " . $event["number"] . " hatte <strong>" . $who["player"] . "</strong> schon am " . date("d.m.Y H:i", $who["timestamp"]) . " Uhr markiert.");
                break;

            case "new_bingo":
                echo_alert("Neuer Bingo für Spieler: " . $event["player"] . " (Bingos: " . $event["bingos"] . ")");
                break;
            
            case "new_ultimate_bingo":
                echo_alert("ULTIMATIVES BINGO für Spieler: " . $event["player"] . " (Bingos: " . $event["bingos"] . ")");
                break;

            case "new_achievement_unlocked":
                echo_alert($event["player"] . ' hat das Achievement "' . $event['achievement'] . '" freigeschaltet');
                break;

            case "next_achievement_level_reached":
                echo_alert($event["player"] . ' hat bei "' . $event['achievement'] . '" ein neues Level erreicht: Level ' . $event['level']);
                break;

        }
    }

?>
