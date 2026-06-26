<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$post = get_blog_post($slug);

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Article not found';
    $robots = 'noindex, follow';
    include __DIR__ . '/includes/header.php';
    echo '<section class="pt-40 pb-32 px-6 text-center"><h1 class="font-display text-4xl font-bold text-brand-ink mb-4">Article not found</h1><p class="text-brand-navy/60 mb-8">This post may have moved.</p><a href="/blog" class="inline-flex items-center gap-2 bg-brand-gold text-brand-ink font-semibold px-6 py-3 rounded-full cursor-pointer">Back to the blog</a></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $post['title'];
$pageDescription = $post['excerpt'];
$pageKeywords = $post['keywords'] ?? '';
$pageImage = $post['image'];
$ogType = 'article';

$postUrl = base_url() . '/blog/' . $post['slug'];
$canonical = $postUrl;

$structuredData = [
    '@context' => 'https://schema.org',
    '@type'    => 'BlogPosting',
    'headline' => $post['title'],
    'description' => $post['excerpt'],
    'image'    => $post['image'],
    'datePublished' => $post['date'],
    'dateModified'  => $post['date'],
    'author'   => ['@type' => 'Organization', 'name' => $post['author']],
    'publisher' => [
        '@type' => 'Organization',
        'name'  => 'BoatRent Cyprus',
        'logo'  => ['@type' => 'ImageObject', 'url' => base_url() . '/images/logo.png'],
    ],
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $postUrl],
    'keywords' => $post['keywords'] ?? '',
];

$related = get_related_posts($post['slug'], 3);
include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb schema -->
<?php echo json_ld([
    '@context' => 'https://schema.org',
    '@type'    => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => base_url() . '/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => base_url() . '/blog'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $post['title'], 'item' => $postUrl],
    ],
]); ?>

<!-- HERO -->
<section class="relative h-[58vh] min-h-[420px] flex items-end overflow-hidden">
  <img src="<?php echo e($post['image']); ?>" alt="<?php echo e($post['title']); ?>" class="absolute inset-0 w-full h-full object-cover">
  <div class="absolute inset-0 hero-overlay"></div>
  <div class="relative z-10 max-w-3xl mx-auto px-6 pb-12 w-full">
    <nav class="reveal text-white/70 text-sm mb-4 flex items-center gap-2 flex-wrap" aria-label="Breadcrumb">
      <a href="/" class="hover:text-brand-gold cursor-pointer">Home</a><span>/</span>
      <a href="/blog" class="hover:text-brand-gold cursor-pointer">Blog</a><span>/</span>
      <span class="text-white"><?php echo e($post['category']); ?></span>
    </nav>
    <h1 class="reveal font-display text-3xl sm:text-5xl font-bold text-white leading-tight"><?php echo e($post['title']); ?></h1>
    <div class="reveal flex items-center gap-2 text-white/70 text-sm mt-4">
      <span><?php echo e($post['author']); ?></span>
      <span aria-hidden="true">&middot;</span>
      <time datetime="<?php echo e($post['date']); ?>"><?php echo e(date('j M Y', strtotime($post['date']))); ?></time>
      <span aria-hidden="true">&middot;</span>
      <span><?php echo (int) $post['read']; ?> min read</span>
    </div>
  </div>
</section>

<!-- BODY -->
<section class="bg-brand-sand py-16 px-6">
  <article class="max-w-3xl mx-auto reveal">
    <div class="prose-cyprus">
      <?php echo $post['body']; // trusted in-house HTML ?>
    </div>

    <!-- Inline CTA -->
    <div class="mt-12 rounded-2xl bg-gradient-to-br from-brand-navy to-brand-ink p-8 text-center">
      <h2 class="font-display text-2xl font-bold text-white mb-2">Plan your own Cyprus boat trip</h2>
      <p class="text-white/70 mb-6 max-w-lg mx-auto">Browse the fleet across six coastal towns and send a free, no-obligation inquiry.</p>
      <a href="/boats" class="inline-flex items-center gap-2 bg-brand-gold hover:bg-brand-goldL text-brand-ink font-semibold px-7 py-3.5 rounded-full transition-colors duration-200 cursor-pointer">Browse the Fleet</a>
    </div>

    <div class="mt-10 pt-8 border-t border-brand-navy/10">
      <a href="/blog" class="inline-flex items-center gap-2 text-brand-navy hover:text-brand-aquaD font-semibold transition-colors duration-200 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to all articles
      </a>
    </div>
  </article>
</section>

<!-- RELATED -->
<?php if ($related): ?>
<section class="bg-white py-16 px-6">
  <div class="max-w-7xl mx-auto">
    <h2 class="font-display text-2xl sm:text-3xl font-semibold text-brand-ink mb-8 reveal">Related reading</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7 reveal-stagger">
      <?php foreach ($related as $p): ?>
      <article class="group flex flex-col rounded-2xl overflow-hidden bg-brand-sand border border-brand-navy/10 hover:shadow-xl transition-shadow duration-300">
        <a href="/blog/<?php echo e($p['slug']); ?>" class="relative block h-44 overflow-hidden cursor-pointer">
          <img src="<?php echo e($p['image']); ?>" alt="<?php echo e($p['title']); ?>" loading="lazy" class="card-img w-full h-full object-cover">
          <span class="absolute top-3 left-3 bg-brand-ink/85 text-white text-xs font-medium px-3 py-1.5 rounded-full"><?php echo e($p['category']); ?></span>
        </a>
        <div class="flex flex-col flex-1 p-5">
          <h3 class="font-display text-lg font-semibold text-brand-ink leading-snug">
            <a href="/blog/<?php echo e($p['slug']); ?>" class="hover:text-brand-aquaD transition-colors duration-200 cursor-pointer"><?php echo e($p['title']); ?></a>
          </h3>
          <p class="text-brand-navy/60 text-sm mt-2 leading-relaxed flex-1"><?php echo e($p['excerpt']); ?></p>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
