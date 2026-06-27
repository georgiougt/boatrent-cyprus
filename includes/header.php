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
$seoImage   = $pageImage ?? 'https://images.unsplash.com/photo-1567899378494-47b22a2ae96a?auto=format&fit=crop&w=1200&q=80';
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

<!-- Site-wide organisation schema -->
<?php echo json_ld([
    '@context' => 'https://schema.org',
    '@type'    => 'TravelAgency',
    'name'     => 'BoatRent Cyprus',
    'description' => 'Marketplace for yacht, catamaran and speedboat rentals across Cyprus.',
    'url'      => base_url() . '/',
    'logo'     => base_url() . '/images/logo.png',
    'image'    => $seoImage,
    'areaServed' => 'Cyprus',
    'address'  => [
        '@type' => 'PostalAddress',
        'addressLocality' => 'Limassol',
        'addressCountry'  => 'CY',
    ],
    'telephone' => '+357 25 000 000',
    'email'     => 'hello@boatrentcyprus.com',
    'sameAs'    => ['https://instagram.com', 'https://facebook.com'],
]); ?>
<?php
if (!empty($structuredData)) {
    echo is_array($structuredData) ? json_ld($structuredData) : $structuredData;
}
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: {
            ink:   '#0D1A33',
            navy:  '#16395A',
            navy2: '#1F567C',
            gold:  '#12A4C9',
            goldL: '#46C2E0',
            aqua:  '#1ECEB6',
            aquaD: '#0C7A6E',
            sand:  '#E7F7FA',
            foam:  '#D2E7EF',
          }
        },
        fontFamily: {
          display: ['"Playfair Display"', 'serif'],
          body: ['"Plus Jakarta Sans"', 'sans-serif'],
        },
        keyframes: {
          fadeUp: { '0%': { opacity: 0, transform: 'translateY(34px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
          floaty: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
        },
        animation: {
          'fade-up': 'fadeUp 0.7s ease-out forwards',
          floaty: 'floaty 6s ease-in-out infinite',
        }
      }
    }
  }
</script>
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
