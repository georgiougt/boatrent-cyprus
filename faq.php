<?php
$pageTitle = 'Frequently Asked Questions';
$pageDescription = 'Answers to common questions about renting a boat in Cyprus — licences, booking, what is included, departure points and more.';
$pageKeywords = 'boat rental cyprus faq, yacht hire questions, do i need a licence, cyprus charter help';
require_once __DIR__ . '/includes/functions.php';

$faqs = get_faqs();

// FAQPage structured data
$structuredData = [
    '@context' => 'https://schema.org',
    '@type'    => 'FAQPage',
    'mainEntity' => array_map(fn($f) => [
        '@type' => 'Question',
        'name'  => $f['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
    ], $faqs),
];

$canonical = base_url() . '/faq';
include __DIR__ . '/includes/header.php';
?>

<section class="relative pt-36 pb-16 px-6 bg-brand-ink overflow-hidden">
  <img src="/assets/scenery/marina.webp" alt="" aria-hidden="true" class="absolute inset-0 w-full h-full object-cover opacity-20">
  <div class="relative max-w-4xl mx-auto text-center reveal">
    <p class="text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-4">Help &amp; answers</p>
    <h1 class="font-display text-5xl sm:text-6xl font-bold text-white">Frequently Asked Questions</h1>
    <p class="text-white/70 mt-5 max-w-xl mx-auto">Everything you need to know before you set sail. Can't find your answer? <a href="/contact" class="text-brand-goldL underline hover:text-white transition-colors cursor-pointer">Get in touch</a>.</p>
  </div>
</section>

<section class="bg-brand-sand py-16 px-6">
  <div class="max-w-3xl mx-auto space-y-4">
    <?php foreach ($faqs as $i => $faq): ?>
    <details class="group bg-white rounded-2xl border border-brand-navy/10 overflow-hidden reveal" <?php echo $i === 0 ? 'open' : ''; ?>>
      <summary class="flex items-center justify-between gap-4 cursor-pointer list-none px-6 py-5 font-display text-lg font-semibold text-brand-ink hover:text-brand-aquaD transition-colors duration-200">
        <span><?php echo e($faq['q']); ?></span>
        <span class="shrink-0 h-8 w-8 rounded-full bg-brand-foam text-brand-aquaD flex items-center justify-center transition-transform duration-300 group-open:rotate-45">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </span>
      </summary>
      <div class="px-6 pb-5 -mt-1 text-brand-navy/75 leading-relaxed">
        <?php echo e($faq['a']); ?>
      </div>
    </details>
    <?php endforeach; ?>
  </div>
</section>

<!-- CTA -->
<section class="bg-white py-16 px-6">
  <div class="reveal max-w-4xl mx-auto rounded-3xl bg-gradient-to-br from-brand-navy to-brand-ink p-10 sm:p-12 text-center relative overflow-hidden">
    <div class="absolute -top-16 -right-16 w-72 h-72 bg-brand-aqua/15 rounded-full blur-3xl"></div>
    <h2 class="relative font-display text-3xl font-bold text-white mb-3">Still have a question?</h2>
    <p class="relative text-white/70 max-w-lg mx-auto mb-7">Our team knows the Cyprus coast inside out and is happy to help you find the perfect boat.</p>
    <div class="relative flex flex-wrap items-center justify-center gap-3">
      <a href="/contact" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">Contact Us</a>
      <a href="/boats" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">Browse the Fleet</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
