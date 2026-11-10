<?php
/**
 * Nucleus — project card preview (design sandbox).
 *
 * Deliberately standalone: its CSS is scoped to .n2 and defines its own token
 * values, so it cannot affect the live dashboard shell. Open it at
 * /ui-preview.php and judge the cards; nothing else changes.
 */
$projects = [
    [
        'id' => 'SER-830', 'name' => 'almarmouq3', 'handle' => 'almarmouq3.local',
        'mode' => 'AUTO', 'state' => 'active', 'state_label' => 'ACTIVE',
        'desc' => 'Arabic news CMS, Laravel 11 · PHP 8.3 · MySQL 8',
        'stack' => ['Laravel', 'Vite', 'MySQL', 'SSL'], 'extra' => 2,
        'stats' => [['SERVICES', '4/4'], ['DB SIZE', '182 MB'], ['PORTS', '80, 443']],
        'bar' => ['Disk 61%', 61], 'foot' => '/var/www/html/almarmouq3', 'icon' => 'solar:global-bold',
    ],
    [
        'id' => 'SER-825', 'name' => '2ti-orchestrator', 'handle' => '2ti-orchestrator.local',
        'mode' => 'AUTO', 'state' => 'warn', 'state_label' => 'DEGRADED',
        'desc' => 'Production orchestration, Laravel 11 · PHP 8.4 · Redis · MariaDB',
        'stack' => ['Laravel', 'Redis', 'MariaDB'], 'extra' => 0,
        'stats' => [['SERVICES', '3/4'], ['DB SIZE', '1.2 GB'], ['QUEUE', '12']],
        'bar' => ['Disk 88%', 88], 'foot' => '/var/www/html/2ti-orchestrator', 'icon' => 'solar:settings-bold',
    ],
    [
        'id' => 'SER-829', 'name' => 'cresta', 'handle' => 'cresta.local',
        'mode' => 'MANUAL', 'state' => 'idle', 'state_label' => 'STOPPED',
        'desc' => 'Tauri + Vite desktop app — static build only',
        'stack' => ['Tauri', 'Vite'], 'extra' => 0,
        'stats' => [['SERVICES', '0/2'], ['DB SIZE', '—'], ['PORTS', '—']],
        'bar' => ['Disk 12%', 12], 'foot' => '/var/www/html/cresta', 'icon' => 'solar:widget-2-bold',
    ],
    [
        'id' => 'SER-831', 'name' => '2ti-theme', 'handle' => '2ti-theme.local',
        'mode' => 'AUTO', 'state' => 'active', 'state_label' => 'ACTIVE',
        'desc' => 'Design tokens and components, static HTML · no build',
        'stack' => ['Static', 'CSS'], 'extra' => 0,
        'stats' => [['SERVICES', '1/1'], ['DB SIZE', '—'], ['PORTS', '80']],
        'bar' => ['Disk 2%', 2], 'foot' => '/var/www/html/2ti-theme', 'icon' => 'solar:palette-bold',
    ],
    [
        'id' => 'SER-832', 'name' => 'CedarCards', 'handle' => 'cedarcards.local',
        'mode' => 'AUTO', 'state' => 'err', 'state_label' => 'FAILED',
        'desc' => 'Card game, Tauri · Rust engine · SQLite — service failed to start',
        'stack' => ['Tauri', 'Rust', 'SQLite', 'SSL', 'Git'], 'extra' => 3,
        'stats' => [['SERVICES', '0/1'], ['DB SIZE', '4 MB'], ['PORTS', '—']],
        'bar' => ['Disk 9%', 9], 'foot' => '/var/www/html/CedarCards', 'icon' => 'solar:gamepad-bold',
    ],
    [
        'id' => 'SER-833', 'name' => 'audiencepulse', 'handle' => 'audiencepulse.local',
        'mode' => 'MANUAL', 'state' => 'active', 'state_label' => 'ACTIVE',
        'desc' => 'Analytics dashboard, Node 22 · Postgres · Redis',
        'stack' => ['Node', 'Postgres', 'Redis'], 'extra' => 0,
        'stats' => [['SERVICES', '3/3'], ['DB SIZE', '640 MB'], ['PORTS', '3000']],
        'bar' => ['Disk 44%', 44], 'foot' => '/var/www/html/audiencepulse', 'icon' => 'solar:chart-2-bold',
    ],
];
?>
<!doctype html>
<html lang="en" dir="ltr" data-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nucleus — project card preview</title>
<script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
<style>
/* Scoped to .n2 so this page cannot leak into the dashboard shell. */
.n2 {
  --brand:#0d1b2a; --brand-accent:#c5a059;
  --bg:#0e131a; --surface:#161d26; --surface-2:#1d2630;
  --border:#2a3644; --border-strong:#3a4a5c;
  --text:#e8edf3; --text-muted:#a2adba; --text-faint:#71808f;
  --success:#2fbf71; --warning:#e0a33e; --danger:#e05a4f; --info:#4f9cf9;
  --r-1:6px; --r-2:10px; --r-3:14px; --r-full:999px;
  --s-1:4px; --s-2:8px; --s-3:12px; --s-4:16px; --s-5:24px;
  --fs-xs:12px; --fs-sm:13px; --fs-md:14px; --fs-xl:20px; --fs-2xl:26px;
  --shadow-1:0 1px 2px rgba(0,0,0,.4); --shadow-2:0 8px 24px rgba(0,0,0,.45);
  background:var(--bg); color:var(--text); min-height:100vh; margin:0;
  font:var(--fs-md)/1.55 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Noto Sans Arabic",sans-serif;
  -webkit-font-smoothing:antialiased;
}
.n2 *,.n2 *::before,.n2 *::after{box-sizing:border-box}
.n2 .wrap{max-width:1280px;margin:0 auto;padding:var(--s-5)}
.n2 .head{display:flex;align-items:baseline;justify-content:space-between;gap:var(--s-4);margin-bottom:var(--s-5);flex-wrap:wrap}
.n2 h1{font-size:var(--fs-2xl);margin:0;letter-spacing:-.02em}
.n2 .sub{color:var(--text-muted);font-size:var(--fs-sm);margin-top:var(--s-1)}
.n2 .count{color:var(--text-faint);font-size:var(--fs-xs);letter-spacing:.08em;text-transform:uppercase}

