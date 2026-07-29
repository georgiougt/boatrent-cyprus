<?php
/**
 * Database bootstrap for BoatRent Cyprus.
 * Uses PDO SQLite — zero external setup. The database file lives in /data
 * and is created + seeded automatically on first run.
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0775, true);
    }

    $dbFile = $dataDir . '/boatrent.sqlite';
    $isNew  = !file_exists($dbFile);

    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    init_schema($pdo);
    if ($isNew) {
        seed_data($pdo);
    }

    return $pdo;
}

function init_schema(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cities (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            name        TEXT NOT NULL,
            slug        TEXT NOT NULL UNIQUE,
            tagline     TEXT,
            description TEXT,
            image_url   TEXT
        );

        CREATE TABLE IF NOT EXISTS boats (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            name          TEXT NOT NULL,
            slug          TEXT NOT NULL UNIQUE,
            city_id       INTEGER NOT NULL REFERENCES cities(id) ON DELETE CASCADE,
            type          TEXT NOT NULL,
            capacity      INTEGER NOT NULL DEFAULT 1,
            length_m      REAL,
            cabins        INTEGER DEFAULT 0,
            year          INTEGER,
            crewed        INTEGER NOT NULL DEFAULT 0,
            price_hour    REAL,
            price_day     REAL NOT NULL DEFAULT 0,
            description   TEXT,
            features      TEXT,            -- JSON array
            image_url     TEXT,
            gallery       TEXT,            -- JSON array
            featured      INTEGER NOT NULL DEFAULT 0,
            status        TEXT NOT NULL DEFAULT 'active',  -- active | hidden
            builder       TEXT,            -- shipyard / manufacturer
            speed         TEXT,            -- cruising speed, e.g. 28 Knots
            beam          TEXT,            -- hull beam, e.g. 16 ft
            price_label   TEXT,            -- headline price string, e.g. 8800 per day
            pricing       TEXT,            -- JSON map of rate options (half day, full day, weekly)
            price_note    TEXT,            -- fine print (APA, fuel, etc.)
            created_at    TEXT NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS inquiries (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            boat_id     INTEGER REFERENCES boats(id) ON DELETE SET NULL,
            boat_name   TEXT,
            name        TEXT NOT NULL,
            email       TEXT NOT NULL,
            phone       TEXT,
            city        TEXT,
            date_from   TEXT,
            date_to     TEXT,
            guests      INTEGER,
            message     TEXT,
            status      TEXT NOT NULL DEFAULT 'new',  -- new | contacted | closed
            created_at  TEXT NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS admins (
            id            INTEGER PRIMARY KEY AUTOINCREMENT,
            username      TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL
        );
    ");

    // Migrate older databases: add charter-yacht columns if they don't exist yet.
    $boatCols = array_column($pdo->query('PRAGMA table_info(boats)')->fetchAll(), 'name');
    $newCols = [
        'builder'     => 'TEXT',
        'speed'       => 'TEXT',
        'beam'        => 'TEXT',
        'price_label' => 'TEXT',
        'pricing'     => 'TEXT',
        'price_note'  => 'TEXT',
    ];
    foreach ($newCols as $col => $type) {
        if (!in_array($col, $boatCols, true)) {
            $pdo->exec("ALTER TABLE boats ADD COLUMN {$col} {$type}");
        }
    }

    // Ensure a default admin exists (username: admin / password: admin123).
    $count = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
    }
}

function seed_data(PDO $pdo): void
{
    $cities = [
        ['Limassol', 'limassol', 'Cyprus\' cosmopolitan marina capital', 'Home to the island\'s largest marina, Limassol is the launchpad for luxury day cruises along the southern coast.', '/assets/scenery/yacht-hero.webp'],
        ['Paphos', 'paphos', 'Mythical coves & golden sunsets', 'Sail the birthplace of Aphrodite, with crystal sea caves and quiet swimming bays just offshore.', '/assets/scenery/paphos.webp'],
        ['Larnaca', 'larnaca', 'Relaxed shores & shipwreck dives', 'Calm waters and the famous Zenobia wreck make Larnaca a favourite for families and divers alike.', '/assets/scenery/larnaca.webp'],
        ['Ayia Napa', 'ayia-napa', 'Turquoise bays & party cruises', 'The vibrant heart of the southeast — think neon-blue lagoons, sea caves and sunset party boats.', '/assets/scenery/ayia-napa.webp'],
        ['Protaras', 'protaras', 'Calm coves & family days out', 'Sheltered, shallow bays like Fig Tree make Protaras ideal for easy, relaxed days on the water.', '/assets/scenery/protaras.webp'],
        ['Latsi', 'latsi', 'Untouched Akamas wilderness', 'Cruise from the quiet harbour of Latsi to the Blue Lagoon and the wild Akamas peninsula.', '/assets/scenery/latsi.webp'],
    ];
    $cityStmt = $pdo->prepare('INSERT INTO cities (name, slug, tagline, description, image_url) VALUES (?, ?, ?, ?, ?)');
    foreach ($cities as $c) {
        $cityStmt->execute($c);
    }
    $cityIds = [];
    foreach ($pdo->query('SELECT id, slug FROM cities') as $row) {
        $cityIds[$row['slug']] = (int) $row['id'];
    }

    // Load the real charter fleet from charter_yachts.json (single source of truth).
    // Every vessel is a Limassol-based charter; fall back to the first city if needed.
    $homeCityId = $cityIds['limassol'] ?? (int) reset($cityIds);
    insert_charter_yachts($pdo, __DIR__ . '/../charter_yachts.json', $homeCityId);

    // A couple of sample inquiries so the dashboard isn't empty on first look.
    $pdo->prepare("INSERT INTO inquiries (boat_id, boat_name, name, email, phone, city, date_from, date_to, guests, message, status)
        VALUES (1, 'Princess 30M', 'Daniel Hughes', 'daniel.h@example.com', '+44 7700 900123', 'Limassol', date('now','+10 day'), date('now','+10 day'), 8, 'Hi, we''d love a full-day charter with catering for a birthday. Is the date available?', 'new')")->execute();
    $pdo->prepare("INSERT INTO inquiries (boat_id, boat_name, name, email, phone, city, date_from, date_to, guests, message, status)
        VALUES (2, 'Azimut 27 Grande', 'Sofia Andreou', 'sofia.a@example.com', '+357 99 123456', 'Limassol', date('now','+3 day'), date('now','+3 day'), 5, 'Looking to charter for a full day around the coast. Is catering available?', 'contacted')")->execute();
}

/**
 * Parse charter_yachts.json into normalised boat rows and insert them, replacing
 * any existing boats. Shared by first-run seeding and the standalone importer so
 * the JSON stays the one source of truth for the fleet.
 */
