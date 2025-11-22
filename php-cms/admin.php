<?php
require __DIR__ . '/bootstrap.php';

if (!isset($_SESSION['logged_in'])) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['password'] === $config['admin_password']) {
            $_SESSION['logged_in'] = true;
            header('Location: admin.php');
            exit;
        }
        $error = 'Hatalı şifre';
    }
    ?>
    <html lang="tr">
    <head>
        <meta charset="utf-8">
        <title>Yönetim Paneli</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 40px; background:#f7f7f7; }
            .login { max-width: 400px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
            input[type=password] { width: 100%; padding: 10px; margin-bottom: 12px; }
            button { padding: 10px 16px; background:#0073aa; color:white; border:0; border-radius: 4px; cursor:pointer; }
            .error { color: #b30000; }
        </style>
    </head>
    <body>
        <div class="login">
            <h1>Yönetim Paneli</h1>
            <?php if ($error): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
            <form method="post">
                <label>Şifre</label>
                <input type="password" name="password" required>
                <button type="submit">Giriş Yap</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (isset($_GET['cikis'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$message = '';
$editPage = null;

if (isset($_GET['duzenle'])) {
    $editPage = get_page_by_slug($pages, $_GET['duzenle']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_page' && isset($_POST['title'], $_POST['content'])) {
        $originalSlug = $_POST['original_slug'] ?? '';
        $slug = sanitize_slug($_POST['slug'] ?: $_POST['title']);
        $menu = isset($_POST['menu']);

        $pageData = [
            'title' => trim($_POST['title']),
            'slug' => $slug,
            'content' => $_POST['content'],
            'menu' => $menu,
            'meta_title' => trim($_POST['meta_title'] ?: ($_POST['title'] . ' — ' . ($settings['site_name'] ?? ''))),
            'meta_description' => trim($_POST['meta_description'] ?: ($settings['meta_description'] ?? '')),
            'meta_keywords' => trim($_POST['meta_keywords'] ?: ($settings['meta_keywords'] ?? '')),
        ];

        $updated = false;
        foreach ($pages as &$page) {
            if ($page['slug'] === $originalSlug || ($originalSlug === '' && $page['slug'] === $slug)) {
                $page = $pageData;
                $updated = true;
            }
        }
        unset($page);

        if (!$updated) {
            $pages[] = $pageData;
        }

        save_pages($config, $pages);
        $message = $updated ? 'Sayfa güncellendi' : 'Sayfa eklendi';
        $editPage = null;
    }

    if ($action === 'delete_page' && isset($_POST['delete_slug'])) {
        $pages = array_values(array_filter($pages, fn($p) => $p['slug'] !== $_POST['delete_slug']));
        save_pages($config, $pages);
        $message = 'Sayfa silindi';
    }

    if ($action === 'set_theme' && isset($_POST['active_theme'])) {
        $config['active_theme'] = $_POST['active_theme'];
        persist_config($config);
        $message = 'Tema güncellendi';
    }

    if ($action === 'upload_theme' && !empty($_FILES['theme_zip']['name'])) {
        $file = $_FILES['theme_zip'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $zipPath = sys_get_temp_dir() . '/' . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $zipPath);
            $zip = new ZipArchive();
            if ($zip->open($zipPath) === true) {
                $target = $config['uploads_dir'];
                $zip->extractTo($target);
                $zip->close();
                $message = 'Tema yüklendi';
            } else {
                $message = 'Zip açılamadı';
            }
            @unlink($zipPath);
        } else {
            $message = 'Tema yüklenemedi';
        }
    }

    if ($action === 'save_settings') {
        $settings['site_name'] = trim($_POST['site_name']);
        $settings['tagline'] = trim($_POST['tagline']);
        $settings['address'] = trim($_POST['address']);
        $settings['phone'] = trim($_POST['phone']);
        $settings['whatsapp'] = trim($_POST['whatsapp']);
        $settings['email'] = trim($_POST['email']);
        $settings['map_embed'] = trim($_POST['map_embed']);
        $settings['meta_title'] = trim($_POST['meta_title']);
        $settings['meta_description'] = trim($_POST['meta_description']);
        $settings['meta_keywords'] = trim($_POST['meta_keywords']);
        $settings['base_url'] = trim($_POST['base_url']);
        save_settings($config, $settings);
        $message = 'Site & SEO ayarları kaydedildi';
    }

    if ($action === 'change_password' && isset($_POST['current_password'], $_POST['new_password'], $_POST['new_password_confirm'])) {
        if ($_POST['current_password'] !== $config['admin_password']) {
            $message = 'Mevcut şifre yanlış';
        } elseif ($_POST['new_password'] !== $_POST['new_password_confirm']) {
            $message = 'Yeni şifreler eşleşmiyor';
        } elseif (strlen($_POST['new_password']) < 6) {
            $message = 'Şifre en az 6 karakter olmalı';
        } else {
            $config['admin_password'] = $_POST['new_password'];
            persist_config($config);
            $message = 'Yönetici şifresi güncellendi';
        }
    }

    // Form dönüşlerinde düzenleme modunu sıfırla
    if (!isset($_GET['duzenle'])) {
        $editPage = null;
    }
}

$themeDirs = array_values(array_filter(scandir(__DIR__ . '/themes'), fn($dir) => !in_array($dir, ['.', '..']) && is_dir(__DIR__ . '/themes/' . $dir)));
?>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>Yönetim Paneli</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin:0; padding:0; }
        header { background: #111827; color:white; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; }
        main { max-width: 1200px; margin: 24px auto; padding: 0 16px 48px; }
        section { background: white; padding: 20px; margin-bottom: 16px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,.06); }
        label { display:block; margin-top: 10px; font-weight:600; }
        input[type=text], input[type=password], textarea { width: 100%; padding: 10px; margin-top: 6px; border-radius: 4px; border: 1px solid #d1d5db; }
        textarea { min-height: 120px; }
        button { margin-top: 12px; padding: 10px 16px; background:#2563eb; color:white; border:0; border-radius: 6px; cursor:pointer; }
        .message { background:#ecfeff; border:1px solid #67e8f9; padding: 10px; border-radius: 6px; color:#0e7490; }
        table { width:100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border-bottom:1px solid #e5e7eb; padding: 8px; text-align:left; }
        .actions form { display:inline; }
        .two-col { display:grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap:14px; }
    </style>
</head>
<body>
<header>
    <div>
        <strong>Yönetim Paneli</strong> — Aktif Tema: <?php echo htmlspecialchars($config['active_theme']); ?>
    </div>
    <div>
        <a href="?cikis=1" style="color:white; margin-left:12px;">Çıkış</a>
    </div>
</header>
<main>
    <?php if ($message): ?><p class="message"><?php echo $message; ?></p><?php endif; ?>

    <section>
        <h2>Sayfa Ekle / Güncelle</h2>
        <form method="post">
            <input type="hidden" name="action" value="save_page">
            <input type="hidden" name="original_slug" value="<?php echo $editPage['slug'] ?? ''; ?>">
            <label>Başlık</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($editPage['title'] ?? ''); ?>">
            <label>Slug (boş bırakılırsa başlıktan üretilir)</label>
            <input type="text" name="slug" value="<?php echo htmlspecialchars($editPage['slug'] ?? ''); ?>">
            <label>Menüde görünsün</label>
            <input type="checkbox" name="menu" <?php echo !isset($editPage['menu']) || !empty($editPage['menu']) ? 'checked' : ''; ?>>
            <label>İçerik</label>
            <textarea name="content" required><?php echo htmlspecialchars($editPage['content'] ?? ''); ?></textarea>
            <div class="two-col">
                <div>
                    <label>Meta Başlık</label>
                    <input type="text" name="meta_title" value="<?php echo htmlspecialchars($editPage['meta_title'] ?? ''); ?>">
                </div>
                <div>
                    <label>Meta Açıklama</label>
                    <input type="text" name="meta_description" value="<?php echo htmlspecialchars($editPage['meta_description'] ?? ''); ?>">
                </div>
            </div>
            <label>Meta Anahtar Kelimeler</label>
            <input type="text" name="meta_keywords" value="<?php echo htmlspecialchars($editPage['meta_keywords'] ?? ''); ?>">
            <button type="submit">Kaydet</button>
            <?php if ($editPage): ?><a href="admin.php" style="margin-left:8px;">Yeni kayıt</a><?php endif; ?>
        </form>
    </section>

    <section>
        <h2>Sayfalar</h2>
        <table>
            <thead><tr><th>Başlık</th><th>Slug</th><th>Menü</th><th>SEO Başlık</th><th>İşlemler</th></tr></thead>
            <tbody>
            <?php foreach ($pages as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo htmlspecialchars($p['slug']); ?></td>
                    <td><?php echo !empty($p['menu']) ? 'Evet' : 'Hayır'; ?></td>
                    <td><?php echo htmlspecialchars($p['meta_title'] ?? ''); ?></td>
                    <td class="actions">
                        <a href="?duzenle=<?php echo urlencode($p['slug']); ?>">Düzenle</a>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Silinsin mi?');">
                            <input type="hidden" name="action" value="delete_page">
                            <input type="hidden" name="delete_slug" value="<?php echo htmlspecialchars($p['slug']); ?>">
                            <button type="submit">Sil</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section>
        <h2>Site Bilgisi & SEO</h2>
        <form method="post">
            <input type="hidden" name="action" value="save_settings">
            <div class="two-col">
                <div>
                    <label>Site Adı</label>
                    <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                </div>
                <div>
                    <label>Alt Başlık / Slogan</label>
                    <input type="text" name="tagline" value="<?php echo htmlspecialchars($settings['tagline'] ?? ''); ?>">
                </div>
            </div>
            <label>Adres</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($settings['address'] ?? ''); ?>">
            <div class="two-col">
                <div>
                    <label>Telefon</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>">
                </div>
                <div>
                    <label>WhatsApp</label>
                    <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($settings['whatsapp'] ?? ''); ?>">
                </div>
            </div>
            <label>E-posta</label>
            <input type="text" name="email" value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>">
            <label>Harita Embed URL</label>
            <input type="text" name="map_embed" value="<?php echo htmlspecialchars($settings['map_embed'] ?? ''); ?>">
            <div class="two-col">
                <div>
                    <label>Varsayılan Meta Başlık</label>
                    <input type="text" name="meta_title" value="<?php echo htmlspecialchars($settings['meta_title'] ?? ''); ?>">
                </div>
                <div>
                    <label>Varsayılan Meta Açıklama</label>
                    <input type="text" name="meta_description" value="<?php echo htmlspecialchars($settings['meta_description'] ?? ''); ?>">
                </div>
            </div>
            <label>Varsayılan Meta Anahtar Kelimeler</label>
            <input type="text" name="meta_keywords" value="<?php echo htmlspecialchars($settings['meta_keywords'] ?? ''); ?>">
            <label>Temel URL (kanonik linkler için)</label>
            <input type="text" name="base_url" value="<?php echo htmlspecialchars($settings['base_url'] ?? ''); ?>">
            <button type="submit">Kaydet</button>
        </form>
    </section>

    <section>
        <h2>Tema Seçimi</h2>
        <form method="post">
            <input type="hidden" name="action" value="set_theme">
            <?php foreach ($themeDirs as $dir): ?>
                <label><input type="radio" name="active_theme" value="<?php echo $dir; ?>" <?php echo $config['active_theme'] == $dir ? 'checked' : ''; ?>> <?php echo $dir; ?></label><br>
            <?php endforeach; ?>
            <button type="submit">Kaydet</button>
        </form>
    </section>

    <section>
        <h2>Tema Yükle (.zip)</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload_theme">
            <input type="file" name="theme_zip" accept="application/zip" required>
            <button type="submit">Yükle</button>
        </form>
        <p>Kök klasörde bir tema klasörü içeren zip dosyası yükleyebilirsiniz.</p>
    </section>

    <section>
        <h2>Yönetici Bilgisi</h2>
        <form method="post">
            <input type="hidden" name="action" value="change_password">
            <label>Mevcut Şifre</label>
            <input type="password" name="current_password" required>
            <div class="two-col">
                <div>
                    <label>Yeni Şifre</label>
                    <input type="password" name="new_password" required>
                </div>
                <div>
                    <label>Yeni Şifre (Tekrar)</label>
                    <input type="password" name="new_password_confirm" required>
                </div>
            </div>
            <button type="submit">Şifreyi Güncelle</button>
        </form>
    </section>
</main>
</body>
</html>
