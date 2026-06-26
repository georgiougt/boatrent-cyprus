<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml; charset=utf-8');

$base = base_url();
$today = date('Y-m-d');

$urls = [];
$add = function (string $loc, string $changefreq, string $priority, ?string $lastmod = null) use (&$urls, $today) {
    $urls[] = [
        'loc' => $loc,
        'lastmod' => $lastmod ?? $today,
        'changefreq' => $changefreq,
        'priority' => $priority,
    ];
};

// Static pages
$add($base . '/',         'weekly', '1.0');
$add($base . '/boats',    'daily',  '0.9');
$add($base . '/about',    'monthly','0.6');
$add($base . '/faq',      'monthly','0.6');
$add($base . '/blog',     'weekly', '0.7');
$add($base . '/contact',  'yearly', '0.5');

// Cities
foreach (get_cities() as $c) {
    $add($base . '/' . $c['slug'], 'weekly', '0.8');
}

// Boats
foreach (get_boats() as $b) {
    $add($base . '/boat/' . $b['slug'], 'weekly', '0.7');
}

// Blog posts
foreach (get_blog_posts() as $p) {
    $add($base . '/blog/' . $p['slug'], 'monthly', '0.6', $p['date']);
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
    echo '    <lastmod>' . $u['lastmod'] . "</lastmod>\n";
    echo '    <changefreq>' . $u['changefreq'] . "</changefreq>\n";
    echo '    <priority>' . $u['priority'] . "</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>' . "\n";
