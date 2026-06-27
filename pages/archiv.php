<div class="card">
						<div class="card-title">Archiv</div>
						<p style="color:var(--text-muted);font-size:0.85rem;line-height:1.8;">
							Hier werden die Ergebnisse aller vorherigen Runden angezeigt. Viel Erfolg beim Aufholen!
						</p>
					</div>

					<?php
						$archiv = get_all_games();
						usort($archiv, function($a, $b){
							return strcmp($b["id"], $a["id"]);
						});
						foreach($archiv as $curr){
							if($curr["id"] == $current_game_id) continue;
					?>
						<div class="card">
							<div class="card-title">Runde <?php echo $curr["id"];?></div>
							<?php
								$ap = $curr["game"]->get_all_players();
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
						}
					?>