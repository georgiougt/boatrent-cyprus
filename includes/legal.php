<?php
/**
 * Shared chrome for the legal pages (privacy, cookies, terms).
 *
 * Each page supplies a $sections array of ['id', 'title', 'html'] and calls
 * render_legal_page(). Keeping the layout here means the three documents stay
 * visually identical and a change to the anchor/TOC behaviour lands on all of
 * them at once.
 */
require_once __DIR__ . '/functions.php';

/** Human-readable "last updated" line for the legal documents. */
function legal_updated(): string
{
    return date('j F Y', strtotime(LEGAL_LAST_UPDATED));
}

/**
 * The registered-trader block required by the e-Commerce Directive. Falls back
 * to the trading name while legal_entity() is still unfilled, so the page never
 * renders an empty <dl> — the staging banner is what nags about the gaps.
 */
function legal_identity_html(): string
{
    $l = legal_entity();
    $b = business();
    $rows = [
        'Trading name'        => $b['name'],
        'Registered name'     => $l['registeredName'] ?: null,
        'Company number'      => $l['regNumber'] ?: null,
        'VAT number'          => $l['vatNumber'] ?: null,
        'Registered address'  => $l['address'] ?: null,
        'Licence'             => $l['licence'] ?: null,
        'Email'               => $l['email'],
        'Telephone'           => $l['phone'],
    ];
    $out = '<dl class="legal-dl">';
    foreach ($rows as $label => $value) {
        if ($value === null || $value === '') {
            continue;
        }
        $out .= '<dt>' . e($label) . '</dt><dd>' . e($value) . '</dd>';
    }
    return $out . '</dl>';
}

/**
 * Render a complete legal page: hero, sticky table of contents, prose body.
 *
 * @param array $opts     eyebrow, title, intro
 * @param array $sections list of ['id' => string, 'title' => string, 'html' => string]
 */
function render_legal_page(array $opts, array $sections): void
{
    $missing = legal_entity_incomplete();
    ?>
<section class="relative pt-36 pb-14 px-6 bg-brand-ink overflow-hidden">
  <img src="/assets/scenery/coast-blue.webp" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover opacity-15">
  <div class="relative max-w-4xl mx-auto text-center reveal">
    <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-4"><?php echo e($opts['eyebrow']); ?></p>
    <h1 class="font-display text-4xl sm:text-5xl font-bold text-white"><?php echo e($opts['title']); ?></h1>
    <p class="text-white/70 mt-5 max-w-2xl mx-auto"><?php echo e($opts['intro']); ?></p>
    <p class="inline-block mt-6 text-xs uppercase tracking-wide text-white/50 border border-white/15 rounded-full px-4 py-1.5">Last updated <?php echo e(legal_updated()); ?></p>
  </div>
</section>

<section class="bg-brand-sand py-14 px-6">
  <div class="max-w-6xl mx-auto">

    <?php if ($missing && !site_is_live()): ?>
    <!-- Visible on local/staging only: never shown on the production domain. -->
    <div class="mb-8 rounded-2xl border border-amber-300 bg-amber-50 px-5 py-4 text-sm text-amber-900">
      <p class="font-semibold mb-1">Before go-live: fill in your company details</p>
      <p>These fields in <code class="font-mono text-xs">includes/config.php</code> &rarr; <code class="font-mono text-xs">legal_entity()</code> are still empty: <strong><?php echo e(implode(', ', $missing)); ?></strong>. Cyprus and EU law require a trader to publish its registered name, address and company number. This notice is hidden automatically on the live domain.</p>
    </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-[16rem_1fr] gap-10">
      <!-- Table of contents -->
      <aside class="hidden lg:block">
        <nav class="sticky top-28" aria-label="On this page">
          <p class="text-xs font-semibold uppercase tracking-wide text-brand-navy/45 mb-3">On this page</p>
          <ol class="space-y-1.5 text-sm">
            <?php foreach ($sections as $i => $s): ?>
            <li>
              <a href="#<?php echo e($s['id']); ?>" class="flex gap-2 text-brand-navy/65 hover:text-brand-aquaD transition-colors duration-200 cursor-pointer">
                <span class="text-brand-navy/30 tabular-nums"><?php echo $i + 1; ?>.</span>
                <span><?php echo e($s['title']); ?></span>
              </a>
            </li>
            <?php endforeach; ?>
          </ol>
        </nav>
      </aside>

      <!-- Document -->
      <div class="bg-white rounded-2xl border border-brand-navy/10 shadow-sm p-7 sm:p-10">
        <details class="lg:hidden mb-8 rounded-xl border border-brand-navy/10 bg-brand-sand px-4 py-3">
          <summary class="text-sm font-semibold text-brand-ink cursor-pointer">Jump to a section</summary>
          <ol class="mt-3 space-y-1.5 text-sm">
            <?php foreach ($sections as $i => $s): ?>
            <li><a href="#<?php echo e($s['id']); ?>" class="text-brand-navy/70 hover:text-brand-aquaD cursor-pointer"><?php echo ($i + 1) . '. ' . e($s['title']); ?></a></li>
            <?php endforeach; ?>
          </ol>
        </details>

        <div class="legal-prose">
          <?php foreach ($sections as $i => $s): ?>
          <h2 id="<?php echo e($s['id']); ?>"><span class="legal-num"><?php echo $i + 1; ?>.</span> <?php echo e($s['title']); ?></h2>
          <?php echo $s['html']; ?>
          <?php endforeach; ?>
        </div>

        <div class="mt-10 pt-8 border-t border-brand-navy/10 flex flex-col sm:flex-row sm:items-center gap-4 justify-between">
          <p class="text-sm text-brand-navy/60">Something here unclear, or want your data removed?</p>
          <a href="/contact" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-6 py-3 rounded-full transition-colors duration-200 cursor-pointer shrink-0">
            Get in touch
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
        </div>
      </div>
    </div>

    <p class="max-w-3xl mx-auto text-center text-xs text-brand-navy/45 mt-10">
      Related: <a href="/privacy" class="underline hover:text-brand-aquaD cursor-pointer">Privacy Policy</a> &middot;
      <a href="/cookies" class="underline hover:text-brand-aquaD cursor-pointer">Cookie Policy</a> &middot;
      <a href="/terms" class="underline hover:text-brand-aquaD cursor-pointer">Terms &amp; Conditions</a>
    </p>
  </div>
</section>
    <?php
}
