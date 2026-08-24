<?php

    /**
     * Multi Kill Achievement.
     *
     * Prämiert Momente, in denen eine einzige getippte Zahl mehrere Bingos
     * auf einmal auslöst (z.B. weil eine Zahl gleichzeitig eine Reihe, eine
     * Spalte und eine Diagonale vervollständigt).
     *
     * Level 1 – Double Kill : 2 Bingos gleichzeitig durch dieselbe Zahl
     * Level 2 – Triple Kill : 3 Bingos gleichzeitig durch dieselbe Zahl
     * Level 3 – Quadro Kill : 4 (oder mehr) Bingos gleichzeitig durch dieselbe Zahl
     *
     * Die komplette Spielhistorie (self::$game_history) wird dazu Zahl für
     * Zahl in der Reihenfolge, in der sie getippt wurden, nachgespielt. Für
     * jede getippte Zahl wird verglichen, wie viele Bingos die Karte des
     * Spielers direkt davor und direkt danach hatte. Der größte dabei
     * beobachtete Sprung über alle Spiele hinweg ist der Fortschrittswert.
     */
    class MultiKill extends Achievement {
        // Größter jemals in einem einzigen Zug erzielter Bingo-Sprung.
        private $keyvalue = 0;

        /**
         * Zählt für eine Karte, wie viele Bingo-Linien (Reihen, Spalten,
         * Diagonalen) mit dem übergebenen Set an markierten Zahlen bereits
         * vollständig sind. Analog zu Game::get_bingos_count(), aber lokal
         * nachgebaut, da diese Logik in der Game-Klasse nicht öffentlich ist.
         */
        private function count_bingos(array $card, array $marked): int {
            $bingos = 0;

            // Reihen
            for ($row = 0; $row < 5; $row++) {
                $complete = 0;
                for ($col = 0; $col < 5; $col++) {
                    $value = $card[$row][$col];
                    if ($value === 'FREE' || isset($marked[$value])) {
                        $complete++;
                    }
                }
                if ($complete == 5) { $bingos++; }
            }

            // Spalten
            for ($col = 0; $col < 5; $col++) {
                $complete = 0;
                for ($row = 0; $row < 5; $row++) {
                    $value = $card[$row][$col];
                    if ($value === 'FREE' || isset($marked[$value])) {
                        $complete++;
                    }
                }
                if ($complete == 5) { $bingos++; }
            }

            // Diagonalen
            $diag1 = 0; $diag2 = 0;
            for ($i = 0; $i < 5; $i++) {
                $v1 = $card[$i][$i];
                $v2 = $card[$i][4 - $i];
                if ($v1 === 'FREE' || isset($marked[$v1])) { $diag1++; }
                if ($v2 === 'FREE' || isset($marked[$v2])) { $diag2++; }
            }
            if ($diag1 == 5) { $bingos++; }
            if ($diag2 == 5) { $bingos++; }

            return $bingos;
        }

        protected function update(): void {
            $best_kill = 0;

            foreach (self::$game_history as $game) {
                if (!$game) { continue; }

                $player = $game['game']->get_player(self::$user);
                if (!$player) { continue; }

                $card = $player['card'];
                $marked_numbers = $game['game']->get_marked_numbers();

                $marked = array();
                $previous_bingos = 0;

                foreach ($marked_numbers as $marked_number) {
                    // Ein bereits markierte Zahl erneut zu tippen kann keinen
                    // neuen Bingo auslösen, das Set bleibt einfach unverändert.
                    $marked[$marked_number['number']] = true;

                    $current_bingos = $this->count_bingos($card, $marked);
                    $kill = $current_bingos - $previous_bingos;

                    if ($kill > $best_kill) {
                        $best_kill = $kill;
                    }

                    $previous_bingos = $current_bingos;
                }
            }

            $this->keyvalue = $best_kill;
        }

        protected function isAchieved(): bool {
            return $this->keyvalue >= 2;
        }

        public function getElement(): ?array {
            if ($this->keyvalue >= 4) {
                return [
                    'title' => 'Quadro Kill',
                    'description' => 'Du hast mindestens einmal 4 Bingos gleichzeitig durch dieselbe getippte Zahl erzielt (auf der eigenen Karte).',
                    'level' => 3,
                    'img' => 'multikill_level3.png',
                ];
            }
            if ($this->keyvalue >= 3) {
                return [
                    'title' => 'Triple Kill',
                    'description' => 'Du hast mindestens einmal 3 Bingos gleichzeitig durch dieselbe getippte Zahl erzielt (auf der eigenen Karte).',
                    'level' => 2,
                    'img' => 'multikill_level2.png',
                ];
            }
            if ($this->keyvalue >= 2) {
                return [
                    'title' => 'Double Kill',
                    'description' => 'Du hast mindestens einmal 2 Bingos gleichzeitig durch dieselbe getippte Zahl erzielt (auf der eigenen Karte).',
                    'level' => 1,
                    'img' => 'multikill_level1.png',
                ];
            }

            return null;
        }

        public function getNumberOfPossibleLevels(): int {
            return 3;
        }

        public function getAchievementName(): string {
            return 'Multi Kill';
        }
    }

    $available_achievements[] = new MultiKill();

?>
