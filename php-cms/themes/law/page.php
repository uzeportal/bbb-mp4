<?php
/** Zübeyir Kayahan Avukatlık Bürosu teması */
?>
<?php
$cleanPhone = preg_replace('/\s+/', '', $settings['phone'] ?? '');
$cleanWhatsapp = preg_replace('/\D+/', '', $settings['whatsapp'] ?? '');
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
        :root { --dark: #0b132b; --gold: #d4af37; --gray: #e5e7eb; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: 'Inter', system-ui, -apple-system, sans-serif; color:#0f172a; background: linear-gradient(180deg, #f9fafb 0%, #eef2ff 100%); }
        a { color: var(--gold); }
        header { position: sticky; top:0; z-index: 30; backdrop-filter: blur(12px); background: rgba(11,19,43,0.92); box-shadow:0 8px 20px rgba(0,0,0,.28); }
        nav { display:flex; align-items:center; justify-content:space-between; padding:14px 26px; color:white; }
        .brand { font-weight:800; letter-spacing:0.5px; display:flex; align-items:center; gap:10px; }
        .brand span { color: var(--gold); }
        .tagline { color:#e5e7eb; font-size:13px; }
        .menu a { color:white; text-decoration:none; margin-left:18px; font-weight:600; }
        .menu a:hover { color: var(--gold); }
        .cta { display:flex; align-items:center; gap:10px; }
        .cta a { background: var(--gold); color:#0b132b; padding:10px 14px; border-radius:8px; font-weight:700; text-decoration:none; box-shadow:0 10px 30px rgba(212,175,55,.3); }
        .hero { max-width:1200px; margin:30px auto 18px; padding: 0 18px; display:grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap:24px; align-items:center; }
        .hero-text { padding:18px; }
        .hero-text h1 { font-size: 32px; margin:0 0 12px; color:#0b132b; }
        .hero-text p { line-height:1.7; margin:8px 0; }
        .badge { display:inline-flex; align-items:center; gap:10px; background: #0b132b; color:#fefefe; padding:8px 12px; border-radius: 999px; font-weight:700; }
        .badge small { color: var(--gold); font-weight:800; }
        .card-grid { max-width:1200px; margin: 12px auto 32px; padding: 0 18px; display:grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap:18px; }
        .card { background:white; border-radius:14px; padding:20px; box-shadow:0 16px 40px rgba(0,0,0,.08); border:1px solid #e5e7eb; }
        .card h3 { margin-top:0; color:#0b132b; }
        .content { line-height:1.7; }
        .contact { display:grid; grid-template-columns: repeat(auto-fit,minmax(320px,1fr)); gap:18px; align-items:start; }
        .map-wrap iframe { width:100%; min-height:260px; border:0; border-radius:10px; box-shadow:0 12px 24px rgba(0,0,0,.1); }
        footer { text-align:center; padding:24px; color:#0b132b; background:#0b132b; color:#f8fafc; position:relative; }
        #toTop { position:fixed; bottom:20px; right:20px; background:var(--gold); color:#0b132b; width:48px; height:48px; display:grid; place-items:center; border-radius:50%; text-decoration:none; font-weight:800; box-shadow:0 12px 20px rgba(0,0,0,.2); }
        @media(max-width:700px){ .menu { display:none; } nav { flex-wrap:wrap; gap:12px; } }
    </style>
</head>
<body>
<header>
    <nav>
        <div>
            <div class="brand">⚖️ <span><?php echo htmlspecialchars($settings['site_name'] ?? ''); ?></span></div>
            <?php if (!empty($settings['tagline'])): ?><div class="tagline"><?php echo htmlspecialchars($settings['tagline']); ?></div><?php endif; ?>
        </div>
        <div class="menu">
            <?php foreach ($menuPages as $item): ?>
                <a href="index.php?sayfa=<?php echo urlencode($item['slug']); ?>"><?php echo htmlspecialchars($item['title']); ?></a>
            <?php endforeach; ?>
        </div>
        <div class="cta">
            <a href="tel:<?php echo htmlspecialchars($cleanPhone); ?>">Ara</a>
            <a href="https://wa.me/<?php echo htmlspecialchars($cleanWhatsapp); ?>" style="background:#0b132b; color:white; box-shadow:none; border:1px solid var(--gold);">WhatsApp</a>
        </div>
    </nav>
</header>

<section class="hero">
    <div class="hero-text">
        <div class="badge">Güvenilir Hukuk Partneriniz <small>Sivas / Türkiye</small></div>
        <h1><?php echo htmlspecialchars($page['title']); ?></h1>
        <p><?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?></p>
        <div class="cta">
            <a href="mailto:<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">E-posta Gönder</a>
            <a href="#iletisim">Randevu Al</a>
        </div>
    </div>
    <div class="card">
        <h3>İletişim</h3>
        <p><strong>Adres:</strong><br><?php echo nl2br(htmlspecialchars($settings['address'] ?? '')); ?></p>
        <p><a href="tel:<?php echo htmlspecialchars($cleanPhone); ?>">Telefon: <?php echo htmlspecialchars($settings['phone'] ?? ''); ?></a><br>
           <a href="https://wa.me/<?php echo htmlspecialchars($cleanWhatsapp); ?>">WhatsApp: <?php echo htmlspecialchars($settings['whatsapp'] ?? ''); ?></a><br>
           <a href="mailto:<?php echo htmlspecialchars($settings['email'] ?? ''); ?>"><?php echo htmlspecialchars($settings['email'] ?? ''); ?></a></p>
        <p><strong>Çalışma Saatleri:</strong><br>Pazartesi - Cuma 09:00 - 18:30</p>
    </div>
</section>

<section class="card-grid">
    <div class="card content"><?php echo $page['content']; ?></div>
    <div class="card">
        <h3>Çalışma Alanları</h3>
        <ul>
            <li>İş Hukuku ve İş Güvencesi</li>
            <li>Ceza Hukuku ve Savunma</li>
            <li>Aile Hukuku (Boşanma, Velayet)</li>
            <li>Ticaret ve Şirketler Hukuku</li>
            <li>İcra ve İflas Hukuku</li>
        </ul>
    </div>
    <div class="card">
        <h3>Neden Biz?</h3>
        <p>Şeffaf bilgi akışı, hızlı randevu, stratejik dosya planlaması ve düzenli raporlama ile müvekkil odaklı çalışıyoruz.</p>
        <p>Her dosyada sonuç odaklı, etik ve titiz bir yaklaşım benimsiyoruz.</p>
    </div>
</section>

<section id="iletisim" class="card-grid contact">
    <div class="card">
        <h3>Hızlı Ulaşım</h3>
        <p><a href="tel:<?php echo htmlspecialchars($cleanPhone); ?>">Telefon: <?php echo htmlspecialchars($settings['phone'] ?? ''); ?></a></p>
        <p><a href="https://wa.me/<?php echo htmlspecialchars($cleanWhatsapp); ?>">WhatsApp: <?php echo htmlspecialchars($settings['whatsapp'] ?? ''); ?></a></p>
        <p><a href="mailto:<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">E-posta: <?php echo htmlspecialchars($settings['email'] ?? ''); ?></a></p>
        <?php if (!empty($settings['address'])): ?>
            <p><a href="https://maps.google.com/?q=<?php echo urlencode($settings['address']); ?>">Haritada Aç</a></p>
        <?php endif; ?>
    </div>
    <div class="card map-wrap">
        <iframe src="<?php echo htmlspecialchars($settings['map_embed'] ?? ''); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

<footer>
    © <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['site_name'] ?? ''); ?> — Tüm hakları saklıdır.
</footer>
<a id="toTop" href="#top" aria-label="Yukarı çık">↑</a>
</body>
</html>
