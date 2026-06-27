<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hallo!</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: #000;
      width: 100vw;
      height: 100vh;
      overflow: hidden;
    }

    #box {
      position: absolute;
      font-family: Arial, sans-serif;
      font-size: 28px;
      font-weight: bold;
      white-space: nowrap;
      padding: 14px 24px;
      border-radius: 6px;
      border: 3px solid;
      letter-spacing: 0.5px;
      pointer-events: none;
      user-select: none;
    }
  </style>
</head>
<body>

  <div id="box">Hallo <?php echo ($current_user == NULL ? '' : $current_user);?>!</div>

  <script>
    const box = document.getElementById('box');

    const colors = [
      '#ff4444',
      '#44aaff',
      '#ffcc00',
      '#44ff88',
      '#ff44cc',
      '#ff8844',
      '#44ffee',
    ];

    let colorIndex = 0;
    let vx = 2.2, vy = 1.7;
    let x = 60, y = 80;

    function applyColor(i) {
      const c = colors[i % colors.length];
      box.style.color = c;
      box.style.borderColor = c;
    }

    applyColor(colorIndex);

    function animate() {
      const cw = window.innerWidth;
      const ch = window.innerHeight;
      const bw = box.offsetWidth;
      const bh = box.offsetHeight;

      x += vx;
      y += vy;

      let bounced = false;

      if (x <= 0) {
        x = 0;
        vx = Math.abs(vx);
        bounced = true;
      } else if (x + bw >= cw) {
        x = cw - bw;
        vx = -Math.abs(vx);
        bounced = true;
      }

      if (y <= 0) {
        y = 0;
        vy = Math.abs(vy);
        bounced = true;
      } else if (y + bh >= ch) {
        y = ch - bh;
        vy = -Math.abs(vy);
        bounced = true;
      }

      if (bounced) {
        colorIndex = (colorIndex + 1) % colors.length;
        applyColor(colorIndex);
      }

      box.style.left = x + 'px';
      box.style.top = y + 'px';

      requestAnimationFrame(animate);
    }

    animate();
  </script>

</body>
</html>