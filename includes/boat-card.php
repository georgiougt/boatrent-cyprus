<?php
/** Renders one boat card. Expects $boat (assoc array with city_name). */
?>
<article class="group flex flex-col rounded-2xl overflow-hidden bg-white border border-brand-navy/10 shadow-sm hover:shadow-xl transition-shadow duration-300">
  <a href="/boat/<?php echo e($boat['slug']); ?>" class="relative block h-52 overflow-hidden cursor-pointer">
    <img src="<?php echo e($boat['image_url']); ?>" alt="<?php echo e($boat['name']); ?> — <?php echo e($boat['type']); ?> in <?php echo e($boat['city_name']); ?>" loading="lazy" class="card-img w-full h-full object-cover">
    <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-brand-ink/85 text-white text-xs font-medium px-3 py-1.5 rounded-full backdrop-blur-sm">
      <svg class="w-3.5 h-3.5 text-brand-gold" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <?php echo e($boat['city_name']); ?>
    </span>
    <?php if (!empty($boat['featured'])): ?>
    <span class="absolute top-3 right-3 bg-brand-gold text-brand-ink text-xs font-semibold px-3 py-1.5 rounded-full">Featured</span>
    <?php endif; ?>
  </a>

  <div class="flex flex-col flex-1 p-5">
    <div class="flex items-center gap-2 mb-2">
      <span class="text-xs font-medium px-2.5 py-1 rounded-full <?php echo type_badge_class($boat['type']); ?>"><?php echo e($boat['type']); ?></span>
      <?php if (!empty($boat['crewed'])): ?>
      <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-brand-navy/10 text-brand-navy">Crewed</span>
      <?php endif; ?>
    </div>

    <h3 class="font-display text-xl font-semibold text-brand-ink leading-tight">
      <a href="/boat/<?php echo e($boat['slug']); ?>" class="hover:text-brand-aquaD transition-colors duration-200 cursor-pointer"><?php echo e($boat['name']); ?></a>
    </h3>

    <ul class="flex flex-wrap gap-x-4 gap-y-1.5 mt-3 text-sm text-brand-navy/70">
      <li class="flex items-center gap-1.5">
        <svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 2a4 4 0 10-7.75 0"/></svg>
        <?php echo (int) $boat['capacity']; ?> guests
      </li>
      <?php if (!empty($boat['length_m'])): ?>
      <li class="flex items-center gap-1.5">
        <svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4l16 16M4 4h6M4 4v6m16 10h-6m6 0v-6"/></svg>
        <?php echo rtrim(rtrim(number_format((float) $boat['length_m'], 1), '0'), '.'); ?> m
      </li>
      <?php endif; ?>
      <?php if (!empty($boat['cabins'])): ?>
      <li class="flex items-center gap-1.5">
        <svg class="w-4 h-4 text-brand-aqua" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M3 12V7a2 2 0 012-2h14a2 2 0 012 2v5M3 12v5a2 2 0 002 2h14a2 2 0 002-2v-5"/></svg>
        <?php echo (int) $boat['cabins']; ?> cabins
      </li>
      <?php endif; ?>
    </ul>

    <div class="mt-auto pt-5 flex items-end justify-between">
      <div>
        <p class="text-2xl font-display font-semibold text-brand-ink"><?php echo money($boat['price_day']); ?><span class="text-sm font-body font-normal text-brand-navy/50">/day</span></p>
        <?php if (!empty($boat['price_hour'])): ?>
        <p class="text-xs text-brand-navy/50">from <?php echo money($boat['price_hour']); ?>/hour</p>
        <?php endif; ?>
      </div>
      <a href="/boat/<?php echo e($boat['slug']); ?>" class="inline-flex items-center gap-1.5 bg-brand-navy hover:bg-brand-ink text-white text-sm font-semibold px-4 py-2.5 rounded-full transition-colors duration-200 cursor-pointer">
        View
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>
  </div>
</article>
