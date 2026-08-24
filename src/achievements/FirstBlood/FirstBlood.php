<?php

    class FirstBlood extends Achievement {
        private $keyvalue = 0;

        protected function update(): void {
            $first_bingos = 0;

            foreach(self::$game_history as $game){
                $marked_numbers = $game['game']->get_marked_numbers();
                $players = $game['game']->get_all_players();
                $marked = array();
                $previous_bingos = array();

                foreach($players as $player){
                    $previous_bingos[$player['player']] = 0;
                }

                foreach($marked_numbers as $marked_number){
                    $marked[$marked_number['number']] = true;
                    $current_bingos = array();

                    foreach($players as $player){
                        $card = $player['card'];
                        $lines = array();

                        for($row = 0; $row < 5; $row++){
                            $lines[] = $card[$row];
                            $column = array();
                            for($column_index = 0; $column_index < 5; $column_index++){
                                $column[] = $card[$column_index][$row];
                            }
                            $lines[] = $column;
                        }

                        $diagonal = array();
                        $reverse_diagonal = array();
                        for($index = 0; $index < 5; $index++){
                            $diagonal[] = $card[$index][$index];
                            $reverse_diagonal[] = $card[$index][4 - $index];
                        }
                        $lines[] = $diagonal;
                        $lines[] = $reverse_diagonal;

                        $bingo_count = 0;
                        foreach($lines as $line){
                            $line_completed = true;
                            foreach($line as $number){
                                if($number !== 'FREE' && !isset($marked[$number])){
                                    $line_completed = false;
                                    break;
                                }
                            }
                            if($line_completed){
                                $bingo_count++;
                            }
                        }

                        $current_bingos[$player['player']] = $bingo_count;
                    }

                    $first_players = array();
                    foreach($current_bingos as $player => $bingo_count){
                        if($bingo_count > $previous_bingos[$player]){
                            $first_players[] = $player;
                        }
                    }

                    if(!empty($first_players)){
                        if(in_array(self::$user, $first_players, true)){
                            $first_bingos++;
                        }
                        break;
                    }

                    $previous_bingos = $current_bingos;
                }
            }

            $this->keyvalue = $first_bingos;
        }

        protected function isAchieved(): bool{
            return $this->keyvalue >= 1;
        }

        public function getElement() : ?array {
            if($this->keyvalue >= 5) {
                return [
                    'title' => 'First Blood Legende',
                    'description' => 'Du warst in mindestens fünf Monaten der erste Spieler mit einem Bingo.',
                    'level' => 5,
                    'img' => 'firstblood_level5.png',
                ];
            }
            if($this->keyvalue >= 4) {
                return [
                    'title' => 'Bingo-Überfall',
                    'description' => 'Du warst in mindestens vier Monaten der erste Spieler mit einem Bingo.',
                    'level' => 4,
                    'img' => 'firstblood_level4.png',
                ];
            }
            if($this->keyvalue >= 3) {
                return [
                    'title' => 'Blitzstarter',
                    'description' => 'Du warst in mindestens drei Monaten der erste Spieler mit einem Bingo.',
                    'level' => 3,
                    'img' => 'firstblood_level3.png',
                ];
            }
            if($this->keyvalue >= 2) {
                return [
                    'title' => 'Bingo-Pionier',
                    'description' => 'Du warst in mindestens zwei Monaten der erste Spieler mit einem Bingo.',
                    'level' => 2,
                    'img' => 'firstblood_level2.png',
                ];
            }
            if($this->keyvalue >= 1) {
                return [
                    'title' => 'Bingo-Erstschlag',
                    'description' => 'Du warst in mindestens einem Monat der erste Spieler mit einem Bingo.',
                    'level' => 1,
                    'img' => 'firstblood_level1.png',
                ];
            }

            return null;
        }

        public function getNumberOfPossibleLevels() : int {
            return 5;
        }

        public function getAchievementName(): string {
            return "First Blood";
        }

    }

    $available_achievements[] = new FirstBlood();

?>