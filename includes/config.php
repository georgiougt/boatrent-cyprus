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