/* The grid: content-driven heights. No 1fr rows, no fixed card height. */
.n2 .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(330px,1fr));gap:var(--s-4);align-items:start}

.n2 .card{
  background:var(--surface);border:1px solid var(--border);border-radius:var(--r-3);
  padding:var(--s-4) var(--s-4) var(--s-3);box-shadow:var(--shadow-1);
  display:flex;flex-direction:column;gap:var(--s-3);transition:border-color .15s,box-shadow .15s;
}
.n2 .card:hover{border-color:var(--border-strong);box-shadow:var(--shadow-2)}
.n2 .card-top{display:flex;align-items:center;justify-content:space-between;gap:var(--s-2)}
.n2 .slug{font-size:var(--fs-xs);color:var(--text-faint);letter-spacing:.09em;font-variant-numeric:tabular-nums}
.n2 .tags{display:flex;gap:6px;flex-wrap:wrap}
.n2 .tag{font-size:11px;letter-spacing:.07em;padding:2px 8px;border-radius:var(--r-1);border:1px solid var(--border-strong);color:var(--text-muted)}
.n2 .pill{font-size:11px;font-weight:600;letter-spacing:.06em;padding:2px 9px;border-radius:var(--r-full);white-space:nowrap}
.n2 .pill--ok{background:color-mix(in srgb,var(--success) 16%,var(--surface));color:var(--success)}
.n2 .pill--warn{background:color-mix(in srgb,var(--warning) 16%,var(--surface));color:var(--warning)}
.n2 .pill--err{background:color-mix(in srgb,var(--danger) 16%,var(--surface));color:var(--danger)}
.n2 .pill--idle{background:var(--surface-2);color:var(--text-muted)}

.n2 .title-row{display:flex;gap:var(--s-3);align-items:center;min-width:0}
.n2 .logo{width:40px;height:40px;flex:0 0 40px;border-radius:var(--r-2);background:var(--surface-2);
  display:grid;place-items:center;color:var(--text-muted);font-size:20px;border:1px solid var(--border)}
