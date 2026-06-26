<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/content.php';

/* ---------------- SEO helpers ---------------- */

/** Absolute site origin, e.g. https://boatrentcyprus.com */
function base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'boatrentcyprus.com';
    return $scheme . '://' . $host;
}

/** Full URL of the current request. */
function current_url(): string
{
    return base_url() . strtok($_SERVER['REQUEST_URI'] ?? '/', '#');
}

/** Render a JSON-LD <script> block from an associative array. */
function json_ld(array $data): string
{
    return '<script type="application/ld+json">'
        . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        . '</script>';
}

/** Escape for HTML output. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Format a price like €1,500. */
function money($amount): string
{
    if ($amount === null || $amount === '' || (float) $amount <= 0) {
        return '—';
    }
    return '€' . number_format((float) $amount, 0, '.', ',');
}

/** Decode a JSON column safely to an array. */
function json_arr(?string $json): array
{
    if (!$json) {
        return [];
    }
    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : [];
}

/** CSRF token helpers. */
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

/* ---------------- Data accessors ---------------- */

function get_cities(): array
{
    return db()->query('SELECT * FROM cities ORDER BY name')->fetchAll();
}

function get_city(string $slug): ?array
{
    $stmt = db()->prepare('SELECT * FROM cities WHERE slug = ?');
    $stmt->execute([$slug]);
    $city = $stmt->fetch();
    return $city ?: null;
}

function count_boats_in_city(int $cityId): int
{
    $stmt = db()->prepare("SELECT COUNT(*) FROM boats WHERE city_id = ? AND status = 'active'");
    $stmt->execute([$cityId]);
    return (int) $stmt->fetchColumn();
}

function get_featured_boats(int $limit = 6): array
{
    $stmt = db()->prepare("
        SELECT b.*, c.name AS city_name, c.slug AS city_slug
        FROM boats b JOIN cities c ON c.id = b.city_id
        WHERE b.featured = 1 AND b.status = 'active'
        ORDER BY b.created_at DESC LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Flexible boat query with optional filters.
 * $filters: city_id, type, guests, max_price, sort, q
 */
function get_boats(array $filters = []): array
{
    $sql = "SELECT b.*, c.name AS city_name, c.slug AS city_slug
            FROM boats b JOIN cities c ON c.id = b.city_id
            WHERE b.status = 'active'";
    $params = [];

    if (!empty($filters['city_id'])) {
        $sql .= ' AND b.city_id = :city_id';
        $params[':city_id'] = $filters['city_id'];
    }
    if (!empty($filters['type'])) {
        $sql .= ' AND b.type = :type';
        $params[':type'] = $filters['type'];
    }
    if (!empty($filters['guests'])) {
        $sql .= ' AND b.capacity >= :guests';
        $params[':guests'] = (int) $filters['guests'];
    }
    if (!empty($filters['max_price'])) {
        $sql .= ' AND b.price_day <= :max_price';
        $params[':max_price'] = (float) $filters['max_price'];
    }
    if (!empty($filters['q'])) {
        $sql .= ' AND (b.name LIKE :q OR b.type LIKE :q OR c.name LIKE :q)';
        $params[':q'] = '%' . $filters['q'] . '%';
    }

    switch ($filters['sort'] ?? '') {
        case 'price_asc':  $sql .= ' ORDER BY b.price_day ASC';  break;
        case 'price_desc': $sql .= ' ORDER BY b.price_day DESC'; break;
        case 'capacity':   $sql .= ' ORDER BY b.capacity DESC';  break;
        default:           $sql .= ' ORDER BY b.featured DESC, b.name ASC';
    }

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_boat(int $id): ?array
{
    $stmt = db()->prepare("
        SELECT b.*, c.name AS city_name, c.slug AS city_slug
        FROM boats b JOIN cities c ON c.id = b.city_id
        WHERE b.id = ?
    ");
    $stmt->execute([$id]);
    $boat = $stmt->fetch();
    return $boat ?: null;
}

function get_boat_by_slug(string $slug): ?array
{
    $stmt = db()->prepare("
        SELECT b.*, c.name AS city_name, c.slug AS city_slug
        FROM boats b JOIN cities c ON c.id = b.city_id
        WHERE b.slug = ?
    ");
    $stmt->execute([$slug]);
    $boat = $stmt->fetch();
    return $boat ?: null;
}

function get_boat_types(): array
{
    return db()->query("SELECT DISTINCT type FROM boats WHERE status = 'active' ORDER BY type")
        ->fetchAll(PDO::FETCH_COLUMN);
}

/** Icon for a vessel type (returns an SVG path 'd' set). */
function type_badge_class(string $type): string
{
    return 'bg-brand-aqua/10 text-brand-aqua';
}

/**
 * Country dial codes for phone inputs. Cyprus first (default), then the main
 * source markets for Cyprus boat charters, then broader coverage.
 * [country name, dial code]
 */
function country_dial_codes(): array
{
    return [
        ['Cyprus', '+357'], ['United Kingdom', '+44'], ['Greece', '+30'],
        ['Germany', '+49'], ['Russia', '+7'], ['Israel', '+972'],
        ['France', '+33'], ['Italy', '+39'], ['Netherlands', '+31'],
        ['Sweden', '+46'], ['Norway', '+47'], ['Denmark', '+45'],
        ['Finland', '+358'], ['Poland', '+48'], ['Ukraine', '+380'],
        ['Romania', '+40'], ['Austria', '+43'], ['Switzerland', '+41'],
        ['Belgium', '+32'], ['Ireland', '+353'], ['Spain', '+34'],
        ['Portugal', '+351'], ['Czechia', '+420'], ['Hungary', '+36'],
        ['Lebanon', '+961'], ['United Arab Emirates', '+971'], ['Saudi Arabia', '+966'],
        ['Qatar', '+974'], ['Kuwait', '+965'], ['Turkey', '+90'],
        ['United States', '+1'], ['Canada', '+1'], ['Australia', '+61'],
        ['South Africa', '+27'], ['India', '+91'], ['China', '+86'],
    ];
}

/** Renders <option> tags for the dial-code select, with $selected pre-chosen. */
function dial_code_options(string $selected = '+357'): string
{
    $out = '';
    $matched = false;
    foreach (country_dial_codes() as [$name, $code]) {
        // only mark the first occurrence of a shared code (e.g. +1) as selected
        $isSel = (!$matched && $code === $selected);
        if ($isSel) {
            $matched = true;
        }
        $out .= '<option value="' . e($code) . '"' . ($isSel ? ' selected' : '') . '>'
              . e($code . '  ' . $name) . '</option>';
    }
    return $out;
}
