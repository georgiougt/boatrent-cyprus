<?php
require_once __DIR__ . '/config.php';
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

/** Headline price for cards & detail pages: the stored label, or a sensible fallback. */
function boat_price_label(array $boat): string
{
    $label = trim((string) ($boat['price_label'] ?? ''));
    if ($label !== '') {
        return $label;
    }
    if (!empty($boat['price_day']) && (float) $boat['price_day'] > 0) {
        return money($boat['price_day']) . ' / day';
    }
    return 'On request';
}

/** Human labels for the detailedPricing keys stored on a boat. */
function pricing_row_label(string $key): string
{
    $labels = [
        'halfDay'     => 'Half day',
        'fullDay'     => 'Full day',
        'overnight'   => 'Overnight',
        'weekly'      => 'Weekly',
        'twoHours'    => '2 hours',
        'threeHours'  => '3 hours',
        'fourHours'   => '4 hours',
        'other'       => 'Other',
    ];
    return $labels[$key] ?? ucfirst(preg_replace('/(?<!^)([A-Z])/', ' $1', $key));
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

/**
 * Animated wave divider between two sections (reuses the hero's 3-layer foam).
 * $fill = colour of the section BELOW (the waves fill down into it).
 * $bg   = colour of the section ABOVE (the divider's backdrop).
 */
function section_wave(string $fill, string $bg): void
{
    $p1 = 'M0,60 C240,100 480,100 720,60 C960,20 1200,20 1440,60 L1440,120 L0,120 Z';
    $p2 = 'M0,50 C300,90 540,10 720,50 C960,100 1140,20 1440,50 L1440,120 L0,120 Z';
    $p3 = 'M0,70 C240,40 480,40 720,70 C960,100 1200,100 1440,70 L1440,120 L0,120 Z';
    ?>
    <div class="section-wave" style="--wave-fill: <?php echo e($fill); ?>; background: <?php echo e($bg); ?>;" aria-hidden="true">
      <svg class="wave-svg wave-svg--back" viewBox="0 0 1440 120" preserveAspectRatio="none"><g class="wave-move wave-move--back"><path d="<?php echo $p1; ?>"></path><path d="<?php echo $p1; ?>" transform="translate(1440,0)"></path></g></svg>
      <svg class="wave-svg wave-svg--mid" viewBox="0 0 1440 120" preserveAspectRatio="none"><g class="wave-move wave-move--mid"><path d="<?php echo $p2; ?>"></path><path d="<?php echo $p2; ?>" transform="translate(1440,0)"></path></g></svg>
      <svg class="wave-svg wave-svg--front" viewBox="0 0 1440 120" preserveAspectRatio="none"><g class="wave-move wave-move--front"><path d="<?php echo $p3; ?>"></path><path d="<?php echo $p3; ?>" transform="translate(1440,0)"></path></g></svg>
    </div>
    <?php
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

/* ---------------- Sailing route map (self-contained inline SVG) ---------------- */

/** Simplified Cyprus coastline as [lon, lat] points, clockwise. */
function cyprus_outline(): array
{
    return [
        [32.27, 35.10], [32.65, 35.20], [33.10, 35.22], [33.35, 35.36],
        [33.70, 35.35], [34.05, 35.38], [34.55, 35.68], [34.25, 35.35],
        [33.95, 35.13], [34.05, 35.02], [34.08, 34.96], [33.90, 34.98],
        [33.62, 34.90], [33.55, 34.82], [33.29, 34.70], [33.06, 34.68],
        [32.95, 34.55], [32.78, 34.66], [32.62, 34.66], [32.42, 34.74],
        [32.33, 34.87], [32.28, 34.95],
    ];
}

/** Project one lon/lat into a target rect [x, y, w, h] given a geo bbox. */
function _cy_fit(float $lon, float $lat, array $bbox, array $rect): array
{
    [$lonMin, $lonMax, $latMin, $latMax] = $bbox;
    [$rx, $ry, $rw, $rh] = $rect;
    $x = $rx + ($lon - $lonMin) / ($lonMax - $lonMin) * $rw;
    $y = $ry + ($latMax - $lat) / ($latMax - $latMin) * $rh;
    return [round($x, 1), round($y, 1)];
}

/**
 * Render an inline SVG map of a sailing route. The main view auto-zooms to
 * the route's own stops (so nearby stops stay legible), with a small Cyprus
 * locator inset for geographic context. Fully self-contained — no tiles, no
 * JS, theme colours inlined. Each stop: ['lon'=>, 'lat'=>, 'name'=>].
 * $id must be unique per map on the page (scopes the gradient).
 */
function render_route_map(array $stops, string $id = 'routemap'): string
{
    $w = 1000.0; $h = 640.0;
    $rect = [70.0, 60.0, 860.0, 520.0]; // inner drawing area (leaves label room)

    // --- Fit a geo bbox to the stops, keep aspect ratio, add breathing room ---
    $lons = array_map(fn($s) => (float) $s['lon'], $stops);
    $lats = array_map(fn($s) => (float) $s['lat'], $stops);
    $lonMin = min($lons); $lonMax = max($lons); $latMin = min($lats); $latMax = max($lats);
    // Minimum span so a tight cluster still shows some coastline context.
    $lonC = ($lonMin + $lonMax) / 2; $latC = ($latMin + $latMax) / 2;
    $lonSpan = max($lonMax - $lonMin, 0.10);
    $latSpan = max($latMax - $latMin, 0.05);
    // Match the drawing-rect aspect (using rough km/deg so nothing is stretched).
    $kmLon = 91.0; $kmLat = 111.0;
    $rectAspect = $rect[2] / $rect[3];
    if (($lonSpan * $kmLon) / ($latSpan * $kmLat) < $rectAspect) {
        $lonSpan = $latSpan * $kmLat * $rectAspect / $kmLon;
    } else {
        $latSpan = $lonSpan * $kmLon / ($kmLat * $rectAspect);
    }
    $lonSpan *= 1.8; $latSpan *= 1.8; // padding around the route
    $bbox = [$lonC - $lonSpan / 2, $lonC + $lonSpan / 2, $latC - $latSpan / 2, $latC + $latSpan / 2];

    // --- Coastline for the zoomed view (same simplified outline, clipped by the frame) ---
    $island = array_map(fn($p) => _cy_fit($p[0], $p[1], $bbox, $rect), cyprus_outline());
    $islandPath = 'M ' . implode(' L ', array_map(fn($p) => "{$p[0]},{$p[1]}", $island)) . ' Z';

    // --- Route line + numbered markers with alternating label placement ---
    $pts = array_map(fn($s) => _cy_fit((float) $s['lon'], (float) $s['lat'], $bbox, $rect), $stops);
    $routePath = $pts ? ('M ' . implode(' L ', array_map(fn($p) => "{$p[0]},{$p[1]}", $pts))) : '';
    $markers = '';
    $drawn = []; // skip markers that coincide with an earlier one (e.g. return-to-start)
    foreach ($pts as $i => $p) {
        [$x, $y] = $p;
        $skip = false;
        foreach ($drawn as $d) {
            if (hypot($x - $d[0], $y - $d[1]) < 32) { $skip = true; break; }
        }
        if ($skip) { continue; }
        $drawn[] = $p;
        $n = $i + 1;
        $anchor = $x > ($rect[0] + $rect[2] * 0.6) ? 'end' : 'start';
        $lx = $anchor === 'end' ? $x - 20 : $x + 20;
        $ly = $y + (count($drawn) % 2 ? -20 : 22); // stagger vertically to reduce collisions
        $label = e($stops[$i]['name']);
        $markers .= <<<SVG
    <g>
      <text x="{$lx}" y="{$ly}" dy="0.32em" text-anchor="{$anchor}" font-size="22" font-weight="600" fill="#0D1A33" stroke="#EAF4F8" stroke-width="4.5" paint-order="stroke" style="font-family:'Plus Jakarta Sans',sans-serif">{$label}</text>
      <circle cx="{$x}" cy="{$y}" r="16" fill="#0D1A33" stroke="#fff" stroke-width="2.5"/>
      <text x="{$x}" y="{$y}" dy="0.34em" text-anchor="middle" font-size="18" font-weight="700" fill="#fff" style="font-family:'Plus Jakarta Sans',sans-serif">{$n}</text>
    </g>
SVG;
    }

    // --- Locator inset: whole island, dropped in the corner farthest from the route ---
    $natBox = [32.15, 34.65, 34.50, 35.75];
    $inW = 208.0; $inH = 150.0; $m = 22.0;
    $corners = [
        [$m, $m], [$w - $inW - $m, $m],
        [$m, $h - $inH - $m], [$w - $inW - $m, $h - $inH - $m],
    ];
    $bestCorner = $corners[3]; $bestDist = -1;
    foreach ($corners as $c) {
        $cx = $c[0] + $inW / 2; $cy = $c[1] + $inH / 2;
        $nearest = PHP_FLOAT_MAX;
        foreach ($pts as $p) { $nearest = min($nearest, hypot($cx - $p[0], $cy - $p[1])); }
        if ($nearest > $bestDist) { $bestDist = $nearest; $bestCorner = $c; }
    }
    [$inX, $inY] = $bestCorner;
    $inRect = [$inX + 12, $inY + 12, $inW - 24, $inH - 24];
    $natIsland = array_map(fn($p) => _cy_fit($p[0], $p[1], $natBox, $inRect), cyprus_outline());
    $natPath = 'M ' . implode(' L ', array_map(fn($p) => "{$p[0]},{$p[1]}", $natIsland)) . ' Z';
    [$dotX, $dotY] = _cy_fit($lonC, $latC, $natBox, $inRect);

    return <<<SVG
<svg viewBox="0 0 {$w} {$h}" role="img" aria-label="Map of the sailing route along the Cyprus coast" class="w-full h-auto">
  <defs>
    <linearGradient id="{$id}-sea" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0" stop-color="#DCF1F6"/>
      <stop offset="1" stop-color="#C2E6EF"/>
    </linearGradient>
  </defs>
  <rect x="0" y="0" width="{$w}" height="{$h}" fill="url(#{$id}-sea)"/>
  <path d="{$islandPath}" fill="#EAF6EE" stroke="#9FC4B4" stroke-width="2.5" stroke-linejoin="round"/>
  <path d="{$routePath}" fill="none" stroke="#12A4C9" stroke-width="4.5" stroke-dasharray="1 13" stroke-linecap="round"/>
  {$markers}
  <g>
    <rect x="{$inX}" y="{$inY}" width="{$inW}" height="{$inH}" rx="12" fill="#fff" fill-opacity="0.82" stroke="#9FC4B4" stroke-width="1.5"/>
    <path d="{$natPath}" fill="#D2E7EF" stroke="#9FC4B4" stroke-width="1.5" stroke-linejoin="round"/>
    <circle cx="{$dotX}" cy="{$dotY}" r="7" fill="#12A4C9" stroke="#fff" stroke-width="2"/>
  </g>
</svg>
SVG;
}

/* ---------------- Sailing route accessors ---------------- */

/** All sailing routes (from content.php), keyed by slug. */
function get_routes(): array
{
    return get_sailing_routes();
}

/** One sailing route by slug, or null. */
function get_route(string $slug): ?array
{
    return get_sailing_routes()[$slug] ?? null;
}
