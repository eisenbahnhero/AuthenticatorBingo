                

					<!-- Number input -->
					<div class="card">
						<div class="card-title">Nummer tippen</div>
						<form method="POST" class="mark-form">
							<input type="number" min="10" max="99" name="mark" placeholder="00" required />
							<button type="submit" class="btn-primary">Markieren</button>
						</form>
						<p class="form-hint">Dein Authenticator hat eine neue Zahl prophezeit? Trage sie ein!</p>
					</div>

					<!-- Bingo card -->
					<div class="card">
						<div class="card-title">Deine Spielkarte</div>
						<?php $cp = $current_game->get_player($current_user); ?>
						<div class="bingo-stat">
							<span class="num"><?php echo $cp["bingos"]; ?></span>
							<span class="label">BINGO<?php echo $cp["bingos"] != 1 ? 'S' : ''; ?></span>
						</div>
						<div class="bingo-grid">
							<?php
								for($i = 0; $i < 5; $i++){
									for($j = 0; $j < 5; $j++){
										$val = $cp["card"][$i][$j];
										$is_free = ($val == "FREE");
										$is_marked = $is_free || $current_game->is_marked($val);
										$cls = $is_marked ? "bingo-cell marked" : "bingo-cell";
										echo "<div class='{$cls}'>";
										if($is_marked && !$is_free){
											$who = $current_game->who_had_marked($val);
											echo "<span class='cell-tooltip'>{$who['player']}<br>" . date("d.m.Y H:i", $who["timestamp"]) . "</span>";
										} elseif($is_free){
											echo "<span class='cell-tooltip'>FREE</span>";
										}
										echo $val;
										echo "</div>";
									}
								}
							?>
						</div>
					</div>