.n2 .name{font-size:16px;font-weight:700;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.n2 .handle{font-size:var(--fs-xs);color:var(--text-muted);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.n2 .desc{color:var(--text-muted);font-size:var(--fs-sm);margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}

.n2 .chips{display:flex;gap:6px;flex-wrap:wrap}
.n2 .chip{font-size:11px;padding:3px 9px;border-radius:var(--r-full);background:var(--surface-2);color:var(--text-muted);border:1px solid var(--border)}
.n2 .chip.more{background:transparent;border-style:dashed}

.n2 .stats{display:grid;grid-template-columns:repeat(3,1fr);gap:var(--s-2);margin-top:auto}
.n2 .stat{background:var(--surface-2);border:1px solid var(--border);border-radius:var(--r-2);padding:var(--s-2) var(--s-3)}
.n2 .stat .k{font-size:10px;letter-spacing:.08em;color:var(--text-faint);text-transform:uppercase}
.n2 .stat .v{font-size:18px;font-weight:700;font-variant-numeric:tabular-nums;margin-top:2px}

.n2 .bar-row{display:flex;justify-content:space-between;font-size:11px;color:var(--text-faint);margin-bottom:5px}
.n2 .bar{height:5px;border-radius:var(--r-full);background:var(--surface-2);overflow:hidden}
.n2 .bar>i{display:block;height:100%;border-radius:var(--r-full);background:var(--success)}
.n2 .card[data-state="warn"] .bar>i{background:var(--warning)}
.n2 .card[data-state="err"] .bar>i{background:var(--danger)}
.n2 .card[data-state="idle"] .bar>i{background:var(--border-strong)}

.n2 .foot{display:flex;align-items:center;justify-content:space-between;gap:var(--s-2);
  padding-top:var(--s-2);border-top:1px solid var(--border);font-size:11px;color:var(--text-faint)}
.n2 .foot code{font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:60%}
.n2 .open{color:var(--text);text-decoration:none;display:inline-flex;align-items:center;gap:4px;white-space:nowrap}
.n2 .open:hover{color:var(--brand-accent)}
.n2 .note{color:var(--text-faint);font-size:var(--fs-xs);margin-top:var(--s-5)}
@media (max-width:600px){ .n2 .grid{grid-template-columns:1fr} }
</style>
</head>
<body class="n2">
<div class="wrap">
  <div class="head">
    <div>
      <h1>Projects</h1>
      <div class="sub">Card preview — content-driven heights, no forced equal rows</div>
    </div>
    <div class="count"><?php echo count($projects); ?> projects</div>
  </div>

  <div class="grid">
    <?php foreach ($projects as $p):
        $pill = ['active' => 'pill--ok', 'warn' => 'pill--warn', 'err' => 'pill--err', 'idle' => 'pill--idle'][$p['state']];
    ?>
    <article class="card" data-state="<?php echo htmlspecialchars($p['state']); ?>">
      <div class="card-top">
        <span class="slug"><?php echo htmlspecialchars($p['id']); ?></span>
        <div class="tags">
          <span class="tag"><?php echo htmlspecialchars($p['mode']); ?></span>
          <span class="pill <?php echo $pill; ?>"><?php echo htmlspecialchars($p['state_label']); ?></span>
        </div>
      </div>

      <div class="title-row">
        <div class="logo"><iconify-icon icon="<?php echo htmlspecialchars($p['icon']); ?>"></iconify-icon></div>
        <div style="min-width:0">
          <h2 class="name"><?php echo htmlspecialchars($p['name']); ?></h2>
          <div class="handle"><?php echo htmlspecialchars($p['handle']); ?></div>
        </div>
      </div>

      <p class="desc"><?php echo htmlspecialchars($p['desc']); ?></p>

      <div class="chips">
        <?php foreach ($p['stack'] as $s): ?>
          <span class="chip"><?php echo htmlspecialchars($s); ?></span>
        <?php endforeach; ?>
        <?php if ($p['extra'] > 0): ?><span class="chip more">+<?php echo (int)$p['extra']; ?></span><?php endif; ?>
      </div>

      <div class="stats">
        <?php foreach ($p['stats'] as $s): ?>
          <div class="stat"><div class="k"><?php echo htmlspecialchars($s[0]); ?></div><div class="v"><?php echo htmlspecialchars($s[1]); ?></div></div>
        <?php endforeach; ?>
      </div>

      <div>
        <div class="bar-row"><span><?php echo htmlspecialchars($p['bar'][0]); ?></span><span><?php echo (int)$p['bar'][1]; ?>%</span></div>
        <div class="bar"><i style="width:<?php echo (int)$p['bar'][1]; ?>%"></i></div>
      </div>

      <div class="foot">
        <code><?php echo htmlspecialchars($p['foot']); ?></code>
        <a class="open" href="https://<?php echo htmlspecialchars($p['handle']); ?>" target="_blank" rel="noopener">
          Open <iconify-icon icon="solar:arrow-right-up-bold"></iconify-icon>
        </a>
      </div>
    </article>
    <?php endforeach; ?>
  </div>

  <p class="note">Sandbox page — scoped styles, no effect on the dashboard shell. Delete when the design lands.</p>
</div>
</body>
</html>
