<?php
/**
 * Homepage guest-reel strip. Shows only the reels an admin has approved AND
 * ticked for the homepage (admin/reels.php). With none picked yet it falls back
 * to a slim invitation so the "send us your reel" route is never a dead end.
 */
$homeReels = get_home_reels(12);
?>

<?php if ($homeReels): ?>
<!-- GUEST REELS -->
<section class="bg-brand-sand pt-16 pb-10 px-6" aria-labelledby="reels-heading">
  <div class="max-w-7xl mx-auto">
    <div class="reveal flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
      <div>
        <p class="text-brand-aquaD font-semibold uppercase tracking-[0.3em] text-xs mb-3">On the water</p>
        <h2 id="reels-heading" class="font-display text-4xl sm:text-5xl font-bold text-brand-ink">Reels from the Water</h2>
        <p class="text-brand-navy/60 mt-3 max-w-xl">Short clips from days out on the water — ours, and ones our guests have sent in.</p>
      </div>
      <a href="/share-your-reel" class="inline-flex items-center gap-2 shrink-0 bg-brand-navy hover:bg-brand-ink text-white font-semibold px-6 py-3 rounded-full transition-colors duration-200 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.55-2.27A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.89L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        Share your reel
      </a>
    </div>

    <div class="relative reveal">
      <button type="button" data-reel-scroll="-1" aria-label="Previous reels" class="hidden lg:flex absolute -left-5 top-1/2 -translate-y-1/2 z-10 h-11 w-11 rounded-full bg-white text-brand-ink shadow-lg items-center justify-center hover:bg-brand-gold hover:text-brand-ink transition-colors duration-200 cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      </button>
      <button type="button" data-reel-scroll="1" aria-label="More reels" class="hidden lg:flex absolute -right-5 top-1/2 -translate-y-1/2 z-10 h-11 w-11 rounded-full bg-white text-brand-ink shadow-lg items-center justify-center hover:bg-brand-gold hover:text-brand-ink transition-colors duration-200 cursor-pointer">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </button>

      <ul data-reel-track class="reel-track flex gap-5 overflow-x-auto snap-x snap-mandatory pb-4 -mx-6 px-6 lg:mx-0 lg:px-0">
        <?php foreach ($homeReels as $i => $reel): ?>
        <li class="snap-start shrink-0">
          <button type="button"
                  class="reel-card group relative block w-[210px] sm:w-[236px] aspect-[9/16] rounded-2xl overflow-hidden bg-brand-ink shadow-sm hover:shadow-xl transition-shadow duration-300 cursor-pointer text-left"
                  data-reel-open="<?php echo (int) $i; ?>"
                  data-reel-src="<?php echo e($reel['video_path']); ?>"
                  data-reel-type="<?php echo e($reel['mime'] ?: 'video/mp4'); ?>"
                  data-reel-name="<?php echo e($reel['guest_name']); ?>"
                  data-reel-meta="<?php echo e(trim(implode(' · ', array_filter([$reel['location'], $reel['boat_name']])))); ?>"
                  data-reel-caption="<?php echo e((string) $reel['caption']); ?>">
            <?php if ($reel['poster_path']): ?>
            <img src="<?php echo e($reel['poster_path']); ?>" alt="Reel filmed by <?php echo e($reel['guest_name']); ?>" loading="lazy" class="card-img absolute inset-0 w-full h-full object-cover">
            <?php else: ?>
            <video class="absolute inset-0 w-full h-full object-cover" muted playsinline preload="metadata" tabindex="-1" aria-hidden="true">
              <source src="<?php echo e($reel['video_path']); ?>#t=0.5" type="<?php echo e($reel['mime'] ?: 'video/mp4'); ?>">
            </video>
            <?php endif; ?>

            <span class="absolute inset-0 bg-gradient-to-t from-brand-ink/90 via-brand-ink/10 to-brand-ink/25"></span>

            <span class="absolute top-3 right-3 h-9 w-9 rounded-full bg-white/15 backdrop-blur text-white flex items-center justify-center group-hover:bg-brand-gold group-hover:text-brand-ink transition-colors duration-300">
              <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M6.3 3.8a1 1 0 011.02.05l8 5a1 1 0 010 1.7l-8 5A1 1 0 015.8 14.7V4.65a1 1 0 01.5-.85z"/></svg>
            </span>

            <span class="absolute bottom-0 left-0 right-0 p-4">
              <?php if ($reel['caption']): ?>
              <span class="block text-white/80 text-xs leading-snug mb-2 line-clamp-2"><?php echo e($reel['caption']); ?></span>
              <?php endif; ?>
              <span class="block font-display text-base font-semibold text-white leading-tight"><?php echo e($reel['guest_name']); ?></span>
              <?php $meta = trim(implode(' · ', array_filter([$reel['location'], $reel['boat_name']]))); ?>
              <?php if ($meta): ?>
              <span class="block text-brand-goldL text-xs mt-0.5"><?php echo e($meta); ?></span>
              <?php endif; ?>
            </span>
          </button>
        </li>
        <?php endforeach; ?>

        <li class="snap-start shrink-0">
          <a href="/share-your-reel" class="flex flex-col items-center justify-center gap-3 w-[210px] sm:w-[236px] aspect-[9/16] rounded-2xl border-2 border-dashed border-brand-navy/20 hover:border-brand-aqua bg-white/50 text-center px-6 transition-colors duration-200 cursor-pointer">
            <span class="h-12 w-12 rounded-full bg-brand-aqua/10 flex items-center justify-center text-brand-aqua">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </span>
            <span class="font-display text-lg font-semibold text-brand-ink">Yours next?</span>
            <span class="text-brand-navy/55 text-xs leading-relaxed">Send us the clip from your day and we'll put it up here.</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- Reel player -->
