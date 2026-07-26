<?php
require_once __DIR__ . '/includes/functions.php';

$slug  = trim($_GET['slug'] ?? '');
$route = $slug !== '' ? get_route($slug) : null;

if (!$route) {
    http_response_code(404);
    $pageTitle = 'Route not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="pt-40 pb-32 px-6 text-center"><h1 class="font-display text-4xl font-bold text-brand-ink mb-4">Route not found</h1><p class="text-brand-navy/60 mb-8">That sailing route isn\'t listed.</p><a href="/routes" class="inline-flex items-center gap-2 bg-brand-gold text-brand-ink font-semibold px-6 py-3 rounded-full cursor-pointer">All sailing routes</a></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$city = get_city($route['city']);
$cityName = $city['name'] ?? ucfirst($route['city']);
$routeUrl = base_url() . '/routes/' . $slug;

$pageTitle = $route['title'] . ' — Cyprus Boat Trip';
$pageDescription = $route['title'] . ': a ' . strtolower($route['duration']) . ' sailing route from ' . $route['depart'] . '. ' . strip_tags($route['intro'][0]);
$pageKeywords = strtolower($route['title'] . ', boat trip ' . $cityName . ', ' . $cityName . ' sailing route, yacht charter ' . $cityName);
$pageImage = $route['image'];
$canonical = $routeUrl;

// TouristTrip + itinerary, plus a breadcrumb — emitted together in <head>.
$tripItinerary = [];
$pos = 1;
foreach ($route['stops'] as $s) {
    $tripItinerary[] = [
        '@type'    => 'ListItem',
        'position' => $pos++,
        'item'     => [
            '@type'       => 'TouristAttraction',
            'name'        => $s['name'],
            'description' => $s['text'],
        ],
    ];
}
$structuredData  = json_ld([
    '@context'    => 'https://schema.org',
    '@type'       => 'TouristTrip',
    'name'        => $route['title'],
    'description' => strip_tags(implode(' ', $route['intro'])),
    'url'         => $routeUrl,
    'image'       => base_url() . $route['image'],
    'touristType' => $route['best_for'],
    'itinerary'   => [
        '@type'           => 'ItemList',
        'itemListElement' => $tripItinerary,
    ],
    'provider' => ['@type' => 'TravelAgency', 'name' => 'BoatRent Cyprus', '@id' => base_url() . '/#business'],
]);
$structuredData .= json_ld([
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => base_url() . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Sailing Routes', 'item' => base_url() . '/routes'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $route['title'], 'item' => $routeUrl],
    ],
]);

include __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="relative h-[46vh] min-h-[340px] flex items-end overflow-hidden">
  <img src="<?php echo e($route['image']); ?>" alt="<?php echo e($route['title']); ?>, Cyprus" width="1600" height="900" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover">
  <div class="absolute inset-0 hero-overlay"></div>
  <div class="relative z-10 max-w-7xl mx-auto px-6 pb-10 w-full">
    <nav class="reveal text-white/70 text-sm mb-4 flex items-center gap-2" aria-label="Breadcrumb">
      <a href="/" class="hover:text-brand-gold cursor-pointer">Home</a><span>/</span>
      <a href="/routes" class="hover:text-brand-gold cursor-pointer">Sailing Routes</a><span>/</span>
      <span class="text-white"><?php echo e($cityName); ?></span>
    </nav>
    <p class="reveal text-brand-goldL font-medium uppercase tracking-[0.3em] text-xs mb-3">Departs <?php echo e($route['depart']); ?></p>
    <h1 class="reveal font-display text-3xl sm:text-5xl font-bold text-white max-w-3xl"><?php echo e($route['title']); ?></h1>
  </div>
</section>

<!-- QUICK FACTS -->
<section class="bg-brand-ink text-white px-6">
  <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 divide-x divide-white/10">
    <?php
    $facts = [
      ['Duration', $route['duration']],
      ['Distance', $route['distance']],
      ['Best for', $route['best_for']],
      ['Ideal boats', $route['boats']],
    ];
    foreach ($facts as [$label, $val]): ?>
    <div class="py-6 px-4 sm:px-6">
      <p class="text-white/50 text-xs uppercase tracking-wide mb-1"><?php echo e($label); ?></p>
      <p class="font-medium text-sm sm:text-base leading-snug"><?php echo e($val); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- MAP + ITINERARY -->
<section class="bg-brand-sand py-16 sm:py-20 px-6">
  <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-10 items-start">
    <!-- Map -->
    <div class="reveal lg:sticky lg:top-24 rounded-3xl overflow-hidden border border-brand-navy/10 shadow-sm bg-white">
      <?php echo render_route_map($route['stops'], 'route-' . $slug); ?>
    </div>
    <!-- Narrative + stops -->
    <div class="reveal">
      <div class="space-y-4 text-brand-navy/75 leading-relaxed mb-8">
        <?php foreach ($route['intro'] as $para): ?>
        <p><?php echo e($para); ?></p>
        <?php endforeach; ?>
      </div>
      <h2 class="font-display text-2xl font-bold text-brand-ink mb-5">The route, stop by stop</h2>
      <ol class="relative border-l-2 border-brand-foam ml-3 space-y-6">
        <?php foreach ($route['stops'] as $i => $s): ?>
        <li class="relative pl-9">
          <span class="absolute -left-[15px] top-0 h-7 w-7 rounded-full bg-brand-ink text-white text-xs font-bold flex items-center justify-center ring-4 ring-brand-sand"><?php echo $i + 1; ?></span>
          <h3 class="font-display text-lg font-semibold text-brand-ink"><?php echo e($s['name']); ?></h3>
          <p class="text-brand-navy/70 text-sm mt-1 leading-relaxed"><?php echo e($s['text']); ?></p>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="bg-white py-16 px-6">
  <div class="max-w-4xl mx-auto text-center reveal">
    <h2 class="font-display text-3xl font-bold text-brand-ink mb-3">Sail this route from <?php echo e($cityName); ?></h2>
    <p class="text-brand-navy/70 mb-8 max-w-xl mx-auto">Browse the boats that run this stretch of coast — <?php echo e(strtolower($route['boats'])); ?> — and send one inquiry. No booking fees, fast reply.</p>
    <div class="flex flex-wrap items-center justify-center gap-3">
      <a href="/<?php echo e($route['city']); ?>" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3 rounded-full transition-colors duration-200 cursor-pointer">
        See <?php echo e($cityName); ?> boats
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
      <a href="/contact" class="inline-flex items-center gap-2 border border-brand-navy/15 hover:bg-brand-sand text-brand-navy font-semibold px-6 py-3 rounded-full transition-colors duration-200 cursor-pointer">Plan a custom trip</a>
    </div>
  </div>
</section>

<!-- OTHER ROUTES -->
<section class="bg-brand-sand py-14 px-6">
  <div class="max-w-7xl mx-auto">
    <h2 class="font-display text-2xl font-semibold text-brand-ink mb-6 reveal">More sailing routes</h2>
    <div class="flex flex-wrap gap-3 reveal">
      <?php foreach (get_sailing_routes() as $s => $r): if ($s === $slug) continue; ?>
      <a href="/routes/<?php echo e($s); ?>" class="inline-flex items-center gap-2 bg-white hover:bg-brand-foam border border-brand-navy/10 text-brand-navy font-medium text-sm px-5 py-2.5 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
        <?php echo e($r['title']); ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
