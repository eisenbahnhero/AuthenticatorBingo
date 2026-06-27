<div class="card">
	<div class="card-title">Gesamtübersicht — alle Runden</div>
	<p style="color:var(--text-muted);font-size:0.85rem;line-height:1.8;margin-bottom:1.2rem;">
		Hier werden die Bingos aller bisherigen Runden zusammenaddiert. Wer hat über die Zeit die meisten Bingos erzielt? Wer ist der unbeständige Aufsteiger, wer der ewige Zweite, wer der Glückspilz, wer ist der wahre King?
	</p>
	<?php
		$ap = get_overall_statistics($archiv);
		usort($ap, function($a, $b){ return $b["bingos"] - $a["bingos"]; });
		$pos = 1;
		$max_bingos = $ap[0]["bingos"];
		for($i = 1; $i < sizeof($ap); $i++){
			if($ap[$i]["bingos"] == $max_bingos){ $pos += 1; }
			else { break; }
		}
	?>
	<div class="leaderboard-list">
		<?php
			for($i = 0; $i < sizeof($ap); $i++){
				$rank   = $i + 1;
				$cls    = $rank <= $pos ? "lb-row rank-1" : "lb-row";
				$initials = mb_strtoupper(mb_substr($ap[$i]["player"], 0, 2));
				$medal  = $rank <= $pos ? "★" : ($rank === 2 ? "2." : ($rank === 3 ? "3." : $rank . "."));
				echo "
					<div class='{$cls}'>
						<div class='lb-rank'>{$medal}</div>
						<div class='lb-avatar'>{$initials}</div>
						<div class='lb-name'>{$ap[$i]['player']}</div>
						<div class='lb-score'>{$ap[$i]['bingos']} <span>Bingo</span></div>
					</div>
				";
			}
		?>
	</div>
</div>