function insert_charter_yachts(PDO $pdo, string $jsonPath, int $cityId): int
{
    $raw = @file_get_contents($jsonPath);
    $yachts = $raw ? json_decode($raw, true) : null;
    if (!is_array($yachts)) {
        return 0;
    }

    // Flagship vessels to surface in the homepage "Featured" row.
    $featuredNames = [
        'Princess 30M', 'Princess 88', 'Falcon 86',
        'Private Yacht 110ft', 'Sunseeker Manhattan 56', 'Azimut 27 Grande',
    ];

    // Map city slugs -> ids so a record can name its own home port ("city" slug).
    // Records without a "city" fall back to the passed default (Limassol).
    $cityBySlug = [];
    foreach ($pdo->query('SELECT id, slug FROM cities') as $row) {
        $cityBySlug[$row['slug']] = (int) $row['id'];
    }

    $pdo->exec('DELETE FROM boats');

    $stmt = $pdo->prepare("
        INSERT INTO boats
            (name, slug, city_id, type, capacity, length_m, cabins, year, crewed,
             price_hour, price_day, description, features, image_url, gallery, featured,
             status, builder, speed, beam, price_label, pricing, price_note)
        VALUES
            (:name, :slug, :city_id, :type, :capacity, :length_m, :cabins, :year, :crewed,
             :price_hour, :price_day, :description, :features, :image_url, :gallery, :featured,
             'active', :builder, :speed, :beam, :price_label, :pricing, :price_note)
    ");

    $count = 0;
    foreach ($yachts as $y) {
        $specs   = $y['specs'] ?? [];
        $pricing = $y['detailedPricing'] ?? [];
        $crew    = cy_int($specs['crew'] ?? null);
        $stmt->execute([
            ':name'        => $y['name'],
            ':slug'        => $y['slug'] ?? slugify($y['name']),
            ':city_id'     => $cityBySlug[$y['city'] ?? ''] ?? $cityId,
            ':type'        => $y['type'] ?? 'Motor Yacht',
            ':capacity'    => cy_int($y['capacity'] ?? null) ?? 1,
            ':length_m'    => cy_length_m($y['length'] ?? null),
            ':cabins'      => cy_int($specs['cabins'] ?? null) ?? 0,
            ':year'        => cy_int($specs['year'] ?? null),
            ':crewed'      => $crew && $crew > 0 ? 1 : 0,
            ':price_hour'  => null,
            ':price_day'   => cy_day_rate($pricing),
            ':description' => $y['description'] ?? '',
            ':features'    => json_encode($y['features'] ?? []),
            ':image_url'   => $y['image'] ?? '',
            ':gallery'     => json_encode($y['gallery'] ?? []),
            ':featured'    => in_array($y['name'], $featuredNames, true) ? 1 : 0,
            ':builder'     => $specs['builder'] ?? null,
            ':speed'       => $y['speed'] ?? null,
            ':beam'        => $specs['beam'] ?? null,
            ':price_label' => $y['price'] ?? null,
            ':pricing'     => json_encode($pricing),
            ':price_note'  => $y['priceNote'] ?? null,
        ]);
        $count++;
    }
    return $count;
}

/** Extract the first integer from a spec string ("12 Guests" → 12, "" → null). */
function cy_int(?string $s): ?int
{
    if ($s !== null && preg_match('/\d+/', $s, $m)) {
        return (int) $m[0];
    }
    return null;
}

/** Convert a length string to metres ("100 ft" → 30.5, "27 meters" → 27.0). */
function cy_length_m(?string $s): ?float
{
    if (!$s || !preg_match('/([\d.]+)/', $s, $m)) {
        return null;
    }
    $v = (float) $m[1];
    if (stripos($s, 'ft') !== false || stripos($s, 'feet') !== false) {
        $v *= 0.3048;
    }
    return round($v, 1);
}

/**
 * Pick a representative "per day" rate from a pricing map for sorting/filtering.
 * Charter yachts use a "fullDay" key; the day-charter fleet lists hourly rates,
 * so fall back through a full day's worth of hours before giving up.
 */
function cy_day_rate(array $pricing): float
{
    foreach (['fullDay', '8 Hours', '24 Hours', 'Overnight', '7 Hours', '6 Hours'] as $key) {
        if (!empty($pricing[$key])) {
            return cy_price_num($pricing[$key]);
        }
    }
    return 0.0;
}

/** Parse a euro price string to a number ("€11,900" → 11900.0, "Upon Request" → 0). */
function cy_price_num(?string $s): float
{
    if (!$s || !preg_match('/([\d][\d,]*)/', $s, $m)) {
        return 0.0;
    }
    return (float) str_replace(',', '', $m[1]);
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}
