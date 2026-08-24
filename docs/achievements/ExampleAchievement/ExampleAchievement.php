<?php

    /**
     * Example achievement that counts the number of rounds in which the
     * current player has been registered. This class demonstrates the full
     * structure required for a custom achievement module.
     *
     * The class name must match the module folder name. For example, this
     * class belongs in src/achievements/ExampleAchievement/ and is loaded
     * automatically by src/achievements.php.
     */
    class ExampleAchievement extends Achievement {
        // Stores the progress value calculated by update(). It is reset and
        // recalculated whenever the achievement is evaluated.
        private $keyvalue = 0;

        /**
         * Calculates the current progress for Achievement::$user.
         *
         * The shared game history contains all available rounds. A round is
         * counted when the current player is registered in that round. Store
         * the result in $keyvalue so the other methods can evaluate it.
         */
        protected function update(): void {
            $this->keyvalue = 0;

            foreach (self::$game_history as $game) {
                if ($game && $game['game']->is_registered(self::$user)) {
                    $this->keyvalue++;
                }
            }
        }

        /**
         * Determines whether the achievement should be shown to the player.
         *
         * This example becomes available after the player has participated in
         * at least one round. Keep this threshold in sync with the first
         * level returned by getElement().
         */
        protected function isAchieved(): bool {
            return $this->keyvalue >= 1;
        }

        /**
         * Returns the highest level currently reached by the player.
         *
         * Level checks must be ordered from the highest threshold to the
         * lowest threshold. Return null when no level has been reached yet.
         *
         * Every non-null result must be an associative array containing
         * exactly these four attributes and no others:
         *
         * - title: string displayed as the achievement title
         * - description: string explaining how this level was earned
         * - level: integer containing the reached level number
         * - img: string containing the image filename in this module folder
         *
         * The attribute names and their value types are required by the
         * achievement page and must not be changed or omitted.
         */
        public function getElement(): ?array {
            if ($this->keyvalue >= 5) {
                return [
                    'title' => 'Round Master',
                    'description' => 'Du hast an mindestens fünf Runden teilgenommen.',
                    'level' => 2,
                    'img' => 'example_level2.png',
                ];
            }

            if ($this->keyvalue >= 1) {
                return [
                    'title' => 'Round Rookie',
                    'description' => 'Du hast an mindestens einer Runde teilgenommen.',
                    'level' => 1,
                    'img' => 'example_level1.png',
                ];
            }

            return null;
        }

        /**
         * Returns the total number of levels this achievement supports.
         *
         * This value is used to render progress information. It must equal
         * the highest level number defined in getElement().
         */
        public function getNumberOfPossibleLevels(): int {
            return 2;
        }

        /**
         * Returns the stable name used to identify this achievement in event
         * snapshots. Keep this name unchanged after the module is deployed,
         * otherwise the application may treat it as a new achievement.
         */
        public function getAchievementName(): string {
            return 'Example Achievement';
        }
    }

    // Registers the module so the application evaluates and displays it.
    $available_achievements[] = new ExampleAchievement();

?>
