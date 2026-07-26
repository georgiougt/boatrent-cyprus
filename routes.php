<?php
require_once __DIR__ . '/includes/functions.php';

$routes = get_sailing_routes();

$pageTitle = 'Cyprus Sailing Routes & Boat Trip Itineraries ' . date('Y');
$pageDescription = 'Discover the best sailing routes in Cyprus — day-charter itineraries from Limassol, Ayia Napa, Paphos, Latsi, Larnaca and Protaras, each with a map, stops and the boats that suit it.';
$pageKeywords = 'cyprus sailing routes, boat trip itinerary cyprus, yacht charter routes cyprus, blue lagoon boat trip, cape greco boat tour, sea caves cruise cyprus';
$canonical = base_url() . '/routes';

// ItemList of routes for rich results.
$routeItems = [];
$pos = 1;
foreach ($routes as $slug => $r) {
    $routeItems[] = [
        '@type'    => 'ListItem',
        'position' => $pos++,
        'name'     => $r['title'],
        'url'      => base_url() . '/routes/' . $slug,
    ];
}
$structuredData = [
    '@context' => 'https://schema.org',
    '@type'    => 'ItemList',
    'name'     => 'Cyprus sailing routes',
    'itemListElement' => $routeItems,
];

include __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="relative pt-36 pb-16 px-6 bg-brand-ink overflow-hidden">
  <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-aqua/10 rounded-full blur-3xl" aria-hidden="true"></div>
  <div class="relative max-w-7xl mx-auto">
    <nav class="reveal text-white/60 text-sm mb-4 flex items-center gap-2" aria-label="Breadcrumb">
      <a href="/" class="hover:text-brand-gold cursor-pointer">Home</a><span>/</span>
      <span class="text-white">Sailing Routes</span>
    </nav>
    <p class="reveal text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-3">Where a day afloat can take you</p>
    <h1 class="reveal font-display text-4xl sm:text-6xl font-bold text-white max-w-3xl">Cyprus Sailing Routes &amp; Boat-Trip Itineraries</h1>
    <p class="reveal text-white/70 text-lg mt-4 max-w-2xl">Six of the best day-charter routes on the island — each with a map, the stops along the way and the boats that suit it. Pick a coast, then browse the fleet that sails it.</p>
  </div>
</section>

<!-- ROUTES GRID -->
<section class="bg-brand-sand py-16 px-6">
  <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-7 reveal-stagger">
    <?php foreach ($routes as $slug => $r): ?>
    <article class="group flex flex-col rounded-3xl overflow-hidden bg-white border border-brand-navy/10 shadow-sm hover:shadow-xl transition-shadow duration-300">
      <a href="/routes/<?php echo e($slug); ?>" class="block relative bg-brand-foam cursor-pointer">
        <?php echo render_route_map($r['stops'], 'hub-' . $slug); ?>
      </a>
      <div class="flex flex-col flex-1 p-6 sm:p-7">
        <span class="inline-flex items-center gap-1.5 text-brand-aquaD text-xs font-semibold uppercase tracking-[0.15em] mb-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          Departs <?php echo e($r['depart']); ?>
        </span>
        <h2 class="font-display text-2xl font-semibold text-brand-ink leading-tight">
          <a href="/routes/<?php echo e($slug); ?>" class="hover:text-brand-aquaD transition-colors duration-200 cursor-pointer"><?php echo e($r['title']); ?></a>
        </h2>
        <p class="text-brand-navy/70 text-sm mt-2 leading-relaxed"><?php echo e($r['best_for']); ?></p>
        <div class="flex flex-wrap gap-x-5 gap-y-2 mt-4 text-sm text-brand-navy/70">
          <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg><?php echo e($r['duration']); ?></span>
          <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg><?php echo e($r['distance']); ?></span>
        </div>
        <a href="/routes/<?php echo e($slug); ?>" class="mt-5 inline-flex items-center gap-1.5 text-brand-navy font-semibold text-sm group/cta cursor-pointer">
          View the route
          <svg class="w-4 h-4 group-hover/cta:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
      </div>
    </article>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
