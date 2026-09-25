<?php
	require_once 'src/config.php';
	require_once 'src/game.php';


    # Viewing page access control
    if(!($config["viewing_enabled"] && isset($_GET["token"]) && $_GET["token"] === $config["viewing_token"])) {
        die("Viewing is disabled or invalid token provided.");
    } 

    $current_user = null;

	//Check folders
	if(!is_dir($config["data_dir"])){
		mkdir($config["data_dir"]);
	}

	$current_game_id = date("Y-m");
	$current_game = new Game($current_game_id);
	$game_history = get_all_games();
	
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<title><?php echo $config["title"];?></title>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="src/stylesheet.css?v=<?php echo filemtime(__DIR__ . '/src/stylesheet.css'); ?>" />
		<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
		<link rel="manifest" href="favicon/site.webmanifest">
	</head>
	<body>

		<div class="page-wrapper">

			<!-- ── HEADER ─────────────────────────────── -->
			<header class="site-header">
				<h1><?php 
					$parts = explode(' ', $config["title"], 2);
					echo $parts[0];
					if(isset($parts[1])) echo ' <span>' . $parts[1] . '</span>';
					else echo ' <span>BINGO</span>';
				?></h1>
			</header>

				<!-- ── ROUND BADGE ────────────────────────── -->
				<div class="round-badge">Runde <?php echo $current_game_id; ?></div>


				<!-- ── TAB NAVIGATION ─────────────────────── -->
				<nav class="tab-nav" role="tablist">
					<button class="tab-btn active" onclick="switchTab('uebersicht', this)" role="tab">Verlauf &amp; Rangliste</button>
					<button class="tab-btn" onclick="switchTab('gesamtuebersicht', this)" role="tab">Gesamtübersicht</button>
					<button class="tab-btn" onclick="switchTab('regeln', this)" role="tab">Spielregeln</button>
					<button class="tab-btn" onclick="switchTab('archiv', this)" role="tab">Archiv</button>
				</nav>
		
				<!-- /tab-verlauf-rangliste -->
				<div id="tab-uebersicht" class="tab-pane active">
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