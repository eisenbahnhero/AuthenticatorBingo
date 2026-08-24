<?php

    class BingoCounter extends Achievement {
        private $keyvalue = 0;

        public function getAchievementName(): string {
            return "Bingo Counter";
        }

        protected function update(): void {
            $bingo_counter = 0;
            foreach(self::$game_history as $game) {
                $player_stats = $game["game"]->get_player(self::$user);
                if(!$player_stats) continue;
                $bingo_counter += $player_stats["bingos"];
            }
            $this->keyvalue = $bingo_counter;
        }

        protected function isAchieved(): bool {
            return $this->keyvalue >= 5;
        }

        public function getNumberOfPossibleLevels(): int {
            return 5;
        }

        public function getElement(): ?array {
            if($this->keyvalue >= 100) {
                return [
                    'title' => 'Bingo God',
                    'description' => 'Mindestens 100 Bingos über alle Spiele hinweg erreicht.',
                    'level' => 5,
                    'img' => 'bingocounter_level5.png',
                ];
            }
            if($this->keyvalue >= 75) {
                return [
                    'title' => 'Bingo Machine',
                    'description' => 'Mindestens 75 Bingos über alle Spiele hinweg erreicht.',
                    'level' => 4,
                    'img' => 'bingocounter_level4.png',
                ];
            }
            if($this->keyvalue >= 45) {
                return [
                    'title' => 'Bingo Legend',
                    'description' => 'Mindestens 45 Bingos über alle Spiele hinweg erreicht.',
                    'level' => 3,
                    'img' => 'bingocounter_level3.png',
                ];
            }
            if($this->keyvalue >= 15) {
                return [
                    'title' => 'Bingo Veteran',
                    'description' => 'Mindestens 15 Bingos über alle Spiele hinweg erreicht.',
                    'level' => 2,
                    'img' => 'bingocounter_level2.png',
                ];
            }
            if($this->keyvalue >= 5) {
                return [
                    'title' => 'Bingo Rookie',
                    'description' => 'Mindestens 5 Bingos über alle Spiele hinweg erreicht.',
                    'level' => 1,
                    'img' => 'bingocounter_level1.png',
                ];
            }

            return null;
        }
    }

    $available_achievements[] = new BingoCounter();

?>