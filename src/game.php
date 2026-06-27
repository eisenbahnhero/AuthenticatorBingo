<?php

	class Game {
		private string $id;
		private $players = array();
		private $marked_numbers = array();
		
		private function generate_new_card(){
			$card = array();
			$randoms = array();
			for($i = 10; $i < 100; $i++){
				$randoms[] = $i;
			}
			shuffle($randoms);
			
			for($r = 0; $r < 5; $r++){
				$row = array();
				for($c = 0; $c < 5; $c++){
					$row[] = array_pop($randoms);
				}
				$card[] = $row;
			}
			$card[2][2] = "FREE";
			return $card;
		}
		
		private function validate_card($card){
			// Dimensions
			if(sizeof($card) != 5){
				return false;
			}
			for($i = 0; $i < 5; $i++){
				if(sizeof($card[$i]) != 5){
					return false;
				}
			}

			// Range and FREE
			for($i = 0; $i < 5; $i++){
				for($j = 0; $j < 5; $j++){
					if($card[$i][$j] == "FREE" && !($i == 2 && $j == 2)){
						return false;
					}

					if($card[$i][$j] != "FREE"){
						if($card[$i][$j] > 99 || $card[$i][$j] < 10){
							return false;
						}
					}
				}
			}

			// Duplicates
			$numbers = array();
			for($i = 0; $i < 5; $i++){
				for($j = 0; $j < 5; $j++){
					if($card[$i][$j] != "FREE"){
						if(in_array($card[$i][$j], $numbers)){
							return false;
						}
						$numbers[] = $card[$i][$j];
					}
				}
			}

			return true;
		}

		private function get_bingos_count($card){
			$bingos = 0;
			
			//Rows
			for($i = 0; $i < 5; $i++){
				$c = 0;
				for($j = 0; $j < 5; $j++){
					if($card[$i][$j] == "FREE" || $this->is_marked($card[$i][$j])){
						$c++;
					}
				}
				$bingos += ($c == 5 ? 1 : 0);
			}
			
			//Columns
			for($i = 0; $i < 5; $i++){
				$c = 0;
				for($j = 0; $j < 5; $j++){
					if($card[$j][$i] == "FREE" || $this->is_marked($card[$j][$i])){
						$c++;
					}
				}
				$bingos += ($c == 5 ? 1 : 0);
			}
			
			//Diagonals
			$c1 = 0; $c2 = 0;
			for($i = 0; $i < 5; $i++){
				if($card[$i][$i] == "FREE" || $this->is_marked($card[$i][$i])){
					$c1++;
				}
				if($card[$i][4-$i] == "FREE" || $this->is_marked($card[$i][4-$i])){
					$c2++;
				}
			}
			$bingos += ($c1 == 5 ? 1 : 0);
			$bingos += ($c2 == 5 ? 1 : 0);
			
			return $bingos;
		}
		
		public function load_game(){
			global $config;
			$path = $config["data_dir"] .  $this->id . ".json";
			
			if(file_exists($path)){
				$json = file_get_contents($path);
				$game = json_decode($json, true);
				
				$this->players = $game["players"];
				$this->marked_numbers = $game["marked_numbers"];
			}
		}
		
		public function __construct(string $id = null){
			if($id == null){
				$id = uniqid();
			}
			
			$this->id = $id;
			$this->load_game();
		}
		
		public function save_game(){
			global $config;
			$path = $config["data_dir"] .  $this->id . ".json";
			
			$game = array("players" => $this->players, "marked_numbers" => $this->marked_numbers);
			$game = json_encode($game, JSON_PRETTY_PRINT);
			
			file_put_contents($path, $game);
		}
		
		public function register_player(string $player, $card = null){
			if($this->is_registered($player)){
				return;
			}

			if($card == null){
				$card = $this->generate_new_card();
			}

			if(!$this->validate_card($card)){
				return;
			}
			
			$this->players[] = array("player" => $player, "card" => $card);	
		}
		
		public function get_all_players(){
			$resp = [];
			foreach($this->players as $p){
				$resp[] = array("player" => $p["player"], "bingos" => $this->get_bingos_count($p["card"]), "card" => $p["card"], "marked_by_himself" => $this->how_much_numbers_marked_by_himself($p["player"]));
			}
			return $resp;
		}

		public function get_all_players_blanko($skip = null){
			$resp = [];
			foreach($this->players as $p){

				if($skip != null && $p["player"] == $skip){
					continue;
				}

				$resp[] = array("player" => $p["player"], "card" => $p["card"]);
			}

			for($p = 0; $p < count($resp); $p++){
				for($card_row = 0; $card_row < count($resp[$p]["card"]); $card_row++){
					for($card_col = 0; $card_col < count($resp[$p]["card"][$card_row]); $card_col++){
						if($resp[$p]["card"][$card_row][$card_col] == "FREE"){
							continue;
						}

						if($this->is_marked($resp[$p]["card"][$card_row][$card_col])){
							$resp[$p]["card"][$card_row][$card_col] = true;
						}
						else{
							$resp[$p]["card"][$card_row][$card_col] = false;
						}
					}
				}
			}

			return $resp;
		}
		
		public function get_player(string $player){
			$p = $this->get_all_players();
			foreach($p as $c){
				if($c["player"] == $player){
					return $c;
				}
			}
			return null;
		}
		
		public function mark_number(int $num, string $player, bool $is_already_marked = false){
			global $config;
			
			if($num > 99 || $num < 10 || $num == null){
				return false;
			}

			$is_in = false;
			foreach($this->players as $p){
				if($player == $p["player"]){
					$is_in = true;
				}
			}
			if(!$is_in){
				return false;
			}
			
			// Register new marked number
			$bingos_before = $this->get_all_players();
			$obj = array("number" => $num, "player" => $player, "timestamp" => time());
			if($is_already_marked){
				$obj["is_already_marked"] = true;
			}
			$this->marked_numbers[] = $obj;
			
			if($config["write_alerts_to_exchange_dir"]){
				// Write new marked number to exchange dir
				$obj = array("type" => "new_number_marked", "number" => $num, "player" => $player, "timestamp" => time());
				$out_file = $config["exchange_dir"] . uniqid() . ".json";
				$json = json_encode($obj, JSON_PRETTY_PRINT);
				file_put_contents($out_file, $json);

				// Affect the new number a new bingo to the players
				$bingos_after = $this->get_all_players();
				$compare = array();
				foreach($bingos_before as $bb){
					foreach($bingos_after as $ba){
						if($bb["player"] == $ba["player"]){
							$compare[] = array("player" => $bb["player"], "bingos_before" => $bb["bingos"], "bingos_after" => $ba["bingos"]);
						}
					}
				}

				// Write new bingos to exchange dir
				foreach($compare as $c){
					if($c["bingos_after"] > $c["bingos_before"]){
						$obj = array("type" => "new_bingo", "player" => $c["player"], "bingos" => $c["bingos_after"], "timestamp" => time()+1);
						$out_file = $config["exchange_dir"] . uniqid() . ".json";
						$json = json_encode($obj, JSON_PRETTY_PRINT);
						file_put_contents($out_file, $json);
					}
				}
			}
			
			return true;
		}
		
		public function is_registered(string $player){
			$is_in = false;
			foreach($this->players as $p){
				if($player == $p["player"]){
					$is_in = true;
				}
			}
			return $is_in;
		}
		
		public function get_marked_numbers(){
			return $this->marked_numbers;
		}
		
		public function is_marked(int $num){
			foreach($this->marked_numbers as $mn){
				if($mn["number"] == $num){
					return true;
				}
			}
			return false;
		}
		
		public function who_had_marked(int $num){
			foreach($this->marked_numbers as $mn){
				if($mn["number"] == $num && !isset($mn["is_already_marked"])){
					return $mn;
				}
			}
			return null;
		}
		
		public function get_id(){
			return $this->id;
		}

		public function how_much_numbers_marked_by_himself(string $player){
			$count = 0;
			foreach($this->players as $g){
				if($g["player"] == $player){
					foreach($g["card"] as $row){
						foreach($row as $num){
							if($num != "FREE"){
								$who = $this->who_had_marked($num);
								if($who != null && $who["player"] == $player){
									$count++;
								}
							}
						}
					}

					break;
				}
			}
			return $count;
		}

		public function get_how_many_numbers_marked_grouped_by_player(bool $with_duplicates = false){
			$coverage = array();
			foreach($this->marked_numbers as $mn){
				if(isset($mn["is_already_marked"]) && !$with_duplicates){ continue; }
				if(!isset($coverage[$mn["player"]])){
					$coverage[$mn["player"]] = 0;
				}
				$coverage[$mn["player"]]++;
			}

			return $coverage;
		}

		public function get_how_many_numbers_tipped(bool $with_duplicates = false){
			if($with_duplicates){
				return count($this->marked_numbers);
			}
			return count(array_unique(array_column($this->marked_numbers, "number")));
		}

		public function get_avg_marked_numbers_per_player(bool $with_duplicates = false){
			$g = $this->get_how_many_numbers_marked_grouped_by_player($with_duplicates);
			if(count($g) == 0){ return 0; }
			return array_sum($g) / count($g);
		}

		public function get_avg_marked_numbers_per_day(bool $with_duplicates = false){
			$per_day = array();

			foreach($this->marked_numbers as $mn){
				if(isset($mn["is_already_marked"]) && !$with_duplicates){ continue; }

				$day = date("Y-m-d", $mn["timestamp"]);
				if(!isset($per_day[$day])){ $per_day[$day] = 0; }
				$per_day[$day]++;
			}

			if(empty($per_day) || count($per_day) == 0){ return 0; }
			return (int) round(array_sum($per_day) / count($per_day));
		}

	}

	function get_all_games(){
		global $config;
		$games = array();
		foreach(scandir($config["data_dir"]) as $file){
			if(strpos($file, ".json") !== false){
				$g = str_replace(".json", "", $file);
				$games[] = array("id" => $g, "game" => new Game($g));
			}
		}
		return $games;
	}
	
	function get_overall_statistics($all_games){
		$stats = array();
		foreach($all_games as $g){
			foreach($g["game"]->get_all_players() as $p){
				if(!isset($stats[$p["player"]])){
					$stats[$p["player"]] = 0;
				}
				$stats[$p["player"]] += $p["bingos"];
			}
		}

		//Get keys and values
		$keys = array_keys($stats);
		$res = array();
		foreach($keys as $k){
			$res[] = array("player" => $k, "bingos" => $stats[$k]);
		}
		return $res;
	}

?>