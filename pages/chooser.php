
<div class="register-box" id="register-box">
    <div class="icon">🎯</div>
    <h2>Runde <?php echo htmlspecialchars((string)$current_game_id); ?></h2>
    <p>Jeden Monat gibt es eine neue Runde! Tritt jetzt bei!</p>

    <div class="card-type-options" style="width:100%;max-width:520px;display:flex;flex-direction:column;gap:0.6rem;text-align:left;">
        <label class="mode-label" for="opt1">
            <input type="radio" name="card_type" value="random" id="opt1" checked>
            <span class="mode-icon">🎲</span>
            <span class="mode-text">
                <strong>Zufällig generiert</strong>
                <small>24 Zahlen + FREE, frisch aus dem Rauschen des Universums</small>
            </span>
        </label>
        <label class="mode-label" for="opt2">
            <input type="radio" name="card_type" value="self-assigned" id="opt2">
            <span class="mode-icon">✏️</span>
            <span class="mode-text">
                <strong>Selbst zuweisen</strong>
                <small>Du bestimmst 24 Zahlen — Mitte ist immer FREE</small>
            </span>
        </label>
        <label class="mode-label" for="opt4">
            <input type="radio" name="card_type" value="chicken-shit" id="opt4">
            <span class="mode-icon">🐔</span>
            <span class="mode-text">
                <strong>Chickenshit-Orakel</strong>
                <small>Ein Huhn wählt deine Zahlen — auf seine ganz eigene Art</small>
            </span>
        </label>
        <label class="mode-label" for="opt5">
            <input type="radio" name="card_type" value="mouse-entropy" id="opt5">
            <span class="mode-icon">🖱️</span>
            <span class="mode-text">
                <strong>Maus-Entropie</strong>
                <small>Zeichne ein Muster — deine Bewegungen erzeugen einen einzigartigen Hash</small>
            </span>
        </label>
    </div>

    <!-- Self-assign grid (24 Felder, Mitte = FREE) -->
    <div id="self-assign-grid" style="display:none;width:100%;max-width:480px;">
        <div class="card-title" style="margin-bottom:0.75rem;">Deine 24 Zahlen (10–99, keine Duplikate) — Mitte ist FREE</div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px;" id="self-grid"></div>
        <center><p class="form-hint" style="margin-top:0.6rem;">Noch <span id="remaining">24</span> Felder offen</p></center>
    </div>

    

    <!-- Chicken arena -->
    <div id="chicken-arena" style="display:none;width:100%;max-width:560px;">
        <div class="card-title" style="margin-bottom:0.75rem;">Das Orakel arbeitet…</div>
        <div id="arena-wrap" style="position:relative;border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;background:var(--surface);padding:10px;">
            <div id="num-field" style="display:grid;grid-template-columns:repeat(10,1fr);gap:4px;"></div>
            <div id="chicken" style="position:absolute;top:0;left:0;font-size:2rem;pointer-events:none;transition:top 0.35s ease,left 0.35s ease;z-index:10;">🐔</div>
        </div>
        <center><p class="form-hint" style="margin-top:0.5rem;" id="chicken-status">Huhn wird rekrutiert…</p></center>
        <button class="btn-secondary" id="btn-restart-chicken" style="margin-top:0.75rem;display:none;" onclick="startChicken()">Nochmal 🐔</button>
    </div>

    <!-- Maus-Entropie -->
    <div id="mouse-entropy-wrap" style="display:none;width:100%;max-width:480px;">
        <div class="card-title" style="margin-bottom:0.5rem;">Zeichne ein Muster auf dem Feld</div>
        <center><p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:0.75rem;text-align:center;">
            Deine Mausbewegungen erzeugen einen einzigartigen Hash, je mehr du zeichnest, desto mehr Entropie.
        </p></center>
        <div style="position:relative;border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
            <canvas id="entropy-canvas" width="480" height="200"
                style="width:100%;height:200px;display:block;cursor:crosshair;touch-action:none;background:var(--surface);">
            </canvas>
            <div id="entropy-overlay" style="
                position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                pointer-events:none;transition:opacity 0.4s;
            ">
                <span style="font-size:0.85rem;color:var(--text-muted);background:var(--surface);padding:6px 14px;border-radius:20px;border:1px solid var(--border);">
                    🖱️ Hier zeichnen…
                </span>
            </div>
        </div>

        <!-- Entropie-Fortschrittsbalken -->
        <div style="margin-top:0.6rem;display:flex;align-items:center;gap:0.6rem;">
            <span style="font-size:0.75rem;color:var(--text-muted);white-space:nowrap;">Entropie</span>
            <div style="flex:1;height:6px;background:var(--border);border-radius:3px;overflow:hidden;">
                <div id="entropy-bar" style="height:100%;width:0%;background:var(--lime);border-radius:3px;transition:width 0.2s;"></div>
            </div>
            <span id="entropy-pct" style="font-size:0.75rem;color:var(--text-muted);min-width:32px;text-align:right;">0%</span>
        </div>

        <!-- Hash-Anzeige -->
        <div style="margin-top:0.5rem;display:flex;align-items:center;gap:0.5rem;">
            <span style="font-size:0.72rem;color:var(--text-muted);white-space:nowrap;">SHA-256</span>
            <code id="entropy-hash" style="
                font-size:0.65rem;color:var(--text-muted);font-family:monospace;
                overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;
                background:var(--bg);padding:3px 8px;border-radius:6px;border:1px solid var(--border);
            ">—</code>
            <button class="btn-secondary" id="btn-clear-entropy" onclick="clearEntropyCanvas()"
                style="font-size:0.72rem;padding:3px 10px;white-space:nowrap;">Löschen</button>
        </div>

        <!-- Vorschau-Karte -->
        <div id="entropy-preview" style="margin-top:1rem;display:none;">
            <div class="card-title" style="margin-bottom:0.5rem;">Generierte Karte</div>
            <div class="bingo-grid" id="entropy-grid"></div>
        </div>
    </div>

    <form method="POST" id="register-form" style="display:flex;flex-direction:column;align-items:center;gap:1rem;width:100%;max-width:520px;">
        <input type="hidden" name="register" />
        <input type="hidden" name="card_numbers" id="card-numbers-input" />
        <button type="submit" class="btn-primary" id="submit-btn" style="font-size:0.95rem;padding:0.85rem 2rem;" disabled>
            Jetzt teilnehmen
        </button>
        <p class="form-hint" id="submit-hint">Karte wird noch generiert…</p>
    </form>
