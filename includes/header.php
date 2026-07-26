<?php
require_once __DIR__ . '/functions.php';
$currentPage = basename($_SERVER['PHP_SELF']);
$navCities = get_cities();

// ---- SEO values (pages may set these before including the header) ----
$seoTitle   = isset($pageTitle) ? $pageTitle . ' | BoatRent Cyprus' : 'BoatRent Cyprus | Yacht & Boat Rentals';
$seoDesc    = $pageDescription ?? 'Rent yachts, catamarans and speedboats across Cyprus — Limassol, Paphos, Larnaca, Ayia Napa, Protaras and Latsi. Browse the fleet and inquire in minutes.';
$seoCanon   = $canonical ?? current_url();
$seoRobots  = $robots ?? (site_is_live() ? 'index, follow' : 'noindex, nofollow');
$seoOgType  = $ogType ?? 'website';
$seoImage   = $pageImage ?? (base_url() . '/images/princess-30m/image-4.webp');
// Social/scraper image URLs must be absolute — promote root-relative paths.
if ($seoImage !== '' && $seoImage[0] === '/') {
    $seoImage = base_url() . $seoImage;
}
$seoCanonPath = parse_url($seoCanon, PHP_URL_PATH) ?: '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($seoTitle); ?></title>
<meta name="description" content="<?php echo e($seoDesc); ?>">
<?php if (!empty($pageKeywords)): ?><meta name="keywords" content="<?php echo e($pageKeywords); ?>">
<?php endif; ?>
<meta name="robots" content="<?php echo e($seoRobots); ?>">
<link rel="canonical" href="<?php echo e($seoCanon); ?>">

<!-- hreflang: only locales flagged live in site_locales() are emitted, so we
     never point crawlers at pages that don't exist yet. ru/el flip on once
     their localized routes are built. -->
<?php foreach (site_locales() as $lc): if (empty($lc['live'])) continue; ?>
<link rel="alternate" hreflang="<?php echo e($lc['hreflang']); ?>" href="<?php echo e(base_url() . $lc['prefix'] . ($seoCanonPath === '/' ? '/' : $seoCanonPath)); ?>">
<?php endforeach; ?>
<link rel="alternate" hreflang="x-default" href="<?php echo e(base_url() . ($seoCanonPath === '/' ? '/' : $seoCanonPath)); ?>">

<!-- Open Graph -->
<meta property="og:type" content="<?php echo e($seoOgType); ?>">
<meta property="og:site_name" content="BoatRent Cyprus">
<meta property="og:locale" content="en_GB">
<meta property="og:title" content="<?php echo e($seoTitle); ?>">
<meta property="og:description" content="<?php echo e($seoDesc); ?>">
<meta property="og:url" content="<?php echo e($seoCanon); ?>">
<meta property="og:image" content="<?php echo e($seoImage); ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo e($seoTitle); ?>">
<meta name="twitter:description" content="<?php echo e($seoDesc); ?>">
<meta name="twitter:image" content="<?php echo e($seoImage); ?>">

<!-- Site-wide business schema (LocalBusiness / TravelAgency) -->
<?php
$biz = business();
$dayNames = ['Mo'=>'Monday','Tu'=>'Tuesday','We'=>'Wednesday','Th'=>'Thursday','Fr'=>'Friday','Sa'=>'Saturday','Su'=>'Sunday'];
echo json_ld([
    '@context' => 'https://schema.org',
    '@type'    => ['TravelAgency', 'LocalBusiness'],
    '@id'      => base_url() . '/#business',
    'name'     => $biz['name'],
    'legalName'=> $biz['legalName'],
    'description' => 'Yacht, catamaran and speedboat charters across Cyprus — Limassol, Paphos, Larnaca, Ayia Napa, Protaras and Latsi.',
    'url'      => base_url() . '/',
    'logo'     => base_url() . '/images/logo.png',
    'image'    => $seoImage,
    'telephone'=> $biz['phone'],
    'email'    => $biz['email'],
    'priceRange' => $biz['priceRange'],
    'currenciesAccepted' => 'EUR',
    'areaServed' => ['@type' => 'Country', 'name' => 'Cyprus'],
    'address'  => [
        '@type' => 'PostalAddress',
        'streetAddress'   => $biz['street'],
        'addressLocality' => $biz['locality'],
        'addressRegion'   => $biz['region'],
        'postalCode'      => $biz['postcode'],
        'addressCountry'  => $biz['country'],
    ],
    'geo' => [
        '@type' => 'GeoCoordinates',
        'latitude'  => $biz['lat'],
        'longitude' => $biz['lng'],
    ],
    'openingHoursSpecification' => [
        '@type'     => 'OpeningHoursSpecification',
        'dayOfWeek' => array_values(array_map(fn($d) => $dayNames[$d], $biz['hours'])),
        'opens'     => $biz['opens'],
        'closes'    => $biz['closes'],
    ],
    'sameAs'    => $biz['sameAs'],
]); ?>
<?php
if (!empty($structuredData)) {
    echo is_array($structuredData) ? json_ld($structuredData) : $structuredData;
}
?>
<!-- Self-hosted fonts (no third-party render-blocking requests) -->
<link rel="preload" href="/assets/fonts/playfair-display-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/assets/fonts/plus-jakarta-sans-latin.woff2" as="font" type="font/woff2" crossorigin>
<?php if (!empty($heroPreload)): // pages set $heroPreload to their LCP image ?>
<link rel="preload" href="<?php echo e($heroPreload); ?>" as="image" fetchpriority="high">
<?php endif; ?>

