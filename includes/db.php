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
        ['Limassol', 'limassol', 'Cyprus\' cosmopolitan marina capital', 'Home to the island\'s largest marina, Limassol is the launchpad for luxury day cruises along the southern coast.', 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1200&q=80'],
        ['Paphos', 'paphos', 'Mythical coves & golden sunsets', 'Sail the birthplace of Aphrodite, with crystal sea caves and quiet swimming bays just offshore.', 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=1200&q=80'],
        ['Larnaca', 'larnaca', 'Relaxed shores & shipwreck dives', 'Calm waters and the famous Zenobia wreck make Larnaca a favourite for families and divers alike.', 'https://images.unsplash.com/photo-1493558103817-58b2924bce98?auto=format&fit=crop&w=1200&q=80'],
        ['Ayia Napa', 'ayia-napa', 'Turquoise bays & party cruises', 'The vibrant heart of the southeast — think neon-blue lagoons, sea caves and sunset party boats.', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80'],
        ['Protaras', 'protaras', 'Calm coves & family days out', 'Sheltered, shallow bays like Fig Tree make Protaras ideal for easy, relaxed days on the water.', 'https://images.unsplash.com/photo-1473116763249-2faaef81ccda?auto=format&fit=crop&w=1200&q=80'],
        ['Latsi', 'latsi', 'Untouched Akamas wilderness', 'Cruise from the quiet harbour of Latsi to the Blue Lagoon and the wild Akamas peninsula.', 'https://images.unsplash.com/photo-1500627964684-141351970a7f?auto=format&fit=crop&w=1200&q=80'],
    ];
    $cityStmt = $pdo->prepare('INSERT INTO cities (name, slug, tagline, description, image_url) VALUES (?, ?, ?, ?, ?)');
    foreach ($cities as $c) {
        $cityStmt->execute($c);
    }
    $cityIds = [];
    foreach ($pdo->query('SELECT id, slug FROM cities') as $row) {
        $cityIds[$row['slug']] = (int) $row['id'];
    }

    // image pools per category (Unsplash)
    // All IDs below verified to render as boats/sea in-browser.
    $img = [
        'yacht'      => 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1100&q=80',
        'yacht2'     => 'https://images.unsplash.com/photo-1605281317010-fe5ffe798166?auto=format&fit=crop&w=1100&q=80',
        'catamaran'  => 'https://images.unsplash.com/photo-1500627964684-141351970a7f?auto=format&fit=crop&w=1100&q=80',
        'sailing'    => 'https://images.unsplash.com/photo-1540946485063-a40da27545f8?auto=format&fit=crop&w=1100&q=80',
        'speedboat'  => 'https://images.unsplash.com/photo-1593351415075-3bac9f45c877?auto=format&fit=crop&w=1100&q=80',
        'fishing'    => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=1100&q=80',
    ];
    $gallery = json_encode([
        'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1100&q=80',
        'https://images.unsplash.com/photo-1605281317010-fe5ffe798166?auto=format&fit=crop&w=1100&q=80',
        'https://images.unsplash.com/photo-1540946485063-a40da27545f8?auto=format&fit=crop&w=1100&q=80',
    ]);

    $boats = [
        // name, city slug, type, capacity, length, cabins, year, crewed, price_hour, price_day, featured, image, features
        ['Azure Princess', 'limassol', 'Luxury Yacht', 12, 24.0, 4, 2021, 1, 450, 3200, 1, $img['yacht'], ['Captain & crew', 'Sun deck & jacuzzi', 'Water toys', 'Premium sound system', 'Air conditioning', 'Catering available']],
        ['Sea Breeze 38', 'limassol', 'Motor Yacht', 10, 11.6, 2, 2019, 1, 220, 1500, 1, $img['yacht2'], ['Skipper included', 'Snorkelling gear', 'Bluetooth audio', 'Shaded cockpit', 'Fridge & cooler']],
        ['Mediterraneo Cat', 'limassol', 'Catamaran', 16, 13.5, 4, 2020, 1, 280, 1900, 0, $img['catamaran'], ['Stable twin-hull', 'Large net lounging', 'Freshwater shower', 'Paddle boards', 'BBQ on board']],
        ['Wind Dancer', 'paphos', 'Sailing Yacht', 8, 12.2, 3, 2018, 1, 160, 1100, 1, $img['sailing'], ['Skippered sailing', 'Sea-cave routes', 'Snorkelling stop', 'Cool box', 'Bimini shade']],
        ['Aphrodite Pearl', 'paphos', 'Luxury Yacht', 10, 18.0, 3, 2022, 1, 380, 2600, 0, $img['yacht'], ['Crew of 2', 'Sunset cruises', 'Premium bar', 'Swim platform', 'Air conditioning']],
        ['Coral Chaser', 'paphos', 'Speedboat', 7, 8.5, 0, 2021, 0, 110, 720, 0, $img['speedboat'], ['Self-drive option', 'Fast & agile', 'Bimini top', 'Bluetooth speaker', 'Cooler box']],
        ['Zenobia Star', 'larnaca', 'Motor Yacht', 9, 10.8, 2, 2017, 1, 190, 1300, 1, $img['yacht2'], ['Skipper included', 'Dive-site trips', 'Snorkelling gear', 'Shaded seating', 'Fridge']],
        ['Salt Marina', 'larnaca', 'Catamaran', 14, 12.0, 3, 2019, 1, 240, 1650, 0, $img['catamaran'], ['Family friendly', 'Trampoline nets', 'Freshwater shower', 'SUP boards', 'Sound system']],
        ['Blue Lagoon Express', 'ayia-napa', 'Speedboat', 8, 9.2, 0, 2022, 0, 130, 850, 1, $img['speedboat'], ['Self-drive option', 'Reach hidden caves', 'Bluetooth audio', 'Cooler', 'Bimini shade']],
        ['Napa Sunset', 'ayia-napa', 'Luxury Yacht', 12, 20.0, 4, 2021, 1, 420, 2900, 1, $img['yacht'], ['Captain & crew', 'Party-ready deck', 'Premium sound', 'Water toys', 'Air conditioning', 'Bar service']],
        ['Lagoon Hopper', 'ayia-napa', 'Catamaran', 18, 14.0, 4, 2020, 1, 300, 2100, 0, $img['catamaran'], ['Big group friendly', 'Shaded saloon', 'Freshwater shower', 'Snorkelling gear', 'BBQ']],
        ['Fig Tree Breeze', 'protaras', 'Sailing Yacht', 8, 11.5, 2, 2018, 1, 150, 1050, 1, $img['sailing'], ['Skippered sailing', 'Calm-bay cruising', 'Snorkelling stop', 'Cool box', 'Bimini shade']],
        ['Protaras Pearl', 'protaras', 'Motor Yacht', 10, 12.5, 2, 2020, 1, 210, 1450, 0, $img['yacht2'], ['Skipper included', 'Family day trips', 'Swim ladder', 'Fridge', 'Bluetooth audio']],
        ['Akamas Explorer', 'latsi', 'Motor Yacht', 11, 13.0, 3, 2019, 1, 230, 1600, 1, $img['yacht2'], ['Blue Lagoon trips', 'Skipper included', 'Snorkelling gear', 'Shaded cockpit', 'Cooler & fridge']],
        ['Blue Lagoon Lady', 'latsi', 'Catamaran', 16, 13.8, 4, 2021, 1, 290, 1950, 0, $img['catamaran'], ['Stable twin-hull', 'Akamas coastline', 'Freshwater shower', 'Paddle boards', 'Sound system']],
        ['Latsi Angler', 'latsi', 'Fishing Boat', 6, 9.0, 1, 2017, 1, 120, 800, 0, $img['fishing'], ['Guided fishing', 'Rods & tackle', 'Skipper included', 'Cool box', 'Shaded cabin']],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO boats
            (name, slug, city_id, type, capacity, length_m, cabins, year, crewed, price_hour, price_day, description, features, image_url, gallery, featured, status)
        VALUES
            (:name, :slug, :city_id, :type, :capacity, :length_m, :cabins, :year, :crewed, :price_hour, :price_day, :description, :features, :image_url, :gallery, :featured, 'active')
    ");

    foreach ($boats as $b) {
        [$name, $citySlug, $type, $cap, $len, $cabins, $year, $crewed, $ph, $pd, $featured, $image, $features] = $b;
        $desc = "The {$name} is a {$year} {$type} based in " . ucfirst(str_replace('-', ' ', $citySlug)) .
            ", comfortably hosting up to {$cap} guests across {$len} metres. " .
            ($crewed ? "Comes fully crewed so you can sit back and enjoy the Cyprus coastline. " : "Available for self-drive (licence required) or with an optional skipper. ") .
            "Perfect for day charters, swimming stops in hidden bays, and unforgettable sunsets on the water.";
        $stmt->execute([
            ':name' => $name,
            ':slug' => slugify($name),
            ':city_id' => $cityIds[$citySlug],
            ':type' => $type,
            ':capacity' => $cap,
            ':length_m' => $len,
            ':cabins' => $cabins,
            ':year' => $year,
            ':crewed' => $crewed,
            ':price_hour' => $ph,
            ':price_day' => $pd,
            ':description' => $desc,
            ':features' => json_encode($features),
            ':image_url' => $image,
            ':gallery' => $gallery,
            ':featured' => $featured,
        ]);
    }

    // A couple of sample inquiries so the dashboard isn't empty on first look.
    $pdo->prepare("INSERT INTO inquiries (boat_id, boat_name, name, email, phone, city, date_from, date_to, guests, message, status)
        VALUES (1, 'Azure Princess', 'Daniel Hughes', 'daniel.h@example.com', '+44 7700 900123', 'Limassol', date('now','+10 day'), date('now','+10 day'), 8, 'Hi, we''d love a full-day charter with catering for a birthday. Is the date available?', 'new')")->execute();
    $pdo->prepare("INSERT INTO inquiries (boat_id, boat_name, name, email, phone, city, date_from, date_to, guests, message, status)
        VALUES (9, 'Blue Lagoon Express', 'Sofia Andreou', 'sofia.a@example.com', '+357 99 123456', 'Ayia Napa', date('now','+3 day'), date('now','+3 day'), 5, 'Looking to self-drive for a half day around the sea caves. Do we need a licence?', 'contacted')")->execute();
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}
