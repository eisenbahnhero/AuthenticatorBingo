<?php
    // Alle Errungenschaften einmalig auswerten (vermeidet doppelte update()-Aufrufe)
    $achievements_data = [];
    foreach ($available_achievements as $curr) {
        if ($curr->isElementAvailable()) {
            $achievement = $curr->getElement();
            if ($achievement === null) continue;

            $achievements_data[] = [
                'title'        => $achievement['title'],
                'description'  => $achievement['description'],
                'img'          => $achievement['img'],
                'module'       => (string) get_class($curr),
                'level'        => (int) $achievement['level'],
                'total_levels' => (int) $curr->getNumberOfPossibleLevels(),
                'name'         => (string) $curr->getAchievementName()
            ];
        }
    }

    $total_types    = count($available_achievements);
    $unlocked_count = count($achievements_data);
    $progress_pct   = $total_types > 0 ? round(($unlocked_count / $total_types) * 100) : 0;
?>

<div class="card">
    <div class="card-title">Hinweise</div>
    <p style="color:var(--text-muted);font-size:0.85rem;line-height:1.8;">
        Achievements sind persönliche Errungenschaften, die dir für bestimmte Leistungen verliehen werden. Jede Errungenschaft kann mehrere Level haben, die du nach und nach erreichen kannst. Spiel einfach fleißig weiter &ndash; deine Fortschritte werden automatisch erfasst.
    </p>
</div>

<?php if ($unlocked_count > 0): ?>

    <div class="card">
        <div class="card-title">Fortschritt</div>
        <div class="achievement-progress-row">
            <div class="achievement-progress-num"><?php echo $unlocked_count; ?><span> / <?php echo $total_types; ?></span></div>
            <div class="achievement-progress-main">
                <div class="achievement-progress-label">Errungenschaften freigeschaltet</div>
                <div class="achievement-progress-track">
                    <div class="achievement-progress-fill" style="width: <?php echo $progress_pct; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Errungenschaften</div>

        <div class="achievement-grid">
        <?php foreach ($achievements_data as $achievement):
            $name        = htmlspecialchars($achievement['name'], ENT_QUOTES, 'UTF-8');
                $title       = htmlspecialchars($achievement['title'], ENT_QUOTES, 'UTF-8');
                $description = htmlspecialchars($achievement['description'], ENT_QUOTES, 'UTF-8');
                $module      = htmlspecialchars($achievement['module'], ENT_QUOTES, 'UTF-8');
                $image       = htmlspecialchars($achievement['img'], ENT_QUOTES, 'UTF-8');
                $level       = $achievement['level'];

                $total_levels = max($achievement['total_levels'], 1);
                $completed_class = $level >= $total_levels ? ' achievement-complete' : '';
        ?>
            <article class="achievement-tile<?php echo $completed_class; ?>">
                <div class="achievement-image-wrap">
                    <img class="achievement-image" src="src/achievements/<?php echo $module; ?>/<?php echo $image; ?>" alt="<?php echo $title; ?>">
                </div>
                <div class="achievement-content">
                    <div class="achievement-name"><?php echo $name; ?></div>
                    <h3 class="achievement-title"><?php echo $title; ?></h3>
                    <div class="achievement-level">Level <?php echo $level; ?> / <?php echo $total_levels; ?></div>
                    <p class="achievement-description"><?php echo $description; ?></p>
                    <div class="achievement-dots">
                        <?php for ($i = 1; $i <= $total_levels; $i++): ?>
                            <span class="achievement-dot<?php echo $i <= $level ? ' filled' : ''; ?>"></span>
                        <?php endfor; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
        </div>
    </div>

<?php else: ?>

    <div class="card">
        <div class="achievement-empty">
            <div class="icon">🏆</div>
            <h3>Noch keine Errungenschaften freigeschaltet</h3>
            <p>
                Hier erscheinen deine Errungenschaften, sobald du sie erspielt hast. Mach ein paar Kreuze auf deiner Bingokarte &ndash; der erste Titel ist meist näher, als man denkt.
            </p>
        </div>
    </div>

<?php endif; ?>