<!-- Compiled Tailwind (built from tailwind.config.js via `npm run build:css`) -->
<link rel="stylesheet" href="/css/tailwind.css">
<link rel="stylesheet" href="/css/style.css">
</head>
<body class="bg-brand-sand text-brand-ink font-body antialiased">

<a href="#main" class="skip-link">Skip to content</a>

<header id="site-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <nav class="mt-4 flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-brand-ink/85 backdrop-blur-md px-5 py-3 shadow-lg shadow-brand-ink/20" aria-label="Primary">
      <a href="/" class="flex items-center gap-2.5 cursor-pointer group">
        <span class="text-brand-gold">
          <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 17c1.5 1 3 1 4.5 0s3-1 4.5 0 3 1 4.5 0 3-1 4.5 0"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 14l7-9 7 9M12 5v9"/>
          </svg>
        </span>
        <span class="font-display text-xl font-semibold tracking-wide text-white leading-none">BoatRent<span class="text-brand-gold">.</span>Cyprus</span>
      </a>

      <ul class="hidden lg:flex items-center gap-7 text-sm font-medium text-white/80">
        <li><a href="/" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer <?php echo $currentPage === 'index.php' ? 'text-brand-gold' : ''; ?>">Home</a></li>
        <li class="relative group">
          <button type="button" class="flex items-center gap-1 hover:text-brand-gold transition-colors duration-200 cursor-pointer <?php echo $currentPage === 'city.php' ? 'text-brand-gold' : ''; ?>">
            Destinations
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <div class="absolute left-1/2 -translate-x-1/2 top-full pt-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
            <div class="w-56 rounded-2xl border border-brand-navy/10 bg-white p-2 shadow-xl">
              <?php foreach ($navCities as $c): ?>
              <a href="/<?php echo e($c['slug']); ?>" class="block px-3 py-2 rounded-lg text-brand-navy hover:bg-brand-foam hover:text-brand-aquaD transition-colors duration-200 cursor-pointer text-sm"><?php echo e($c['name']); ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </li>
        <li><a href="/boats" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer <?php echo $currentPage === 'boats.php' ? 'text-brand-gold' : ''; ?>">Fleet</a></li>
        <li><a href="/routes" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer <?php echo in_array($currentPage, ['routes.php', 'route.php'], true) ? 'text-brand-gold' : ''; ?>">Routes</a></li>
        <li><a href="/blog" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer <?php echo in_array($currentPage, ['blog.php', 'blog-post.php'], true) ? 'text-brand-gold' : ''; ?>">Blog</a></li>
        <li><a href="/about" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer <?php echo $currentPage === 'about.php' ? 'text-brand-gold' : ''; ?>">About</a></li>
        <li><a href="/faq" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer <?php echo $currentPage === 'faq.php' ? 'text-brand-gold' : ''; ?>">FAQ</a></li>
        <li><a href="/contact" class="hover:text-brand-gold transition-colors duration-200 cursor-pointer <?php echo $currentPage === 'contact.php' ? 'text-brand-gold' : ''; ?>">Contact</a></li>
      </ul>

      <div class="hidden lg:flex items-center gap-3">
        <a href="/boats" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink text-sm font-semibold px-5 py-2.5 rounded-full transition-colors duration-200 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
          Browse Boats
        </a>
      </div>

      <button id="menu-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu" class="lg:hidden h-11 w-11 flex items-center justify-center rounded-full text-white hover:bg-white/10 transition-colors duration-200 cursor-pointer">
        <svg id="icon-burger" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </nav>

    <div id="mobile-menu" class="lg:hidden hidden mt-2 rounded-2xl border border-white/10 bg-brand-ink/95 backdrop-blur-md px-4 py-4 shadow-xl">
      <ul class="flex flex-col gap-1 text-white/90 font-medium">
        <li><a href="/" class="block py-2.5 px-2 rounded-lg hover:bg-white/10 cursor-pointer">Home</a></li>
        <li class="py-1 px-2 text-xs uppercase tracking-wide text-white/40">Destinations</li>
        <?php foreach ($navCities as $c): ?>
        <li><a href="/<?php echo e($c['slug']); ?>" class="block py-2.5 px-4 rounded-lg hover:bg-white/10 cursor-pointer text-sm"><?php echo e($c['name']); ?></a></li>
        <?php endforeach; ?>
        <li><a href="/boats" class="block py-2.5 px-2 rounded-lg hover:bg-white/10 cursor-pointer">Fleet</a></li>
        <li><a href="/routes" class="block py-2.5 px-2 rounded-lg hover:bg-white/10 cursor-pointer">Routes</a></li>
        <li><a href="/blog" class="block py-2.5 px-2 rounded-lg hover:bg-white/10 cursor-pointer">Blog</a></li>
        <li><a href="/about" class="block py-2.5 px-2 rounded-lg hover:bg-white/10 cursor-pointer">About</a></li>
        <li><a href="/faq" class="block py-2.5 px-2 rounded-lg hover:bg-white/10 cursor-pointer">FAQ</a></li>
        <li><a href="/contact" class="block py-2.5 px-2 rounded-lg hover:bg-white/10 cursor-pointer">Contact</a></li>
        <li class="pt-2"><a href="/boats" class="block text-center bg-brand-gold text-brand-ink font-semibold py-3 rounded-full cursor-pointer">Browse Boats</a></li>
      </ul>
    </div>
  </div>
</header>

<main id="main">
