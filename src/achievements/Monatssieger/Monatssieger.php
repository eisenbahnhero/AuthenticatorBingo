<?php

    class Monatssieger extends Achievement {
        private $keyvalue = 0;

        public function getAchievementName(): string {
            return "Monatssiege";
        }

        protected function update(): void {
            $wins = 0;
            foreach(self::$game_history as $game){
                $best_list = $game['game']->get_best_list();
                if(date("Y-m") == $game['game']->get_id()) continue;

                if(isset($best_list[0]['player'])){
                    if($best_list[0]['player'] == self::$user){
                        $wins++;
                    }
                }
            }
            $this->keyvalue = $wins;
        }

        protected function isAchieved(): bool {
            return $this->keyvalue >= 1;
        }

        public function getNumberOfPossibleLevels(): int {
            return 10;
        }

        public function getElement(): ?array {
            if($this->keyvalue >= 10) {
                return [
                    'title' => 'Monatskaiser',
                    'description' => 'Du hast mindestens zehn abgeschlossene Monatsrunden gewonnen.',
                    'level' => 10,
                    'img' => 'monatssieger_level10.png',
                ];
            }
            if($this->keyvalue >= 9) {
                return [
                    'title' => 'Bingo-Dynastie',
                    'description' => 'Du hast mindestens neun abgeschlossene Monatsrunden gewonnen.',
                    'level' => 9,
                    'img' => 'monatssieger_level9.png',
                ];
            }
            if($this->keyvalue >= 8) {
                return [
                    'title' => 'Runden-Royalty',
                    'description' => 'Du hast mindestens acht abgeschlossene Monatsrunden gewonnen.',
                    'level' => 8,
                    'img' => 'monatssieger_level8.png',
                ];
            }
            if($this->keyvalue >= 7) {
                return [
                    'title' => 'Bingo-Baron',
                    'description' => 'Du hast mindestens sieben abgeschlossene Monatsrunden gewonnen.',
                    'level' => 7,
                    'img' => 'monatssieger_level7.png',
                ];
            }
            if($this->keyvalue >= 6) {
                return [
                    'title' => 'Siegesmaschine',
                    'description' => 'Du hast mindestens sechs abgeschlossene Monatsrunden gewonnen.',
                    'level' => 6,
                    'img' => 'monatssieger_level6.png',
                ];
            }
            if($this->keyvalue >= 5) {
                return [
                    'title' => 'Bingo-Champion',
                    'description' => 'Du hast mindestens fünf abgeschlossene Monatsrunden gewonnen.',
                    'level' => 5,
                    'img' => 'monatssieger_level5.png',
                ];
            }
            if($this->keyvalue >= 4) {
                return [
                    'title' => 'Doppelsieger',
                    'description' => 'Du hast mindestens vier abgeschlossene Monatsrunden gewonnen.',
                    'level' => 4,
                    'img' => 'monatssieger_level4.png',
                ];
            }
            if($this->keyvalue >= 3) {
                return [
                    'title' => 'Bingo-Stratege',
                    'description' => 'Du hast mindestens drei abgeschlossene Monatsrunden gewonnen.',
                    'level' => 3,
                    'img' => 'monatssieger_level3.png',
                ];
            }
            if($this->keyvalue >= 2) {
                return [
                    'title' => 'Siegesanwärter',
                    'description' => 'Du hast mindestens zwei abgeschlossene Monatsrunden gewonnen.',
                    'level' => 2,
                    'img' => 'monatssieger_level2.png',
                ];
            }
            if($this->keyvalue >= 1) {
                return [
                    'title' => 'Monatsrookie',
                    'description' => 'Du hast mindestens eine abgeschlossene Monatsrunde gewonnen.',
                    'level' => 1,
                    'img' => 'monatssieger_level1.png',
                ];
            }

            return null;
        }
    }

    $available_achievements[] = new Monatssieger();

?>