<div id="reel-lightbox" role="dialog" aria-modal="true" aria-label="Guest reel player">
  <button type="button" id="reel-close" aria-label="Close reel" class="absolute top-5 right-5 h-11 w-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors duration-200 cursor-pointer">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>
  <button type="button" id="reel-prev" aria-label="Previous reel" class="absolute left-3 sm:left-8 top-1/2 -translate-y-1/2 h-11 w-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors duration-200 cursor-pointer">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
  </button>
  <button type="button" id="reel-next" aria-label="Next reel" class="absolute right-3 sm:right-8 top-1/2 -translate-y-1/2 h-11 w-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors duration-200 cursor-pointer">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
  </button>

  <figure class="reel-stage">
    <video id="reel-player" class="reel-player" controls playsinline preload="metadata"></video>
    <figcaption class="text-center mt-4 px-6">
      <p id="reel-caption" class="text-white/75 text-sm max-w-sm mx-auto"></p>
      <p id="reel-name" class="font-display text-lg font-semibold text-white mt-2"></p>
      <p id="reel-meta" class="text-brand-goldL text-xs mt-0.5"></p>
    </figcaption>
  </figure>
</div>

<?php else: ?>
<!-- GUEST REELS — nothing published yet, so invite submissions instead. -->
<section class="bg-brand-sand pt-14 pb-4 px-6">
  <div class="reveal max-w-5xl mx-auto rounded-3xl border border-brand-navy/10 bg-white px-8 py-8 sm:px-10 flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left">
    <span class="h-14 w-14 shrink-0 rounded-2xl bg-brand-aqua/10 flex items-center justify-center text-brand-aqua">
      <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.55-2.27A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.89L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
    </span>
    <div class="flex-1">
      <h2 class="font-display text-2xl font-semibold text-brand-ink">Chartered with us? Send us your reel.</h2>
      <p class="text-brand-navy/60 text-sm mt-1.5">We feature guest clips right here on the homepage — filmed on the day, credited to you.</p>
    </div>
    <a href="/share-your-reel" class="shrink-0 inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">
      Share your reel
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </a>
  </div>
</section>
<?php endif; ?>
