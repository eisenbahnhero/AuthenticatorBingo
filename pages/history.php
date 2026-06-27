

					
					<!-- Leaderboard -->
					<div class="card">
						<div class="card-title">Rangliste</div>
						<?php
							$ap = $current_game->get_all_players();
							usort($ap, function($a, $b){ return $b["bingos"] - $a["bingos"]; });

							$pos = 1;
							$max_bingos = $ap[0]["bingos"];
							for($i = 1; $i < sizeof($ap); $i++){
								if($ap[$i]["bingos"] == $max_bingos){
									$pos += 1;
								}
								else{
									break;
								}
							}

						?>
						<div class="leaderboard-list">
							<?php
								for($i = 0; $i < sizeof($ap); $i++){
									$rank = $i + 1;
									$cls = $rank <= $pos ? "lb-row rank-1" : "lb-row";
									$initials = mb_strtoupper(mb_substr($ap[$i]["player"], 0, 2));
									$medal = $rank <= $pos ? "★" : ($rank === 2 ? "2." : ($rank === 3 ? "3." : $rank . "."));
									$player_name = $ap[$i]["marked_by_himself"] == 0 ? $ap[$i]['player'] : $ap[$i]['player'] . " &nbsp;&nbsp;<span style='color: gray;font-size: 0.8em;'>(<strong>{$ap[$i]['marked_by_himself']}</strong> Nummern auf der eigenen Karte markiert)</span>";

									echo "
										<div class='{$cls}'>
											<div class='lb-rank'>{$medal}</div>
											<div class='lb-avatar'>{$initials}</div>
											<div class='lb-name'>{$player_name}</div>
											<div class='lb-score'>{$ap[$i]['bingos']} <span>Bingo</span></div>
										</div>
									";
								}
							?>
						</div>
					</div>


<?php
	// Daten vorbereiten
	$data_no_dup  = $current_game->get_how_many_numbers_marked_grouped_by_player(false);
	$data_with_dup = $current_game->get_how_many_numbers_marked_grouped_by_player(true);
 
	$total_with_dup  = $current_game->get_how_many_numbers_tipped(true);
	$total_no_dup    = $current_game->get_how_many_numbers_tipped(false);
	$avg_per_player_no_dup   = round($current_game->get_avg_marked_numbers_per_player(false), 1);
	$avg_per_player_with_dup = round($current_game->get_avg_marked_numbers_per_player(true), 1);
	$avg_per_day_no_dup      = round($current_game->get_avg_marked_numbers_per_day(false), 1);
	$avg_per_day_with_dup    = round($current_game->get_avg_marked_numbers_per_day(true), 1);
 
	// Farben dynamisch per HSL generieren – funktioniert für beliebig viele Spieler
	$num_players = max(count($data_no_dup), count($data_with_dup));
 
	function generate_hsl_colors(int $n): array {
		$fill   = [];
		$border = [];
		// Goldener-Schnitt-Offset sorgt für maximale Streuung auch bei wenigen Spielern
		$golden = 137.508;
		// Feste Startfarben: Lime & Blue des Designs zuerst
		$base_hues = [82, 217, 43, 328, 175, 271, 25, 191];
		for($i = 0; $i < $n; $i++){
			$hue = $i < count($base_hues)
				? $base_hues[$i]
				: (int)(($i * $golden) % 360);
			$fill[]   = "hsla({$hue}, 75%, 62%, 0.85)";
			$border[] = "hsl({$hue}, 75%, 62%)";
		}
		return [$fill, $border];
	}
 
	[$palette, $palette_border] = generate_hsl_colors($num_players);
 
	// JSON für Chart.js
	$labels_no_dup   = json_encode(array_keys($data_no_dup));
	$values_no_dup   = json_encode(array_values($data_no_dup));
	$labels_with_dup = json_encode(array_keys($data_with_dup));
	$values_with_dup = json_encode(array_values($data_with_dup));
 
	$colors        = json_encode($palette);
	$colors_border = json_encode($palette_border);
?>

<br>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

