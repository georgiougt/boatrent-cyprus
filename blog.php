<?php
$pageTitle = 'Cyprus Boating Blog';
$pageDescription = 'Guides, tips and inspiration for boat trips in Cyprus — the best destinations, choosing your vessel, licence rules and the best time to sail.';
$pageKeywords = 'cyprus boating blog, yacht charter tips, boat trip guides cyprus';
require_once __DIR__ . '/includes/functions.php';

$posts = array_values(get_blog_posts());
// newest first
usort($posts, fn($a, $b) => strcmp($b['date'], $a['date']));
$featured = $posts[0];
$rest = array_slice($posts, 1);

// Blog (CollectionPage) + ItemList schema
$structuredData = [
    '@context' => 'https://schema.org',
    '@type'    => 'Blog',
    'name'     => 'Cyprus Boating Blog',
    'url'      => base_url() . '/blog',
    'blogPost' => array_map(fn($p) => [
        '@type'         => 'BlogPosting',
        'headline'      => $p['title'],
        'description'   => $p['excerpt'],
        'datePublished' => $p['date'],
        'image'         => $p['image'],
        'author'        => ['@type' => 'Organization', 'name' => $p['author']],
        'url'           => base_url() . '/blog/' . $p['slug'],
    ], $posts),
];

$canonical = base_url() . '/blog';
include __DIR__ . '/includes/header.php';

function post_card(array $p): void { ?>
  <article class="group flex flex-col rounded-2xl overflow-hidden bg-white border border-brand-navy/10 shadow-sm hover:shadow-xl transition-shadow duration-300">
    <a href="/blog/<?php echo e($p['slug']); ?>" class="relative block h-48 overflow-hidden cursor-pointer">
      <img src="<?php echo e($p['image']); ?>" alt="<?php echo e($p['title']); ?>" loading="lazy" class="card-img w-full h-full object-cover">
      <span class="absolute top-3 left-3 bg-brand-ink/85 text-white text-xs font-medium px-3 py-1.5 rounded-full backdrop-blur-sm"><?php echo e($p['category']); ?></span>
    </a>
    <div class="flex flex-col flex-1 p-6">
      <div class="flex items-center gap-2 text-xs text-brand-navy/50 mb-2">
        <time datetime="<?php echo e($p['date']); ?>"><?php echo e(date('j M Y', strtotime($p['date']))); ?></time>
        <span aria-hidden="true">&middot;</span>
        <span><?php echo (int) $p['read']; ?> min read</span>
      </div>
      <h2 class="font-display text-xl font-semibold text-brand-ink leading-snug">
        <a href="/blog/<?php echo e($p['slug']); ?>" class="hover:text-brand-aquaD transition-colors duration-200 cursor-pointer"><?php echo e($p['title']); ?></a>
      </h2>
      <p class="text-brand-navy/65 text-sm mt-3 leading-relaxed flex-1"><?php echo e($p['excerpt']); ?></p>
      <a href="/blog/<?php echo e($p['slug']); ?>" class="inline-flex items-center gap-1.5 text-brand-navy hover:text-brand-aquaD font-semibold text-sm mt-5 cursor-pointer group/link">
        Read article
        <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>
  </article>
<?php }
?>

<section class="bg-brand-ink pt-32 pb-12 px-6">
  <div class="max-w-7xl mx-auto">
    <p class="reveal text-brand-goldL font-semibold uppercase tracking-[0.3em] text-xs mb-3">The logbook</p>
    <h1 class="reveal font-display text-4xl sm:text-5xl font-bold text-white">Cyprus Boating Blog</h1>
    <p class="reveal text-white/65 mt-3 max-w-2xl">Guides, tips and inspiration to help you make the most of your days on the water around Cyprus.</p>
  </div>
</section>

<section class="bg-brand-sand py-16 px-6">
  <div class="max-w-7xl mx-auto">

    <!-- Featured post -->
    <a href="/blog/<?php echo e($featured['slug']); ?>" class="reveal group grid lg:grid-cols-2 gap-0 rounded-3xl overflow-hidden bg-white border border-brand-navy/10 shadow-sm hover:shadow-xl transition-shadow duration-300 mb-12 cursor-pointer">
      <div class="relative h-64 lg:h-auto overflow-hidden">
        <img src="<?php echo e($featured['image']); ?>" alt="<?php echo e($featured['title']); ?>" class="card-img w-full h-full object-cover">
        <span class="absolute top-4 left-4 bg-brand-gold text-brand-ink text-xs font-semibold px-3 py-1.5 rounded-full">Latest</span>
      </div>
      <div class="p-8 lg:p-10 flex flex-col justify-center">
        <div class="flex items-center gap-2 text-xs text-brand-navy/50 mb-3">
          <span class="text-brand-aquaD font-semibold uppercase tracking-wide"><?php echo e($featured['category']); ?></span>
          <span aria-hidden="true">&middot;</span>
          <time datetime="<?php echo e($featured['date']); ?>"><?php echo e(date('j M Y', strtotime($featured['date']))); ?></time>
          <span aria-hidden="true">&middot;</span>
          <span><?php echo (int) $featured['read']; ?> min read</span>
        </div>
        <h2 class="font-display text-3xl font-bold text-brand-ink leading-tight group-hover:text-brand-aquaD transition-colors duration-200"><?php echo e($featured['title']); ?></h2>
        <p class="text-brand-navy/65 mt-4 leading-relaxed"><?php echo e($featured['excerpt']); ?></p>
        <span class="inline-flex items-center gap-1.5 text-brand-navy font-semibold mt-6">Read article
          <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </span>
      </div>
    </a>

    <!-- Grid of remaining posts -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7 reveal-stagger">
      <?php foreach ($rest as $p) { post_card($p); } ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
