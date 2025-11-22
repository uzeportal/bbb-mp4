<?php
session_start();

$config = require __DIR__ . '/config.php';

function persist_config(array $config): void
{
    file_put_contents(__DIR__ . '/config.php', "<?php\nreturn " . var_export($config, true) . ";\n");
}

function load_pages(array $config): array
{
    if (!file_exists($config['data_file'])) {
        return [];
    }
    $json = file_get_contents($config['data_file']);
    $pages = json_decode($json, true);
    return is_array($pages) ? $pages : [];
}

function load_settings(array $config): array
{
    if (!file_exists($config['settings_file'])) {
        return [];
    }
    $json = file_get_contents($config['settings_file']);
    $settings = json_decode($json, true);
    return is_array($settings) ? $settings : [];
}

function save_pages(array $config, array $pages): void
{
    file_put_contents($config['data_file'], json_encode($pages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function save_settings(array $config, array $settings): void
{
    file_put_contents($config['settings_file'], json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function get_page_by_slug(array $pages, string $slug): ?array
{
    foreach ($pages as $page) {
        if ($page['slug'] === $slug) {
            return $page;
        }
    }
    return null;
}

function sanitize_slug(string $slug): string
{
    $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);
    $slug = trim($slug, '-');
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
    $slug = strtolower($slug);
    $slug = preg_replace('~[^-\w]+~', '', $slug);
    return $slug ?: 'sayfa';
}

function ensure_default_data(array $config): void
{
    if (!file_exists($config['data_file'])) {
        $pages = [
            [
                'title' => 'Ana Sayfa',
                'slug' => 'ana-sayfa',
                'content' => "<h2>Zübeyir Kayahan Avukatlık Bürosu</h2><p>Sivas'ta müvekkillerimize iş hukuku, ceza hukuku, aile hukuku ve ticaret hukuku alanlarında danışmanlık ve dava takibi hizmeti sunuyoruz.</p><p><strong>Adres:</strong> Cami-i Kebir Mah., Hoca Ahmet Yesevi Cad., No:24/2, Merkez Sivas<br><strong>Telefon:</strong> +90 532 772 64 66<br><strong>WhatsApp:</strong> +90 532 772 64 66</p>",
                'menu' => true,
                'meta_title' => 'Zübeyir Kayahan Avukatlık Bürosu — Ana Sayfa',
                'meta_description' => 'Sivas merkezli Zübeyir Kayahan Avukatlık Bürosu; iş, ceza, aile ve ticaret hukuku alanlarında dava takibi ve danışmanlık sunar.',
                'meta_keywords' => 'Sivas avukat, hukuk bürosu, iş hukuku, aile hukuku, ceza hukuku',
            ],
            [
                'title' => 'Hakkımızda',
                'slug' => 'hakkimizda',
                'content' => '<p>Zübeyir Kayahan Avukatlık Bürosu, her dosyada şeffaf iletişimi ve stratejik dava yönetimini önceliklendiren bir yaklaşımla çalışır. Müvekkillerimizin haklarını korurken süreci anlaşılır kılmaya önem veriyoruz.</p>',
                'menu' => true,
                'meta_title' => 'Zübeyir Kayahan Avukatlık Bürosu — Hakkımızda',
                'meta_description' => 'Zübeyir Kayahan Avukatlık Bürosu ekibini, yaklaşımını ve önceliklerini tanıyın.',
                'meta_keywords' => 'Zübeyir Kayahan, avukat, hukuk bürosu',
            ],
            [
                'title' => 'Çalışma Alanları',
                'slug' => 'calisma-alanlari',
                'content' => '<ul><li>İş Hukuku</li><li>Aile Hukuku</li><li>Ceza Hukuku</li><li>Ticaret Hukuku</li><li>İcra ve İflas Hukuku</li></ul>',
                'menu' => true,
                'meta_title' => 'Çalışma Alanları — Zübeyir Kayahan Avukatlık Bürosu',
                'meta_description' => 'İş, aile, ceza, ticaret ve icra-iflas hukuku alanlarında sunduğumuz hizmetler.',
                'meta_keywords' => 'iş hukuku, aile hukuku, ceza hukuku, icra iflas',
            ],
            [
                'title' => 'İletişim',
                'slug' => 'iletisim',
                'content' => "<p>Bizimle dilediğiniz zaman iletişime geçebilirsiniz.</p><p><a href='tel:+905327726466'>Telefon: +90 532 772 64 66</a><br><a href='https://wa.me/905327726466'>WhatsApp: +90 532 772 64 66</a><br><a href='mailto:info@zkayahan.av.tr'>E-posta: info@zkayahan.av.tr</a></p><div class='map-wrap'><iframe src='https://www.google.com/maps?q=Cami-i%20Kebir%20Mah.,%20Hoca%20Ahmet%20Yesevi%20Cad.,%20No:24/2,%20Merkez%20Sivas&output=embed' loading='lazy' referrerpolicy='no-referrer-when-downgrade'></iframe></div>",
                'menu' => true,
                'meta_title' => 'İletişim — Zübeyir Kayahan Avukatlık Bürosu',
                'meta_description' => 'Adres, telefon, WhatsApp ve harita bilgileriyle Zübeyir Kayahan Avukatlık Bürosu iletişim sayfası.',
                'meta_keywords' => 'iletişim, avukat, Sivas, telefon, whatsapp',
            ],
        ];

        save_pages($config, $pages);
    }

    if (!file_exists($config['settings_file'])) {
        $settings = [
            'site_name' => 'Zübeyir Kayahan Avukatlık Bürosu',
            'tagline' => 'Sivas\'ta kapsamlı hukuki danışmanlık ve dava takibi',
            'address' => 'Cami-i Kebir Mah., Hoca Ahmet Yesevi Cad., No:24/2, Merkez Sivas',
            'phone' => '+90 532 772 64 66',
            'whatsapp' => '+90 532 772 64 66',
            'email' => 'info@zkayahan.av.tr',
            'map_embed' => 'https://www.google.com/maps?q=Cami-i%20Kebir%20Mah.,%20Hoca%20Ahmet%20Yesevi%20Cad.,%20No:24/2,%20Merkez%20Sivas&output=embed',
            'meta_title' => 'Zübeyir Kayahan Avukatlık Bürosu — Sivas',
            'meta_description' => 'Sivas merkezli hukuk bürosu; iş, ceza, aile ve ticaret hukukunda avukatlık, danışmanlık ve dava hizmetleri.',
            'meta_keywords' => 'Sivas avukat, avukat, hukuk bürosu, danışmanlık',
            'base_url' => 'http://localhost:8000',
        ];
        save_settings($config, $settings);
    }
}

ensure_default_data($config);

$pages = load_pages($config);
$active_theme = $config['active_theme'];
$settings = load_settings($config);

// Var olan sayfalara meta alanları eklenmemişse doldur
foreach ($pages as &$p) {
    $p['meta_title'] = $p['meta_title'] ?? ($p['title'] . ' — ' . ($settings['site_name'] ?? 'Site'));
    $p['meta_description'] = $p['meta_description'] ?? ($settings['meta_description'] ?? '');
    $p['meta_keywords'] = $p['meta_keywords'] ?? ($settings['meta_keywords'] ?? '');
}
unset($p);
save_pages($config, $pages);
