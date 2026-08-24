<?php

    /**
     * Achievement "Bingo-Zünder" (BingoTrigger).
     *
     * Misst, wie viele Bingos insgesamt (über alle Spieler eines Spiels
     * hinweg summiert) ausgelöst wurden, als der aktuelle Spieler
     * (self::$user) EINE einzelne Zahl getippt hat.
     *
     * Beispiel: Spieler X tippt die Zahl 14. Dadurch vervollständigen
     * mehrere Spieler gleichzeitig eine oder mehrere Reihen/Spalten/
     * Diagonalen auf ihrer Karte -> in Summe entstehen z.B. 6 neue Bingos
     * bei mehreren Leuten. Dieser "Treffer" von 6 ist der Wert, der für
     * dieses Achievement zählt.
     *
     * Das Achievement speichert den besten (höchsten) jemals mit einer
     * einzigen getippten Zahl ausgelösten Bingo-Wert über die komplette
     * Spielhistorie hinweg und schaltet danach Level 1 bis 10 frei.
     *
     * Die komplette Berechnung wird - wie bei den anderen Modulen auch -
     * ausschließlich aus der Spielhistorie (self::$game_history)
     * rekonstruiert, es werden keine zusätzlichen/eigenen Datenquellen
     * benötigt.
     */
    class BingoTrigger extends Achievement {
        private $keyvalue = 0;

        protected function update(): void {
            $best = 0;

            foreach(self::$game_history as $game){
                if(!$game){ continue; }

                $marked_numbers = $game['game']->get_marked_numbers();
                $players = $game['game']->get_all_players();

                $marked = array();
                $previous_bingos = array();

                foreach($players as $player){
                    $previous_bingos[$player['player']] = 0;
                }

                foreach($marked_numbers as $marked_number){
                    // Zahl in die aktuell aufgedeckte Menge übernehmen.
                    // Ein erneutes Tippen einer bereits aufgedeckten Zahl
                    // verändert den Spielstand nicht (idempotent), daher
                    // ist keine gesonderte Behandlung von
                    // "is_already_marked" nötig.
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

                    // Wurde diese Zahl vom aktuell betrachteten Nutzer
                    // getippt, wird gezählt, wie viele neue Bingos dadurch
                    // in Summe über alle Spieler entstanden sind.
                    if($marked_number['player'] == self::$user){
                        $triggered = 0;
                        foreach($current_bingos as $player => $bingo_count){
                            $diff = $bingo_count - $previous_bingos[$player];
                            if($diff > 0){
                                $triggered += $diff;
                            }
                        }
                        if($triggered > $best){
                            $best = $triggered;
                        }
                    }

                    $previous_bingos = $current_bingos;
                }
            }

            $this->keyvalue = $best;
        }

        protected function isAchieved(): bool {
            return $this->keyvalue >= 1;
        }

        public function getElement(): ?array {
            if($this->keyvalue >= 10) {
                return [
                    'title' => 'Bingo-Detonator',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 10 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 10,
                    'img' => 'bingotrigger_level10.png',
                ];
            }
            if($this->keyvalue >= 9) {
                return [
                    'title' => 'Sprengmeister',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 9 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 9,
                    'img' => 'bingotrigger_level9.png',
                ];
            }
            if($this->keyvalue >= 8) {
                return [
                    'title' => 'Bingo-Lawine',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 8 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 8,
                    'img' => 'bingotrigger_level8.png',
                ];
            }
            if($this->keyvalue >= 7) {
                return [
                    'title' => 'Dominoeffekt',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 7 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 7,
                    'img' => 'bingotrigger_level7.png',
                ];
            }
            if($this->keyvalue >= 6) {
                return [
                    'title' => 'Sechsfach-Knall',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 6 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 6,
                    'img' => 'bingotrigger_level6.png',
                ];
            }
            if($this->keyvalue >= 5) {
                return [
                    'title' => 'Kettensprenger',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 5 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 5,
                    'img' => 'bingotrigger_level5.png',
                ];
            }
            if($this->keyvalue >= 4) {
                return [
                    'title' => 'Vierfach-Treffer',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 4 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 4,
                    'img' => 'bingotrigger_level4.png',
                ];
            }
            if($this->keyvalue >= 3) {
                return [
                    'title' => 'Kettenreaktion',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 3 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 3,
                    'img' => 'bingotrigger_level3.png',
                ];
            }
            if($this->keyvalue >= 2) {
                return [
                    'title' => 'Doppelzünder',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 2 Bingos gleichzeitig ausgelöst (bei beliebigen Spielern).',
                    'level' => 2,
                    'img' => 'bingotrigger_level2.png',
                ];
            }
            if($this->keyvalue >= 1) {
                return [
                    'title' => 'Erster Funke',
                    'description' => 'Du hast mit einer einzigen getippten Zahl mindestens 1 Bingo ausgelöst (bei beliebigen Spielern).',
                    'level' => 1,
                    'img' => 'bingotrigger_level1.png',
                ];
            }

            return null;
        }

        public function getNumberOfPossibleLevels(): int {
            return 10;
        }

        public function getAchievementName(): string {
            return 'Bingo-Zünder';
        }
    }

    $available_achievements[] = new BingoTrigger();

?>