<!-- ── STAT CARDS ────────────────────────────────── -->
<div class="kpi-stat-grid">

	<div class="kpi-stat-card">
		<div class="kpi-stat-label">Tipps gesamt</div>
		<div class="kpi-stat-row">
			<div class="kpi-stat-block">
				<div class="kpi-stat-num"><?php echo $total_no_dup; ?></div>
				<div class="kpi-stat-sub">ohne Duplikate</div>
			</div>
			<div class="kpi-stat-divider"></div>
			<div class="kpi-stat-block">
				<div class="kpi-stat-num kpi-muted"><?php echo $total_with_dup; ?></div>
				<div class="kpi-stat-sub">mit Duplikaten</div>
			</div>
		</div>
	</div>

	<div class="kpi-stat-card">
		<div class="kpi-stat-label">Avg. Tipps pro Spieler</div>
		<div class="kpi-stat-row">
			<div class="kpi-stat-block">
				<div class="kpi-stat-num"><?php echo $avg_per_player_no_dup; ?></div>
				<div class="kpi-stat-sub">ohne Duplikate</div>
			</div>
			<div class="kpi-stat-divider"></div>
			<div class="kpi-stat-block">
				<div class="kpi-stat-num kpi-muted"><?php echo $avg_per_player_with_dup; ?></div>
				<div class="kpi-stat-sub">mit Duplikaten</div>
			</div>
		</div>
	</div>

	<div class="kpi-stat-card">
		<div class="kpi-stat-label">Avg. Tipps pro Tag</div>
		<div class="kpi-stat-row">
			<div class="kpi-stat-block">
				<div class="kpi-stat-num"><?php echo $avg_per_day_no_dup; ?></div>
				<div class="kpi-stat-sub">ohne Duplikate</div>
			</div>
			<div class="kpi-stat-divider"></div>
			<div class="kpi-stat-block">
				<div class="kpi-stat-num kpi-muted"><?php echo $avg_per_day_with_dup; ?></div>
				<div class="kpi-stat-sub">mit Duplikaten</div>
			</div>
		</div>
	</div>

</div>

<!-- ── DONUT CHARTS ──────────────────────────────── -->
<div class="kpi-charts-grid">

	<div class="card">
		<div class="card-title">Tipps pro Spieler — ohne Duplikate</div>
		<div class="kpi-chart-wrap">
			<canvas id="chartNoDup"></canvas>
		</div>
		<div class="kpi-legend" id="legendNoDup"></div>
	</div>

	<div class="card">
		<div class="card-title">Tipps pro Spieler — mit Duplikaten</div>
		<div class="kpi-chart-wrap">
			<canvas id="chartWithDup"></canvas>
		</div>
		<div class="kpi-legend" id="legendWithDup"></div>
	</div>

</div>


<!-- ── CHART INIT ─────────────────────────────────── -->
<script>
(function(){
	const COLORS        = <?php echo $colors; ?>;
	const COLORS_BORDER = <?php echo $colors_border; ?>;

	const chartDefaults = {
		type: 'doughnut',
		options: {
			cutout: '62%',
			plugins: {
				legend: { display: false },
				tooltip: {
					backgroundColor: '#1e293b',
					borderColor: '#253657',
					borderWidth: 1,
					titleColor: '#e2e8f0',
					bodyColor: '#94a3b8',
					padding: 10,
					callbacks: {
						label: ctx => '  ' + ctx.label + ': ' + ctx.parsed
					}
				}
			},
			animation: { duration: 600, easing: 'easeInOutQuart' }
		}
	};

	function buildLegend(containerId, labels, values) {
		const el = document.getElementById(containerId);
		if(!el) return;
		const total = values.reduce((a,b) => a+b, 0);
		labels.forEach((label, i) => {
			const pct = total > 0 ? Math.round(values[i] / total * 100) : 0;
			el.innerHTML += `
				<div class="kpi-legend-item">
					<div class="kpi-legend-dot" style="background:${COLORS[i]}"></div>
					<div class="kpi-legend-name">${label}</div>
					<div class="kpi-legend-val">${values[i]} <span style="color:var(--text-muted);font-size:0.72rem;font-weight:400">(${pct}%)</span></div>
				</div>`;
		});
	}

	// Chart 1: ohne Duplikate
	const labelsND  = <?php echo $labels_no_dup; ?>;
	const valuesND  = <?php echo $values_no_dup; ?>;
	new Chart(document.getElementById('chartNoDup'), {
		...chartDefaults,
		data: {
			labels: labelsND,
			datasets: [{
				data: valuesND,
				backgroundColor: COLORS.slice(0, labelsND.length),
				borderColor: COLORS_BORDER.slice(0, labelsND.length),
				borderWidth: 2,
				hoverOffset: 6
			}]
		}
	});
	buildLegend('legendNoDup', labelsND, valuesND);

	// Chart 2: mit Duplikaten
	const labelsWD  = <?php echo $labels_with_dup; ?>;
	const valuesWD  = <?php echo $values_with_dup; ?>;
	new Chart(document.getElementById('chartWithDup'), {
		...chartDefaults,
		data: {
			labels: labelsWD,
			datasets: [{
				data: valuesWD,
				backgroundColor: COLORS.slice(0, labelsWD.length),
				borderColor: COLORS_BORDER.slice(0, labelsWD.length),
				borderWidth: 2,
				hoverOffset: 6
			}]
		}
	});
	buildLegend('legendWithDup', labelsWD, valuesWD);
})();
</script>




					<!-- Number overview grid -->
					<div class="card">
						<div class="card-title">Alle Nummern (10–99)</div>
						<div class="num-overview">
							<?php
								for($n = 10; $n <= 99; $n++){
									if($current_game->is_marked($n)){
										$who = $current_game->who_had_marked($n);
										echo "<div class='num-cell marked'>";
										echo "<span class='cell-tooltip'>{$who['player']}<br>" . date("d.m.Y H:i", $who["timestamp"]) . "</span>";
									} else {
										echo "<div class='num-cell'>";
									}
									echo $n . "</div>";
								}
							?>
						</div>
					</div>

