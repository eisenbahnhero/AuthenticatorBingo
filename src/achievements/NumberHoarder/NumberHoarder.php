<?php

    class NumberHoarder extends Achievement{
        private $keyvalue = 0;

        public function getAchievementName(): string {
            return "Number Hoarder";
        }

        protected function update(): void {
            $max = 0;
            foreach(self::$game_history as $game){
                $game_counter = 0;
                $mn = $game['game']->get_marked_numbers();
                foreach($mn as $n){
                    if($n['player'] == self::$user){
                        $game_counter++;
                    }
                }
                if($game_counter > $max){
                    $max = $game_counter;
                }
            }
            $this->keyvalue = $max;
        }

        protected function isAchieved(): bool {
            return $this->keyvalue >= 20;
        }

        public function getNumberOfPossibleLevels(): int {
            return 7;
        }

        public function getElement(): ?array {
            if($this->keyvalue >= 150) {
                return [
                    'title' => 'Number Overlord',
                    'description' => 'Du hast in einem Monat mindestens 150 Zahlen getippt.',
                    'level' => 7,
                    'img' => 'numberhoarder_level7.png',
                ];
            }
            if($this->keyvalue >= 90) {
                return [
                    'title' => 'Number Titan',
                    'description' => 'Du hast in einem Monat mindestens 90 Zahlen getippt.',
                    'level' => 6,
                    'img' => 'numberhoarder_level6.png',
                ];
            }
            if($this->keyvalue >= 80) {
                return [
                    'title' => 'Digit Dominator',
                    'description' => 'Du hast in einem Monat mindestens 80 Zahlen getippt.',
                    'level' => 5,
                    'img' => 'numberhoarder_level5.png',
                ];
            }
            if($this->keyvalue >= 70) {
                return [
                    'title' => 'Number Ninja',
                    'description' => 'Du hast in einem Monat mindestens 70 Zahlen getippt.',
                    'level' => 4,
                    'img' => 'numberhoarder_level4.png',
                ];
            }
            if($this->keyvalue >= 60) {
                return [
                    'title' => 'Counter Commander',
                    'description' => 'Du hast in einem Monat mindestens 60 Zahlen getippt.',
                    'level' => 3,
                    'img' => 'numberhoarder_level3.png',
                ];
            }
            if($this->keyvalue >= 40) {
                return [
                    'title' => 'Number Hunter',
                    'description' => 'Du hast in einem Monat mindestens 40 Zahlen getippt.',
                    'level' => 2,
                    'img' => 'numberhoarder_level2.png',
                ];
            }
            if($this->keyvalue >= 20) {
                return [
                    'title' => 'Number Scout',
                    'description' => 'Du hast in einem Monat mindestens 20 Zahlen getippt.',
                    'level' => 1,
                    'img' => 'numberhoarder_level1.png',
                ];
            }

            return null;
        }

    }

    $available_achievements[] = new NumberHoarder();

?>