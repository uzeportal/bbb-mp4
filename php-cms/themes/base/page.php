<?php
/** Basit varsayılan tema */
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($meta['title']); ?></title>
    <?php if (!empty($meta['description'])): ?><meta name="description" content="<?php echo htmlspecialchars($meta['description']); ?>"><?php endif; ?>
    <?php if (!empty($meta['keywords'])): ?><meta name="keywords" content="<?php echo htmlspecialchars($meta['keywords']); ?>"><?php endif; ?>
    <?php if (!empty($settings['base_url'])): ?><link rel="canonical" href="<?php echo htmlspecialchars(rtrim($settings['base_url'], '/') . '/index.php?sayfa=' . urlencode($page['slug'])); ?>"><?php endif; ?>
    <style>
        :root { --accent: #2563eb; }
        body { margin:0; font-family: 'Segoe UI', sans-serif; color:#111827; background:#f7f8fb; }
        header { background:#0f172a; color:white; position:sticky; top:0; z-index:10; box-shadow:0 2px 6px rgba(0,0,0,.2); }
        nav { display:flex; justify-content:space-between; align-items:center; padding:12px 24px; }
        nav a { color:white; text-decoration:none; margin-left:16px; font-weight:600; }
        nav a:hover { color:#bfdbfe; }
        .logo { font-weight:800; letter-spacing:0.5px; }
        .tagline { font-size: 14px; color:#cbd5e1; }
        main { max-width: 1080px; margin: 24px auto; padding: 0 16px 40px; }
        .card { background:white; padding: 28px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,.06); }
        .card h1 { margin-top:0; }
        footer { text-align:center; padding: 24px; color:#6b7280; }
        #toTop { position:fixed; bottom:24px; right:24px; background:var(--accent); color:white; padding:12px 14px; border-radius:50%; text-decoration:none; box-shadow:0 8px 20px rgba(37,99,235,.3); }
    </style>
</head>
<body>
    <header>
        <nav>
            <div>
                <div class="logo"><?php echo htmlspecialchars($settings['site_name'] ?? 'Site'); ?></div>
                <?php if (!empty($settings['tagline'])): ?><div class="tagline"><?php echo htmlspecialchars($settings['tagline']); ?></div><?php endif; ?>
            </div>
            <div>
                <?php foreach ($menuPages as $item): ?>
                    <a href="index.php?sayfa=<?php echo urlencode($item['slug']); ?>"><?php echo htmlspecialchars($item['title']); ?></a>
                <?php endforeach; ?>
            </div>
        </nav>
    </header>

    <main>
        <div class="card">
            <h1><?php echo htmlspecialchars($page['title']); ?></h1>
            <div class="content"><?php echo $page['content']; ?></div>
        </div>
    </main>

    <footer>Basit tema örneği — <?php echo date('Y'); ?></footer>
    <a id="toTop" href="#top" aria-label="Yukarı çık">↑</a>
</body>
</html>