<br>

<div class="card">
    <div class="card-title">Karten der anderen Spieler</div>

    <?php
        $other_players = $current_game->get_all_players_blanko($current_user);
        if(empty($other_players)):
    ?>
        <p style="color: var(--text-muted); font-size: 0.85rem;">Noch keine anderen Spieler registriert.</p>
    <?php else: ?>

        <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
        <?php foreach($other_players as $entry): ?>
			
            <div style="flex: 0 0 auto;">
                <!-- Player header row -->
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.85rem;">
                    <div class="lb-avatar" style="width:32px;height:32px;font-size:0.7rem;">
                        <?php echo mb_strtoupper(mb_substr($entry["player"], 0, 2)); ?>
                    </div>
                    <span style="font-family:'Exo 2',sans-serif; font-weight:700; font-size:0.9rem; color:var(--text);">
                        <?php echo htmlspecialchars($entry["player"]); ?>
                    </span>
                </div>

                <!-- Mini bingo grid (marked / not marked, no numbers shown) -->
                <div class="bingo-grid" style="max-width: 200px;">
                <?php
                    for($i = 0; $i < 5; $i++){
                        for($j = 0; $j < 5; $j++){
                            $val = $entry["card"][$i][$j];
                            $is_free   = ($val === "FREE");
                            $is_marked = $is_free || !empty($val);
                            $cls = $is_marked ? "bingo-cell marked" : "bingo-cell";
                            echo "<div class='{$cls}' style='font-size:0.8rem;'>";
                            if($is_free){
                                echo "<span class='cell-tooltip'>FREE</span>★";
                            } elseif($is_marked){
                                echo "✓";
                            } else {
                                echo "–";
                            }
                            echo "</div>";
                        }
                    }
                ?>
                </div>
            </div>

        <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>



<br>
					<!-- History list -->
					<div class="card">
						<div class="card-title">Tipp-Verlauf</div>
						<table class="data-table">
							<thead>
								<tr>
									<th>#</th>
									<th>Nummer</th>
									<th>Zeitpunkt</th>
									<th>Spieler</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$mns = $current_game->get_marked_numbers();
								for($i = sizeof($mns)-1; $i >= 0; $i--){
									$add = "";
									if(isset($mns[$i]["is_already_marked"])){
										$add = " &nbsp;&nbsp;<span style='color: gray;font-size: 0.8em;'>(war bereits markiert)</span>";
									}

									echo "
										<tr>
											<td class='pos-col'>" . ($i+1) . "</td>
											<td class='num-col'>" . $mns[$i]["number"] . "</td>
											<td>" . date("d.m.Y H:i", $mns[$i]["timestamp"]) . " " . $add . "</td>
											<td>" . $mns[$i]["player"] . "</td>
										</tr>
									";
								}
							?>
							</tbody>
						</table>
					</div>
