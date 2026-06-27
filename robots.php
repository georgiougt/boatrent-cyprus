<?php
/**
 * Dynamic robots.txt. Served at /robots.txt via .htaccess (production) and
 * router.php (dev). On the real domain it allows crawling and points to the
 * sitemap; on any temporary/staging host it disallows everything.
 */
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: text/plain; charset=utf-8');

if (site_is_live()) {
    echo "User-agent: *\n";
    echo "Allow: /\n";
    echo "Disallow: /admin/\n";
    echo "Disallow: /submit-inquiry.php\n\n";
    echo 'Sitemap: ' . base_url() . "/sitemap.xml\n";
} else {
    // Temporary / staging domain — keep it out of search engines entirely.
    echo "User-agent: *\n";
    echo "Disallow: /\n";
}
