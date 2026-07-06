<?php
	session_start();

	require_once 'src/config.php';
	require_once 'src/events.php';
	require_once 'src/auth.php';
	require_once 'src/game.php';

	//ACL check
	if($config["use_acl"]){
		$allowed = false;
		foreach($config["acl_allowed_players"] as $player){
			if($player == $current_user){
				$allowed = true;
			}
		}
		if(!$allowed){
			require_once 'pages/no-access.php';
			die();
		}
	}

	//Check folders
	if(!is_dir($config["data_dir"])){
		mkdir($config["data_dir"]);
	}
	if(!is_dir($config["exchange_dir"]) && $config["send_events_to_exchange_dir"]){
		mkdir($config["exchange_dir"]);
	}

	$current_game_id = date("Y-m");
	$current_game = new Game($current_game_id);
	$should_be_saved = false;
	$alert_message = array();
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		//Register for current round
		if(isset($_REQUEST["register"])){
			if(!isset($_REQUEST["card_numbers"])){
				$current_game->register_player($current_user);
			}
			else{
				$card_numbers = json_decode($_REQUEST["card_numbers"], true);
				$card = array();
				$curr_arr = array();
				for($i = 0; $i < sizeof($card_numbers); $i++){
					$curr_arr[] = $card_numbers[$i] == null ? "FREE" : $card_numbers[$i];
					if(sizeof($curr_arr) == 5){
						$card[] = $curr_arr;
						$curr_arr = array();
					}
				}

				$method = "-";
				if(isset($_REQUEST["register_method"])){
					$method = $_REQUEST["register_method"];
				}

				$current_game->register_player($current_user, $card, $method);
			}
			$should_be_saved = true;
		}
		
		//Mark a new number
		if(isset($_REQUEST["mark"])){
			$current_game->mark_number($_REQUEST["mark"], $current_user);
			$should_be_saved = true;
		}
		
		//Save game
		if($should_be_saved){
			$current_game->save_game();
		}

		//Push new Events
		push_events();

		//Redirect to avoid resubmission
		$_SESSION["alert_message"] = get_events();
		header("Location: " . $_SERVER['PHP_SELF']);
    	exit;
	}

	if(isset($_SESSION["alert_message"])){
		$alert_message = $_SESSION["alert_message"];
		unset($_SESSION["alert_message"]);
	}

?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<title><?php echo $config["title"];?></title>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="src/stylesheet.css" />
		<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
		<link rel="manifest" href="favicon/site.webmanifest">
	</head>
	<body>

		<a class="github-link" href="https://github.com/eisenbahnhero/AuthenticatorBingo" target="_blank" rel="noopener noreferrer" title="Authenticator Bingo on GitHub">
			<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.385-1.335-1.755-1.335-1.755-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
			Authenticator Bingo on GitHub
		</a>

		<div class="page-wrapper">

			<!-- ── HEADER ─────────────────────────────── -->
			<header class="site-header">
				<h1><?php 
					$parts = explode(' ', $config["title"], 2);
					echo $parts[0];
					if(isset($parts[1])) echo ' <span>' . $parts[1] . '</span>';
					else echo ' <span>BINGO</span>';
				?></h1>
				<div class="user-badge">
					Eingeloggt als <strong><?php echo $current_user; ?></strong>
				</div>
			</header>

			<?php if($current_game->is_registered($current_user)): ?>

				<!-- ── ROUND BADGE ────────────────────────── -->
				<div class="round-badge">Runde <?php echo $current_game_id; ?></div>

				<!-- ── ALERT ──────────────────────────────── -->
				<?php require_once("pages/alerts.php"); ?>

				<!-- ── TAB NAVIGATION ─────────────────────── -->
				<nav class="tab-nav" role="tablist">
					<button class="tab-btn active" onclick="switchTab('spielkarte', this)" role="tab">Spielkarte</button>
					<button class="tab-btn" onclick="switchTab('uebersicht', this)" role="tab">Verlauf &amp; Rangliste</button>
					<button class="tab-btn" onclick="switchTab('gesamtuebersicht', this)" role="tab">Gesamtübersicht</button>
					<button class="tab-btn" onclick="switchTab('regeln', this)" role="tab">Spielregeln</button>
					<button class="tab-btn" onclick="switchTab('archiv', this)" role="tab">Archiv</button>
				</nav>
		
				<!-- /tab-spielkarte -->
				<div id="tab-spielkarte" class="tab-pane active">
					<?php require_once("pages/game.php"); ?>
				</div>

				<!-- /tab-verlauf-rangliste -->
				<div id="tab-uebersicht" class="tab-pane">
					<?php require_once("pages/history.php"); ?>
				</div>

				<!-- /tab-regeln -->
				<div id="tab-regeln" class="tab-pane">
					<?php require_once("pages/rules.php"); ?>
				</div>

				<!-- /tab-archiv -->
				<div id="tab-archiv" class="tab-pane">
					<?php require_once("pages/archiv.php"); ?>
				</div>

				<!-- /tab-gesamtuebersicht -->
				<div id="tab-gesamtuebersicht" class="tab-pane">
					<?php require_once("pages/overall.php"); ?>
				</div>


			<?php else: ?>
			<?php require_once 'pages/chooser.php'; ?>	
			<?php endif; ?>

		</div><!-- /page-wrapper -->

		<script>
			function switchTab(name, btn) {
				document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
				document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
				document.getElementById('tab-' + name).classList.add('active');
				btn.classList.add('active');
			}
		</script>
	</body>
</html>