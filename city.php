<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$city = get_city($slug);
if (!$city) {
    http_response_code(404);
    $pageTitle = 'Destination not found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="pt-40 pb-32 px-6 text-center"><h1 class="font-display text-4xl font-bold text-brand-ink mb-4">Destination not found</h1><p class="text-brand-navy/60 mb-8">We don\'t have that town listed.</p><a href="/" class="inline-flex items-center gap-2 bg-brand-gold text-brand-ink font-semibold px-6 py-3 rounded-full cursor-pointer">Back home</a></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$cityName = $city['name'];
$pageTitle = 'Yacht Charter & Boat Rental in ' . $cityName . ' ' . date('Y');
$pageDescription = 'Rent a boat or yacht in ' . $cityName . ', Cyprus. Charter luxury yachts, catamarans, sailing boats and speedboats in ' . $cityName . ' — ' . rtrim($city['tagline'], '.') . '. Browse the fleet and inquire today.';
$pageKeywords = strtolower(
    'rent boat ' . $cityName . ', rent yacht ' . $cityName . ', charter yacht ' . $cityName . ', ' .
    $cityName . ' yacht charter, ' . $cityName . ' boat hire, boat rental ' . $cityName . ' cyprus, yacht hire ' . $cityName
);
$pageImage = $city['image_url'];
$canonical = base_url() . '/' . $city['slug'];
$cityContent = get_city_content()[$city['slug']] ?? null;

// The signature sailing route that departs this city (for a cross-link).
$cityRoute = null;
foreach (get_sailing_routes() as $rSlug => $r) {
    if ($r['city'] === $city['slug']) {
        $cityRoute = ['slug' => $rSlug] + $r;
        break;
    }
}

// Breadcrumb + (if we have editorial FAQs) a FAQPage — both emitted in <head>.
$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => base_url() . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Fleet', 'item' => base_url() . '/boats'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $city['name'], 'item' => base_url() . '/' . $city['slug']],
    ],
];
$structuredData = json_ld($breadcrumbSchema);
if ($cityContent && !empty($cityContent['faqs'])) {
    $structuredData .= json_ld([
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(fn($f) => [
            '@type' => 'Question',
            'name'  => $f['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
        ], $cityContent['faqs']),
    ]);
}

$filters = [
    'city_id' => (int) $city['id'],
    'type'    => $_GET['type'] ?? '',
    'sort'    => $_GET['sort'] ?? '',
];
$boats = get_boats($filters);
$types = db()->prepare("SELECT DISTINCT type FROM boats WHERE city_id = ? AND status='active' ORDER BY type");
$types->execute([(int) $city['id']]);
$cityTypes = $types->fetchAll(PDO::FETCH_COLUMN);

// Stats for the SEO bento (all active boats in this city, unfiltered)
$cityAllBoats  = get_boats(['city_id' => (int) $city['id']]);
$cityBoatCount = count($cityAllBoats);
$cityDayPrices = array_filter(array_map(fn($b) => (float) $b['price_day'], $cityAllBoats), fn($p) => $p > 0);
$cityPriceFrom = $cityDayPrices ? min($cityDayPrices) : 0;
$citySeo       = get_city_seo()[$city['slug']] ?? null;

$heroPreload = $city['image_url']; // LCP image
include __DIR__ . '/includes/header.php';
?>

<!-- CITY HERO -->
<section class="relative h-[60vh] min-h-[420px] flex items-end overflow-hidden">
  <img src="<?php echo e($city['image_url']); ?>" alt="<?php echo e($city['name']); ?> coastline, Cyprus" width="1600" height="900" fetchpriority="high" class="absolute inset-0 w-full h-full object-cover">
  <div class="absolute inset-0 hero-overlay"></div>
  <div class="relative z-10 max-w-7xl mx-auto px-6 pb-12 w-full">
    <nav class="reveal text-white/70 text-sm mb-4 flex items-center gap-2" aria-label="Breadcrumb">
      <a href="/" class="hover:text-brand-gold cursor-pointer">Home</a>
      <span>/</span>
      <a href="/boats" class="hover:text-brand-gold cursor-pointer">Fleet</a>
      <span>/</span>
      <span class="text-white"><?php echo e($city['name']); ?></span>
    </nav>
    <p class="reveal text-brand-goldL font-medium uppercase tracking-[0.3em] text-xs mb-3"><?php echo e($city['tagline']); ?></p>
    <h1 class="reveal font-display text-4xl sm:text-6xl font-bold text-white">Rent a Boat or Yacht in <?php echo e($city['name']); ?></h1>
  </div>
</section>

<!-- INTRO -->
<section class="bg-brand-sand py-12 px-6">
  <div class="max-w-3xl mx-auto text-center reveal">
    <p class="text-brand-navy/70 text-lg leading-relaxed"><span class="text-brand-ink font-medium">Rent a yacht, catamaran or speedboat in <?php echo e($city['name']); ?>, Cyprus.</span> <?php echo e($city['description']); ?></p>
  </div>
</section>

<!-- LISTINGS -->
<section class="bg-brand-sand pb-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <p class="text-brand-navy/70 text-sm"><span class="font-semibold text-brand-ink"><?php echo count($boats); ?></span> vessel<?php echo count($boats) === 1 ? '' : 's'; ?> in <?php echo e($city['name']); ?></p>
      <form method="get" action="/<?php echo e($city['slug']); ?>" class="flex items-center gap-2">
        <label for="ct" class="sr-only">Filter by type</label>
        <select id="ct" name="type" onchange="this.form.submit()" class="bg-white border border-brand-navy/10 rounded-full px-4 py-2 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
          <option value="">All vessel types</option>
          <?php foreach ($cityTypes as $t): ?>
          <option value="<?php echo e($t); ?>" <?php echo ($filters['type'] === $t) ? 'selected' : ''; ?>><?php echo e($t); ?></option>
          <?php endforeach; ?>
        </select>
        <label for="cs" class="sr-only">Sort</label>
        <select id="cs" name="sort" onchange="this.form.submit()" class="bg-white border border-brand-navy/10 rounded-full px-4 py-2 text-sm text-brand-ink focus:outline-none focus:ring-2 focus:ring-brand-aqua cursor-pointer">
          <option value="">Recommended</option>
          <option value="price_asc"  <?php echo $filters['sort'] === 'price_asc' ? 'selected' : ''; ?>>Price ↑</option>
          <option value="price_desc" <?php echo $filters['sort'] === 'price_desc' ? 'selected' : ''; ?>>Price ↓</option>
          <option value="capacity"   <?php echo $filters['sort'] === 'capacity' ? 'selected' : ''; ?>>Capacity</option>
        </select>
      </form>
    </div>

    <?php if ($boats): ?>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7 reveal-stagger">
      <?php foreach ($boats as $boat) { include __DIR__ . '/includes/boat-card.php'; } ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl border border-brand-navy/10 p-14 text-center">
      <h3 class="font-display text-xl font-semibold text-brand-ink mb-2">No vessels here yet</h3>
      <p class="text-brand-navy/60 mb-6">Try another destination or browse the full fleet.</p>
      <a href="/boats" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3 rounded-full cursor-pointer">Browse all boats</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($citySeo): ?>
<!-- SEO BENTO -->
<section class="bg-white py-20 px-6">
  <div class="max-w-7xl mx-auto">
    <div class="reveal max-w-2xl mb-10">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Why <?php echo e($city['name']); ?></p>
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-brand-ink">Renting a Boat in <?php echo e($city['name']); ?>, Cyprus</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5 reveal-stagger">

      <!-- Big image tile -->
      <div class="relative md:col-span-2 md:row-span-2 rounded-3xl overflow-hidden min-h-[300px] group">
        <img src="<?php echo e($citySeo['image']); ?>" alt="Rent a boat or yacht in <?php echo e($city['name']); ?>, Cyprus" loading="lazy" class="card-img absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-ink/90 via-brand-ink/25 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-7">
          <span class="inline-block text-brand-goldL text-xs font-semibold uppercase tracking-[0.2em] mb-2"><?php echo e($citySeo['best_for']); ?></span>
          <h3 class="font-display text-2xl sm:text-3xl font-bold text-white">Rent a yacht or boat in <?php echo e($city['name']); ?></h3>
        </div>
      </div>

      <!-- Intro text tile -->
      <div class="rounded-3xl bg-brand-sand border border-brand-navy/10 p-6 sm:p-7">
        <p class="text-brand-navy/75 leading-relaxed text-sm"><?php echo e($citySeo['intro']); ?></p>
      </div>

      <!-- Live stats tile -->
      <div class="rounded-3xl bg-brand-ink text-white p-6 sm:p-7 flex flex-col justify-center relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-aqua/15 rounded-full blur-2xl"></div>
        <div class="relative grid grid-cols-2 gap-4">
          <div>
            <p class="font-display text-3xl font-bold"><?php echo (int) $cityBoatCount; ?></p>
            <p class="text-white/55 text-xs uppercase tracking-wide mt-1">Boats available</p>
          </div>
          <div>
            <p class="font-display text-3xl font-bold"><?php echo money($cityPriceFrom); ?></p>
            <p class="text-white/55 text-xs uppercase tracking-wide mt-1">From / day</p>
          </div>
        </div>
        <a href="/boats?city=<?php echo e($city['slug']); ?>" class="relative inline-flex items-center gap-1.5 text-brand-goldL hover:text-white font-semibold text-sm mt-5 transition-colors duration-200 cursor-pointer group/cta">
          See all <?php echo e($city['name']); ?> boats
          <svg class="w-4 h-4 group-hover/cta:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
      </div>

      <!-- Highlights tile -->
      <div class="md:col-span-2 rounded-3xl bg-brand-foam border border-brand-navy/10 p-6 sm:p-7">
        <h3 class="font-display text-lg font-semibold text-brand-ink mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Where you'll sail near <?php echo e($city['name']); ?>
        </h3>
        <ul class="grid sm:grid-cols-2 gap-x-6 gap-y-3">
          <?php foreach ($citySeo['highlights'] as $h): ?>
          <li class="flex items-center gap-2.5 text-brand-navy/75 text-sm">
            <span class="h-5 w-5 rounded-full bg-brand-aqua/15 text-brand-aqua flex items-center justify-center shrink-0">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </span>
            <?php echo e($h); ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Good to know tile -->
      <div class="rounded-3xl bg-white border border-brand-navy/10 p-6 sm:p-7">
        <h3 class="font-display text-lg font-semibold text-brand-ink mb-4">Good to know</h3>
        <dl class="space-y-4 text-sm">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-aqua shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <div><dt class="text-brand-navy/50 text-xs uppercase tracking-wide">Departs from</dt><dd class="text-brand-ink font-medium"><?php echo e($citySeo['departure']); ?></dd></div>
          </div>
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-aqua shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.539-1.118l1.519-4.674a1 1 0 00-.363-1.118L2.075 9.8c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.673z"/></svg>
            <div><dt class="text-brand-navy/50 text-xs uppercase tracking-wide">Best for</dt><dd class="text-brand-ink font-medium"><?php echo e($citySeo['best_for']); ?></dd></div>
          </div>
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-brand-aqua shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <div><dt class="text-brand-navy/50 text-xs uppercase tracking-wide">Season</dt><dd class="text-brand-ink font-medium">Year-round &middot; best Apr–Oct</dd></div>
          </div>
        </dl>
      </div>

    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($cityContent): ?>

<!-- LONG-FORM: ABOUT -->
<section class="bg-brand-sand py-20 px-6">
  <div class="max-w-3xl mx-auto reveal">
    <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">The complete guide</p>
    <h2 class="font-display text-3xl sm:text-4xl font-bold text-brand-ink mb-6">Renting a boat in <?php echo e($city['name']); ?>, Cyprus</h2>
    <div class="space-y-5 text-brand-navy/75 leading-relaxed">
      <?php foreach ($cityContent['long'] as $para): ?>
      <p><?php echo e($para); ?></p>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ITINERARY -->
<section class="bg-white py-20 px-6">
  <div class="max-w-4xl mx-auto">
    <div class="reveal mb-10">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">A day on the water</p>
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-brand-ink">A sample one-day itinerary from <?php echo e($city['name']); ?></h2>
      <p class="text-brand-navy/70 mt-3">Every charter is tailored to the wind and to what you want from the day — but here's how a classic full-day cruise from <?php echo e($city['name']); ?> tends to unfold.</p>
    </div>
    <ol class="relative border-l-2 border-brand-foam ml-3 space-y-8 reveal">
      <?php foreach ($cityContent['itinerary'] as $stop): ?>
      <li class="relative pl-8">
        <span class="absolute -left-[11px] top-1 h-5 w-5 rounded-full bg-brand-aqua ring-4 ring-white"></span>
        <span class="inline-block text-brand-aquaD font-semibold text-sm mb-1"><?php echo e($stop['time']); ?></span>
        <h3 class="font-display text-lg font-semibold text-brand-ink"><?php echo e($stop['title']); ?></h3>
        <p class="text-brand-navy/70 text-sm mt-1 leading-relaxed"><?php echo e($stop['text']); ?></p>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="bg-brand-ink text-white py-20 px-6">
  <div class="max-w-5xl mx-auto">
    <div class="reveal mb-12">
      <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-3">Simple from start to finish</p>
      <h2 class="font-display text-3xl sm:text-4xl font-bold">How chartering a boat in <?php echo e($city['name']); ?> works</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 reveal-stagger">
      <?php
      $steps = [
        ['1', 'Browse the fleet', 'Compare boats available in ' . $city['name'] . ' by size, type and price. Every listing shows real photos, specs and rates.'],
        ['2', 'Send one inquiry', 'Tell us your dates, group size and what you\'re after. There\'s no payment at this stage and no booking fee.'],
        ['3', 'We confirm & match', 'We check availability with the local operator and confirm your boat, skipper and meeting point — usually within hours.'],
        ['4', 'Step aboard', 'Meet your skipper at the harbour, cast off and enjoy the day. Any extras — catering, water toys — are arranged in advance.'],
      ];
      foreach ($steps as [$n, $t, $d]): ?>
      <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-gold text-brand-ink font-display font-bold mb-4"><?php echo $n; ?></span>
        <h3 class="font-display text-lg font-semibold mb-2"><?php echo e($t); ?></h3>
        <p class="text-white/60 text-sm leading-relaxed"><?php echo e($d); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PRICING & WHAT TO BRING -->
<section class="bg-brand-sand py-20 px-6">
  <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-6">
    <div class="rounded-3xl bg-white border border-brand-navy/10 p-7 sm:p-8 reveal">
      <h2 class="font-display text-2xl font-bold text-brand-ink mb-4">Charter prices &amp; what's included</h2>
      <div class="space-y-4 text-brand-navy/75 text-sm leading-relaxed">
        <p>What you pay to rent a boat in <?php echo e($city['name']); ?> depends on the vessel, the length of the charter and the season. Self-drive day boats are the most affordable; crewed motor yachts and catamarans cost more but come with a professional skipper and, on larger boats, full crew. Every boat on our site shows its own live rate.</p>
        <p>Day charters are usually quoted as a <span class="text-brand-ink font-medium">half day</span> (around four hours) or a <span class="text-brand-ink font-medium">full day</span> (around eight), with sunset and weekly options on many boats. Fuel for day charters from the home port is typically included; larger yachts may add an APA (Advance Provisioning Allowance) for fuel, food and dockage on longer trips.</p>
        <p>Optional extras — catering, drinks packages, water toys, extra crew or hotel transfers — are arranged in advance and priced separately, so you only pay for what you want. There are never any booking fees when you inquire through us.</p>
      </div>
    </div>
    <div class="rounded-3xl bg-white border border-brand-navy/10 p-7 sm:p-8 reveal">
      <h2 class="font-display text-2xl font-bold text-brand-ink mb-4">What to bring on the day</h2>
      <ul class="grid sm:grid-cols-2 gap-x-6 gap-y-3">
        <?php foreach ([
          'Swimwear & a towel', 'Sunscreen (reef-safe)', 'A hat & sunglasses',
          'A light layer for the evening', 'Your camera or phone', 'Any medication you need',
          'Flat, soft-soled shoes', 'A sense of adventure',
        ] as $item): ?>
        <li class="flex items-center gap-2.5 text-brand-navy/75 text-sm">
          <span class="h-5 w-5 rounded-full bg-brand-aqua/15 text-brand-aqua flex items-center justify-center shrink-0">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          </span>
          <?php echo e($item); ?>
        </li>
        <?php endforeach; ?>
      </ul>
      <p class="text-brand-navy/60 text-xs mt-5 leading-relaxed">Towels, snorkelling gear and safety equipment are provided on most crewed charters — just ask when you inquire.</p>
    </div>
  </div>
</section>

<?php if (!empty($cityContent['faqs'])): ?>
<!-- FAQ -->
<section class="bg-white py-20 px-6">
  <div class="max-w-3xl mx-auto">
    <div class="reveal mb-8">
      <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">Good questions</p>
      <h2 class="font-display text-3xl sm:text-4xl font-bold text-brand-ink">Renting a boat in <?php echo e($city['name']); ?>: FAQs</h2>
    </div>
    <div class="divide-y divide-brand-navy/10 border-y border-brand-navy/10 reveal">
      <?php foreach ($cityContent['faqs'] as $f): ?>
      <details class="group py-4">
        <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-display text-lg font-semibold text-brand-ink">
          <?php echo e($f['q']); ?>
          <svg class="w-5 h-5 text-brand-aqua shrink-0 transition-transform duration-200 group-open:rotate-45" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </summary>
        <p class="text-brand-navy/75 text-sm leading-relaxed mt-3"><?php echo e($f['a']); ?></p>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?php endif; ?>

<?php if ($cityRoute): ?>
<!-- SIGNATURE ROUTE CROSS-LINK -->
<section class="bg-brand-ink py-16 px-6">
  <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 items-center">
    <a href="/routes/<?php echo e($cityRoute['slug']); ?>" class="reveal block rounded-3xl overflow-hidden border border-white/10 bg-white/5 cursor-pointer">
      <?php echo render_route_map($cityRoute['stops'], 'city-route-' . $cityRoute['slug']); ?>
    </a>
    <div class="reveal">
      <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-3">Sailing route from <?php echo e($city['name']); ?></p>
      <h2 class="font-display text-3xl font-bold text-white mb-3"><?php echo e($cityRoute['title']); ?></h2>
      <p class="text-white/70 leading-relaxed mb-6"><?php echo e($cityRoute['best_for']); ?> — a <?php echo e(strtolower($cityRoute['duration'])); ?> cruise covering <?php echo count($cityRoute['stops']); ?> stops along the coast.</p>
      <a href="/routes/<?php echo e($cityRoute['slug']); ?>" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3 rounded-full transition-colors duration-200 cursor-pointer">
        See the full route &amp; map
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- OTHER DESTINATIONS -->
<section class="bg-white py-16 px-6">
  <div class="max-w-7xl mx-auto">
    <h2 class="font-display text-2xl font-semibold text-brand-ink mb-6 reveal">Explore other destinations</h2>
    <div class="flex flex-wrap gap-3 reveal">
      <?php foreach (get_cities() as $c): if ($c['slug'] === $city['slug']) continue; ?>
      <a href="/<?php echo e($c['slug']); ?>" class="inline-flex items-center gap-2 bg-brand-sand hover:bg-brand-foam border border-brand-navy/10 text-brand-navy font-medium text-sm px-5 py-2.5 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <?php echo e($c['name']); ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
