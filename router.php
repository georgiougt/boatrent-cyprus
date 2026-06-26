<?php
/**
 * Front-controller router for the PHP built-in server (development).
 * Run with:  php -S localhost:8021 -t . router.php
 *
 * Maps clean URLs to the underlying scripts:
 *   /                -> index.php
 *   /boats           -> boats.php
 *   /about /contact /faq /blog
 *   /blog/{slug}     -> blog-post.php
 *   /boat/{slug}     -> boat.php
 *   /{city}          -> city.php
 *
 * Production uses .htaccess (Apache) with the same rules.
 */

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim(rawurldecode($uri), '/');

// 0. Never expose the database folder, PHP includes or dotfiles.
if (preg_match('#^(data|includes)/#', $path) || preg_match('#(^|/)\.#', $path) || preg_match('#\.(sqlite|sqlite3|db)$#', $path)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

// 1. Serve real files (css, js, images, admin/*.php, submit-inquiry.php, sitemap.php, robots.txt) directly.
if ($path !== '' && is_file(__DIR__ . '/' . $path)) {
    return false;
}

// Helper: run a script as though it were the requested file (keeps PHP_SELF correct).
$run = function (string $script, array $params = []) {
    $_SERVER['SCRIPT_NAME'] = '/' . $script;
    $_SERVER['PHP_SELF']    = '/' . $script;
    foreach ($params as $k => $v) {
        $_GET[$k] = $v;
    }
    require __DIR__ . '/' . $script;
};

$segments = $path === '' ? [] : explode('/', $path);

// Static page map
$pages = [
    ''        => 'index.php',
    'boats'   => 'boats.php',
    'about'   => 'about.php',
    'contact' => 'contact.php',
    'faq'     => 'faq.php',
    'blog'    => 'blog.php',
];

if (array_key_exists($path, $pages)) {
    $run($pages[$path]);
    return true;
}

if ($path === 'sitemap.xml') {
    $run('sitemap.php');
    return true;
}

// Two-segment routes
if (count($segments) === 2 && $segments[0] === 'blog') {
    $run('blog-post.php', ['slug' => $segments[1]]);
    return true;
}
if (count($segments) === 2 && $segments[0] === 'boat') {
    $run('boat.php', ['slug' => $segments[1]]);
    return true;
}

// Single segment -> a city (city.php renders its own 404 for unknown slugs)
if (count($segments) === 1 && $segments[0] !== 'admin') {
    $run('city.php', ['slug' => $segments[0]]);
    return true;
}

// Anything else -> 404 (reuse the city page's not-found view)
http_response_code(404);
$run('city.php', ['slug' => '__not_found__']);
return true;