</div>

<style>
.mode-label {
    display: flex; align-items: flex-start; gap: 0.85rem;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 0.85rem 1rem;
    cursor: pointer; transition: border-color 0.2s, background 0.2s;
}
.mode-label:hover { border-color: var(--border2); background: var(--surface2); }
.mode-label input[type="radio"] { margin-top: 3px; accent-color: var(--lime); flex-shrink: 0; }
.mode-label input[type="radio"]:checked ~ .mode-text strong { color: var(--lime-text); }
.mode-label:has(input:checked) { border-color: rgba(163,230,53,0.35); background: rgba(163,230,53,0.04); }
.mode-icon { font-size: 1.3rem; flex-shrink: 0; }
.mode-text { display: flex; flex-direction: column; gap: 2px; }
.mode-text strong { font-family: 'Exo 2', sans-serif; font-weight: 700; font-size: 0.9rem; color: var(--text); }
.mode-text small { font-size: 0.78rem; color: var(--text-muted); line-height: 1.4; }

.self-input {
    width: 100%; aspect-ratio: 1; text-align: center;
    background: var(--bg); border: 1px solid var(--border2);
    border-radius: 8px; color: var(--text);
    font-family: 'Exo 2', sans-serif; font-size: 0.95rem; font-weight: 700;
    outline: none; transition: border-color 0.2s, box-shadow 0.2s;
    -moz-appearance: textfield;
}
.self-input::-webkit-inner-spin-button, .self-input::-webkit-outer-spin-button { -webkit-appearance: none; }
.self-input:focus { border-color: var(--lime); box-shadow: 0 0 0 3px var(--lime-glow); }
.self-input.error { border-color: #ef4444; box-shadow: 0 0 0 2px rgba(239,68,68,0.2); }
.self-input.ok { border-color: var(--lime-dim); }
.self-free {
    width: 100%; aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
    background: rgba(163,230,53,0.08); border: 1px solid rgba(163,230,53,0.3);
    border-radius: 8px; font-family: 'Exo 2', sans-serif;
    font-size: 0.7rem; font-weight: 700; color: var(--lime-text); letter-spacing: 0.05em;
}

.bingo-cell.free-cell {
    background: rgba(163,230,53,0.1) !important;
    border-color: rgba(163,230,53,0.35) !important;
    color: var(--lime-text) !important;
    font-size: 0.72rem !important;
    font-weight: 700;
    letter-spacing: 0.05em;
}

.num-field-cell {
    aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 6px; font-family: 'Exo 2', sans-serif;
    font-size: 0.72rem; font-weight: 600; color: var(--text-muted);
    transition: all 0.3s ease;
}
.num-field-cell.pooped {
    background: rgba(163,230,53,0.15); border-color: var(--lime-dim); color: var(--lime-text);
}
</style>

<script>
(function () {
    
    let finalNumbers = null;
    const FREE_INDEX = 12; // card[2][2], nullbasiert in einem 25er-Array

    // ── helpers ──────────────────────────────────────
    function shuffleArr(arr, seed) {
        const a = [...arr];
        if (seed === undefined) {
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
            return a;
        }
        let s = seed >>> 0;
        function rand() {
            s = (Math.imul(s, 1664525) + 1013904223) >>> 0;
            return s / 0x100000000;
        }
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(rand() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function fullPool() { return Array.from({length: 90}, (_, i) => i + 10); }

    function withFree(nums24) {
        const result = [];
        let ni = 0;
        for (let i = 0; i < 25; i++) {
            result.push(i === FREE_INDEX ? null : nums24[ni++]);
        }
        return result;
    }

    function setReady(nums25) {
        finalNumbers = nums25;
        document.getElementById('card-numbers-input').value = JSON.stringify(nums25);
        document.getElementById('submit-btn').disabled = false;
        document.getElementById('submit-hint').textContent = '24 Zahlen + FREE bereit ✓';
        document.getElementById('submit-hint').style.color = 'var(--lime-text)';
    }
    function setNotReady(msg = 'Karte wird noch generiert…') {
        finalNumbers = null;
        document.getElementById('card-numbers-input').value = '';
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('submit-hint').textContent = msg;
        document.getElementById('submit-hint').style.color = '';
    }

    // ── mode switch ───────────────────────────────────
    function hideExtras() {
        ['self-assign-grid','chicken-arena','mouse-entropy-wrap'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
    }

    document.querySelectorAll('input[name="card_type"]').forEach(r => {
        r.addEventListener('change', onModeChange);
    });

    function onModeChange() {
        const mode = document.querySelector('input[name="card_type"]:checked').value;
        hideExtras();
        setNotReady();

        if (mode === 'random') {
            const nums24 = shuffleArr(fullPool()).slice(0, 24);
            setReady(withFree(nums24));
        } else if (mode === 'self-assigned') {
            document.getElementById('self-assign-grid').style.display = 'block';
            buildSelfGrid();
        } else if (mode === 'chicken-shit') {
            document.getElementById('chicken-arena').style.display = 'block';
            startChicken();
        } else if (mode === 'mouse-entropy') {
            document.getElementById('mouse-entropy-wrap').style.display = 'block';
            initEntropyCanvas();
        }
    }

    // ── self-assign (24 Felder + FREE-Feld) ──────────
    function buildSelfGrid() {
        const grid = document.getElementById('self-grid');
        grid.innerHTML = '';
        for (let i = 0; i < 25; i++) {
            if (i === FREE_INDEX) {
                const free = document.createElement('div');
                free.className = 'self-free';
                free.textContent = 'FREE';
                grid.appendChild(free);
                continue;
            }
            const inp = document.createElement('input');
            inp.type = 'number';
            inp.min = 10; inp.max = 99;
            inp.className = 'self-input';
            inp.placeholder = '--';
            inp.dataset.slot = i;
            inp.addEventListener('input', validateSelf);
            grid.appendChild(inp);
        }
    }

    function validateSelf() {
        const inputs = [...document.querySelectorAll('.self-input')];
        const vals = inputs.map(i => parseInt(i.value));
        const seen = new Set();
        let ok = 0;
        inputs.forEach((inp, idx) => {
            const v = vals[idx];
            if (!inp.value || isNaN(v) || v < 10 || v > 99) {
                inp.className = 'self-input';
            } else if (seen.has(v)) {
                inp.className = 'self-input error';
            } else {
                seen.add(v);
                inp.className = 'self-input ok';
                ok++;
            }
        });
        document.getElementById('remaining').textContent = 24 - ok;
        if (ok === 24) {
            const nums24 = vals;
            setReady(withFree(nums24));
        } else {
            setNotReady('Noch ' + (24 - ok) + ' Felder offen');
        }
    }

    // ── chicken-shit ──────────────────────────────────
    let chickenInterval = null;

    window.startChicken = function() {
        if (chickenInterval) clearInterval(chickenInterval);
        setNotReady('Huhn läuft…');
        document.getElementById('btn-restart-chicken').style.display = 'none';
        document.getElementById('chicken-status').textContent = 'Das Huhn überlegt seine Route…';

        const field = document.getElementById('num-field');
        field.innerHTML = '';
        const nums = shuffleArr(fullPool());
        const numEls = {};
        nums.forEach(n => {
            const cell = document.createElement('div');
            cell.className = 'num-field-cell';
            cell.textContent = n;
            cell.dataset.num = n;
            field.appendChild(cell);
            numEls[n] = cell;
        });

        const chicken = document.getElementById('chicken');
        const wrap = document.getElementById('arena-wrap');
        chicken.style.transition = 'none';
        chicken.style.left = '0px';
        chicken.style.top = '0px';

        const chosen24 = shuffleArr(fullPool()).slice(0, 24);
        const poopQueue = [...chosen24];
        let pooped = 0;

        function getCellCenter(el) {
            const wRect = wrap.getBoundingClientRect();
            const eRect = el.getBoundingClientRect();
            return {
                x: eRect.left - wRect.left + eRect.width / 2 - 16,
                y: eRect.top - wRect.top + eRect.height / 2 - 16
            };
        }

        setTimeout(() => {
            chicken.style.transition = 'top 0.4s ease, left 0.4s ease';
            chickenInterval = setInterval(() => {
                if (poopQueue.length === 0) {
                    clearInterval(chickenInterval);
                    document.getElementById('chicken-status').textContent =
                        '🐔 Das Orakel hat gesprochen. 24 Zahlen auserwählt + FREE in der Mitte.';
                    document.getElementById('btn-restart-chicken').style.display = 'inline-block';
                    setReady(withFree(chosen24));
                    return;
                }
                const nextNum = poopQueue.shift();
                const el = numEls[nextNum];
                if (!el) return;
                const pos = getCellCenter(el);
                chicken.style.left = pos.x + 'px';
                chicken.style.top  = pos.y + 'px';
                pooped++;
                document.getElementById('chicken-status').textContent =
                    `🐔 ${pooped}/24 — Huhn inspiziert Feld ${nextNum}…`;
                setTimeout(() => {
                    el.classList.add('pooped');
                    el.innerHTML = nextNum + '<span style="font-size:0.55rem;display:block;line-height:1">💩</span>';
                }, 300);
            }, 520);
        }, 300);
    };

    // ── Maus-Entropie ─────────────────────────────────
    // Sammelt Mausbewegungen, hasht sie mit SHA-256 und generiert daraus Zahlen.
    let entropyPoints = [];
    let entropyIsDrawing = false;
    let entropyInitDone = false;
    const ENTROPY_GOAL = 200; // Punkte bis "voll"

    function initEntropyCanvas() {
        if (entropyInitDone) {
            // Canvas bereits initialisiert, nur zurücksetzen falls leer
            updateEntropyUI();
            return;
        }
        entropyInitDone = true;

        const canvas = document.getElementById('entropy-canvas');
        const ctx = canvas.getContext('2d');

        // Canvas-Auflösung an echte CSS-Größe anpassen
        function resizeCanvas() {
            const rect = canvas.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;
            canvas.width  = rect.width  * dpr;
            canvas.height = rect.height * dpr;
            ctx.scale(dpr, dpr);
            redrawStrokes();
        }

        let strokes = []; // [{x,y,drawing}]
        let allRawPoints = []; // für Entropie: alle Positionen

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            if (e.touches) {
                return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
            }
            return { x: e.clientX - rect.left, y: e.clientY - rect.top };
        }

        function redrawStrokes() {
            const dpr = window.devicePixelRatio || 1;
            ctx.clearRect(0, 0, canvas.width / dpr, canvas.height / dpr);
            if (strokes.length < 2) return;

            ctx.strokeStyle = 'white';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            ctx.beginPath();
            for (let i = 0; i < strokes.length; i++) {
                const pt = strokes[i];
                if (!pt.drawing || i === 0) ctx.moveTo(pt.x, pt.y);
                else ctx.lineTo(pt.x, pt.y);
            }
            ctx.stroke();
        }

        function onMove(e) {
            if (!entropyIsDrawing) return;
            e.preventDefault();
            const pos = getPos(e);
            strokes.push({ x: pos.x, y: pos.y, drawing: true });
            allRawPoints.push(pos.x, pos.y);
            entropyPoints = allRawPoints;
            redrawStrokes();
            updateEntropyUI();
        }

        function onStart(e) {
            e.preventDefault();
            entropyIsDrawing = true;
            const pos = getPos(e);
            strokes.push({ x: pos.x, y: pos.y, drawing: false });
            document.getElementById('entropy-overlay').style.opacity = '0';
        }

        function onEnd() { entropyIsDrawing = false; }

        canvas.addEventListener('mousedown',  onStart, { passive: false });
        canvas.addEventListener('mousemove',  onMove,  { passive: false });
        canvas.addEventListener('mouseup',    onEnd);
        canvas.addEventListener('mouseleave', onEnd);
        canvas.addEventListener('touchstart', onStart, { passive: false });
        canvas.addEventListener('touchmove',  onMove,  { passive: false });
        canvas.addEventListener('touchend',   onEnd);

        window.clearEntropyCanvas = function() {
            strokes = [];
            allRawPoints = [];
            entropyPoints = [];
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById('entropy-overlay').style.opacity = '1';
            document.getElementById('entropy-hash').textContent = '—';
            document.getElementById('entropy-bar').style.width = '0%';
            document.getElementById('entropy-pct').textContent = '0%';
            document.getElementById('entropy-preview').style.display = 'none';
            setNotReady('Zeichne ein Muster…');
        };

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
    }

    async function updateEntropyUI() {
        const n = entropyPoints.length / 2; // Anzahl Punkte
        const pct = Math.min(100, Math.round((n / ENTROPY_GOAL) * 100));
        document.getElementById('entropy-bar').style.width = pct + '%';
        document.getElementById('entropy-pct').textContent = pct + '%';

        if (n < 5) {
            setNotReady('Zeichne weiter…');
            document.getElementById('entropy-hash').textContent = '—';
            document.getElementById('entropy-preview').style.display = 'none';
            return;
        }

        // SHA-256 berechnen
        const hashHex = await computeSHA256(entropyPoints);
        document.getElementById('entropy-hash').textContent = hashHex;

        // Zahlen aus Hash ableiten
        const nums24 = numsFromHash(hashHex);
        const nums25 = withFree(nums24);

        // Vorschau-Karte anzeigen
        document.getElementById('entropy-preview').style.display = 'block';
        const grid = document.getElementById('entropy-grid');
        grid.innerHTML = '';
        nums25.forEach(n => {
            const cell = document.createElement('div');
            if (n === null) {
                cell.className = 'bingo-cell free-cell';
                cell.textContent = 'FREE';
            } else {
                cell.className = 'bingo-cell';
                cell.textContent = n;
            }
            grid.appendChild(cell);
        });

        if (pct >= 20) {
            setReady(nums25);
        } else {
            setNotReady('Noch etwas mehr zeichnen… (' + pct + '%)');
        }
    }

    // SHA-256 via Web Crypto API
    async function computeSHA256(data) {
        const str = data.map(v => Math.round(v * 10)).join(',');
        const buf = new TextEncoder().encode(str);
        const hashBuf = await crypto.subtle.digest('SHA-256', buf);
        return Array.from(new Uint8Array(hashBuf))
            .map(b => b.toString(16).padStart(2, '0'))
            .join('');
    }

    // 24 eindeutige Zahlen (10–99) aus einem Hex-Hash deterministisch ableiten
    // Methode: Hash-Bytes als Seed für Fisher-Yates auf fullPool()
    function numsFromHash(hex) {
        // Seed aus den ersten 8 Bytes des Hashes
        const seed = parseInt(hex.slice(0, 8), 16);
        return shuffleArr(fullPool(), seed).slice(0, 24);
    }

    onModeChange();
})();
</script>