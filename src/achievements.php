<?php

    abstract class Achievement {
        protected static $game_history;
        protected static $user;

        public static function setGameHistory($game_history): void {
            self::$game_history = $game_history;
        }

        public static function getGameHistory(){
            return self::$game_history;
        }

        public static function setUser($user): void {
            self::$user = $user;
        }

        public static function getUser() {
            return self::$user;
        }

        public function isElementAvailable(): bool {
            $this->update();
            return $this->isAchieved();
        }

        abstract protected function update(): void;
        abstract protected function isAchieved(): bool;
        abstract public function getElement() : ?array;
        abstract public function getNumberOfPossibleLevels() : int;
        abstract public function getAchievementName(): string;
    }

    $available_achievements = array();

    foreach (glob(__DIR__ . '/achievements/*/*.php') as $achievement_file) {
        require_once $achievement_file;
    }

    function getAchievementSnapshot(){
        global $available_achievements;

        $o_user = Achievement::getUser();
        $all_users = array();
        foreach(Achievement::getGameHistory() as $game){
            if($game){
                $all_users = array_merge($all_users, $game['game']->get_all_player_names());
            }
        }
        $all_users = array_unique($all_users);

        $snapshot = array();
        foreach($all_users as $user){
            Achievement::setUser($user);
            $user_obj = array();
            $user_obj['name'] = $user;
            $user_obj['achievements'] = array();

            foreach($available_achievements as $achivement){
                if($achivement->isElementAvailable()){
                    $achivement_obj = array();
                    $achivement_obj['name'] = $achivement->getAchievementName();
                    $achivement_obj['element'] = $achivement->getElement();
                    $user_obj['achievements'][] = $achivement_obj;
                }
            }
            $snapshot[] = $user_obj;
        }

        Achievement::setUser($o_user);
        return $snapshot;
    }

    function compareAchievementSnapshots($snap1, $snap2){
        foreach($snap2 as $curr_snap2){
            $previous_achievements = array();

            foreach($snap1 as $curr_snap1){
                if($curr_snap1['name'] == $curr_snap2['name']){
                    foreach($curr_snap1['achievements'] as $achievement){
                        $previous_achievements[$achievement['name']] = $achievement;
                    }
                    break;
                }
            }

            foreach($curr_snap2['achievements'] as $achievement){
                $achievement_name = $achievement['name'];
                $achievement_level = (int) $achievement['element']['level'];

                if(!isset($previous_achievements[$achievement_name])){
                    $data = array();
                    $data['player'] = $curr_snap2['name'];
                    $data['achievement'] = $achievement_name;
                    add_event("new_achievement_unlocked", $data);
                    continue;
                }

                $previous_level = (int) $previous_achievements[$achievement_name]['element']['level'];
                if($achievement_level > $previous_level){
                    $data = array();
                    $data['player'] = $curr_snap2['name'];
                    $data['achievement'] = $achievement_name;
                    $data['level'] = $achievement_level;
                    add_event("next_achievement_level_reached", $data);
                }
            }
        }
    }


?>