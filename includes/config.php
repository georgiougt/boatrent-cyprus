<?php
/**
 * Site-wide configuration.
 *
 * LIVE_HOST is your real production domain. Until the site is served from that
 * host (e.g. while on a Hostinger temporary *.hostingersite.com domain), every
 * page is marked noindex and robots.txt disallows crawling — so the temp domain
 * never gets indexed by Google. When you point the real domain here, indexing
 * switches on automatically.
 *
 * 👉 When you go live, set LIVE_HOST to your real domain (no scheme, no slash).
 */
const LIVE_HOST = 'boatrentcyprus.com';

/** True only when the current request is served from the real production domain. */
function site_is_live(): bool
{
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    return $host === LIVE_HOST || $host === 'www.' . LIVE_HOST;
}

/**
 * Central business details (NAP) used for LocalBusiness schema, contact CTAs
 * and social links.
 *
 * ⚠️ PLACEHOLDERS — replace every value marked "TODO" with the real business
 * details before go-live. These feed structured data that Google shows in
 * search results, so accuracy matters.
 */
function business(): array
{
    return [
        'name'      => 'BoatRent Cyprus',
        'legalName' => 'BoatRent Cyprus',                       // TODO: registered company name
        'phone'     => '+357 25 000 000',                       // TODO: real phone
        'whatsapp'  => '35799000000',                           // TODO: real WhatsApp number (digits only)
        'email'     => 'hello@boatrentcyprus.com',              // TODO: real inbox
        'priceRange'=> '€€€',
        'street'    => 'Limassol Marina',                       // TODO: real street address
        'locality'  => 'Limassol',
        'region'    => 'Limassol',
        'postcode'  => '3601',                                  // TODO: real postcode
        'country'   => 'CY',
        'lat'       => 34.6720,                                 // TODO: real coordinates (currently Limassol Marina)
        'lng'       => 33.0430,
        // Public profiles for schema `sameAs`. TODO: replace with real URLs.
        'sameAs'    => [
            'https://instagram.com/',                           // TODO
            'https://facebook.com/',                            // TODO
        ],
        // Open 7 days, 08:00–22:00 (matches typical charter-desk hours).
        'hours'     => ['Mo','Tu','We','Th','Fr','Sa','Su'],
        'opens'     => '08:00',
        'closes'    => '22:00',
    ];
}

/**
 * Legal identity of the business, shown on the privacy, cookie and terms pages.
 *
 * The EU e-Commerce Directive (and Cyprus Law 156(I)/2004 implementing it)
 * requires a trader to publish its registered name, geographic address,
 * registration and VAT numbers in a directly accessible form — the footer's
 * trading name is not enough on its own.
 *
 * ⚠️ EVERY "TODO" BELOW MUST BE FILLED IN BEFORE GO-LIVE. While any of them is
 * still a placeholder the legal pages show a warning banner on non-production
 * hosts (see legal_entity_incomplete()).
 */
function legal_entity(): array
{
    return [
        'registeredName' => '',              // TODO: e.g. "Boatrent Cyprus Ltd"
        'regNumber'      => '',              // TODO: Registrar of Companies number, e.g. HE 123456
        'vatNumber'      => '',              // TODO: VAT number, e.g. CY10123456X — or '' if not registered
        'address'        => '',              // TODO: full registered address incl. postcode
        'email'          => 'hello@boatrentcyprus.com',   // TODO: monitored inbox
        'privacyEmail'   => 'hello@boatrentcyprus.com',   // TODO: inbox for data-protection requests
        'phone'          => '+357 25 000 000',            // TODO: real phone
        // Optional: charter/travel licence details, if the business holds one.
        'licence'        => '',              // TODO: e.g. "Deputy Ministry of Tourism licence no. 1234"
    ];
}

/** Which legal_entity() fields are still placeholders. Empty array = ready. */
function legal_entity_incomplete(): array
{
    $required = ['registeredName', 'regNumber', 'address'];
    $missing = [];
    foreach ($required as $key) {
        if (trim((string) (legal_entity()[$key] ?? '')) === '') {
            $missing[] = $key;
        }
    }
    return $missing;
}

/**
 * Date the legal pages were last substantively revised. Bump it by hand
 * whenever the wording changes — GDPR Art 12 expects users to be able to see
 * which version they are reading.
 */
const LEGAL_LAST_UPDATED = '2026-09-07';

/**
 * Guest reels — the biggest video a customer may upload as a testimonial.
 *
 * Kept deliberately low: shared hosting can't transcode, so whatever a guest
 * uploads is byte-for-byte what every visitor then downloads. 25 MB comfortably
 * fits a trimmed 1080p clip of ~30 seconds; raising it mostly lets raw 4K phone
 * footage through, which is 10x heavier than it needs to be. See the ffmpeg
 * recipe in README.md for re-compressing reels before featuring them.
 *
 * ⚠️ PHP itself caps uploads via `upload_max_filesize` / `post_max_size`. The
 * bundled .user.ini raises those on PHP-FPM/CGI hosts (Hostinger included);
 * reel_max_bytes() always uses whichever limit is lowest, so the number shown
 * on the upload form is the number the server will actually accept.
 */
const REEL_MAX_MB = 25;

/** Video formats accepted from guests: mime => file extension. */
function reel_allowed_types(): array
{
    return [
        'video/mp4'       => 'mp4',
        'video/quicktime' => 'mov',
        'video/webm'      => 'webm',
        'video/x-m4v'     => 'm4v',
    ];
}

/**
 * Site locales for hreflang. English is live; Russian & Greek are planned
 * (large Cyprus charter markets) — add their prefixes here and create the
 * localized routes to switch hreflang on. Only locales flagged `live` are
 * emitted, so we never point search engines at pages that 404 yet.
 *
 * [code => [hreflang, url prefix, live?]]
 */
function site_locales(): array
{
    return [
        'en' => ['hreflang' => 'en',    'prefix' => '',    'live' => true],
        'ru' => ['hreflang' => 'ru',    'prefix' => '/ru', 'live' => false],
        'el' => ['hreflang' => 'el-CY', 'prefix' => '/el', 'live' => false],
    ];
}
