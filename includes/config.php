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
