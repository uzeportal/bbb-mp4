<?php
require __DIR__ . '/bootstrap.php';

$slug = $_GET['sayfa'] ?? 'ana-sayfa';
$page = get_page_by_slug($pages, $slug);

if (!$page) {
    http_response_code(404);
    $page = [
        'title' => 'Sayfa bulunamadı',
        'slug' => '404',
        'content' => '<p>Aradığınız sayfa bulunamadı.</p>',
        'menu' => false,
    ];
}

$themePath = __DIR__ . '/themes/' . $active_theme;
if (!is_dir($themePath)) {
    $themePath = __DIR__ . '/themes/base';
}

$menuPages = array_filter($pages, fn($p) => !empty($p['menu']));

$meta = [
    'title' => $page['meta_title'] ?? ($page['title'] . ' — ' . ($settings['site_name'] ?? '')),
    'description' => $page['meta_description'] ?? ($settings['meta_description'] ?? ''),
    'keywords' => $page['meta_keywords'] ?? ($settings['meta_keywords'] ?? ''),
];

include $themePath . '/page.php';
