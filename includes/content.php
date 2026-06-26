<?php
/**
 * Static editorial content: frequently asked questions and blog posts.
 * Kept separate from the database since this is marketing copy that rarely changes.
 */

/** @return array<int, array{q:string, a:string}> */
function get_faqs(): array
{
    return [
        [
            'q' => 'Do I need a licence to rent a boat in Cyprus?',
            'a' => 'It depends on the boat. Most of our charters come with a professional skipper, so no licence is needed — you simply relax and enjoy. For self-drive speedboats and smaller vessels, a valid boating licence is usually required, though some low-powered boats can be driven without one. Each listing states whether it is crewed or self-drive, and our team confirms the exact requirements before you sail.',
        ],
        [
            'q' => 'How does booking work after I send an inquiry?',
            'a' => 'Once you submit an inquiry on a boat page, your request goes straight to our team. We check live availability with the local operator for your dates and reply — usually within a few hours during office hours — with confirmation and the final details. There is no upfront payment to inquire and no obligation to proceed.',
        ],
        [
            'q' => 'Are the boats and operators licensed and insured?',
            'a' => 'Yes. Every vessel listed on BoatRent Cyprus is operated by a licensed local partner that is fully insured for passenger charters in Cypriot waters. Safety equipment is provided on board and skippers are experienced, qualified professionals.',
        ],
        [
            'q' => 'What is included in the rental price?',
            'a' => 'Each listing shows exactly what is included — typically the skipper (on crewed boats), fuel for the agreed route, safety gear and basic amenities such as a cool box and shaded seating. Extras like catering, premium drinks or water toys vary by boat and are noted in the features. We confirm everything in writing before your trip.',
        ],
        [
            'q' => 'Where do the boats depart from?',
            'a' => 'Boats depart from the marina or harbour of their listed town — Limassol, Paphos, Larnaca, Ayia Napa, Protaras or Latsi. The exact meeting point and time are confirmed once your booking is finalised, and it is always within easy reach of the town centre.',
        ],
        [
            'q' => 'Can we bring our own food and drinks on board?',
            'a' => 'Absolutely. You are welcome to bring your own food and drinks on most charters, and many boats provide a cool box or fridge to keep everything fresh. If you would prefer catering or a stocked bar, just let us know in your inquiry and we will arrange it where available.',
        ],
        [
            'q' => 'What happens if the weather is bad on the day?',
            'a' => 'Your safety comes first. If the operator judges that conditions are unsafe, your trip will be rescheduled to another date or fully refunded — your choice. Cyprus enjoys calm seas and sunshine for most of the year, so weather cancellations are rare.',
        ],
        [
            'q' => 'How many guests can join a charter?',
            'a' => 'Capacity varies by vessel, from intimate speedboats for 6 guests up to large catamarans hosting 16–18. Every listing shows its maximum capacity, and you can filter the fleet by group size to find the perfect match for your party.',
        ],
    ];
}

/**
 * Blog posts keyed by slug. `body` is trusted HTML (authored in-house).
 * @return array<string, array>
 */
function get_blog_posts(): array
{
    return [
        'best-boat-trip-destinations-cyprus' => [
            'slug'     => 'best-boat-trip-destinations-cyprus',
            'title'    => '10 of the Best Boat Trip Destinations in Cyprus',
            'excerpt'  => 'From the Blue Lagoon to hidden sea caves, here are the most beautiful spots to drop anchor on a boat trip around Cyprus.',
            'category' => 'Destinations',
            'author'   => 'The BoatRent Cyprus Team',
            'date'     => '2024-05-12',
            'read'     => 7,
            'image'    => 'https://images.unsplash.com/photo-1500627964684-141351970a7f?auto=format&fit=crop&w=1400&q=80',
            'keywords' => 'cyprus boat trips, blue lagoon, sea caves, akamas, best beaches cyprus by boat',
            'body'     => '
<p>Cyprus packs an astonishing amount of coastline into a small island, and the very best of it is only reachable by sea. Whether you are chartering a crewed yacht or taking the helm of a speedboat, these are the spots worth setting a course for.</p>
<h2>1. The Blue Lagoon, Akamas</h2>
<p>The jewel of the northwest, the Blue Lagoon near Latsi is famous for water so clear it glows turquoise. Anchor up, dive in, and you will understand why it tops every sailor\'s list.</p>
<h2>2. The Sea Caves of Ayia Napa</h2>
<p>Carved into the cliffs east of Ayia Napa, these dramatic arches and grottoes are a paradise for snorkelling and cliff-jumping. A speedboat lets you nose right inside the larger caverns.</p>
<h2>3. Aphrodite\'s Rock, Paphos</h2>
<p>Cruise past the legendary birthplace of the goddess of love. The waters here are calm and ideal for a sunset swim as the rock glows gold in the evening light.</p>
<h2>4. Cape Greco</h2>
<p>Between Ayia Napa and Protaras, Cape Greco is a protected national park with vivid blue bays, a natural rock bridge and some of the best snorkelling on the island.</p>
<h2>5. Fig Tree Bay, Protaras</h2>
<p>Shallow, sheltered and impossibly blue, Fig Tree Bay is perfect for families and for a lazy afternoon at anchor.</p>
<p>Other highlights round out the top ten: the Zenobia wreck off Larnaca for divers, the quiet coves of the Akamas peninsula, the Limassol marina waterfront, Governor\'s Beach, and the long golden sands of Lara Bay — a protected turtle nesting site.</p>
<h2>Ready to explore?</h2>
<p>The easiest way to see these spots is from the water. Browse our fleet across all six coastal towns and send an inquiry — our local partners know exactly which route makes the most of your day.</p>',
        ],
        'yacht-vs-catamaran-vs-speedboat' => [
            'slug'     => 'yacht-vs-catamaran-vs-speedboat',
            'title'    => 'Yacht vs Catamaran vs Speedboat: Which Should You Rent?',
            'excerpt'  => 'Not sure which type of boat suits your trip? Here is a plain-English guide to choosing between a yacht, a catamaran and a speedboat in Cyprus.',
            'category' => 'Guides',
            'author'   => 'The BoatRent Cyprus Team',
            'date'     => '2024-05-28',
            'read'     => 6,
            'image'    => 'https://images.unsplash.com/photo-1605281317010-fe5ffe798166?auto=format&fit=crop&w=1400&q=80',
            'keywords' => 'yacht rental cyprus, catamaran hire, speedboat rental, which boat to rent',
            'body'     => '
<p>The right boat makes all the difference. Here is how the three most popular options compare so you can pick with confidence.</p>
<h2>Luxury &amp; motor yachts</h2>
<p>Best for: special occasions, comfort and style. Yachts offer spacious decks, cabins, shade and often a crew to look after you. They are the premium choice for anniversaries, celebrations or simply travelling in comfort along the coast.</p>
<h2>Catamarans</h2>
<p>Best for: groups and families. With two hulls, a catamaran is exceptionally stable — ideal if anyone is prone to seasickness. The wide deck and netted lounging areas make them brilliant for larger parties who want space to spread out.</p>
<h2>Speedboats</h2>
<p>Best for: adventure and flexibility. Fast and nimble, speedboats let you reach hidden caves and quiet bays quickly. Many are available for self-drive (with a licence) so you can explore at your own pace.</p>
<h2>Still unsure?</h2>
<p>Think about your group size, your budget and the mood you are after — relaxed luxury, family stability or fast-paced adventure. You can filter our entire fleet by vessel type and capacity, and our team is always happy to recommend the right match.</p>',
        ],
        'blue-lagoon-by-boat-latsi-akamas' => [
            'slug'     => 'blue-lagoon-by-boat-latsi-akamas',
            'title'    => 'A Guide to the Blue Lagoon by Boat from Latsi',
            'excerpt'  => 'Everything you need to know about visiting the famous Blue Lagoon and the wild Akamas peninsula on a boat trip from Latsi.',
            'category' => 'Destinations',
            'author'   => 'The BoatRent Cyprus Team',
            'date'     => '2024-06-09',
            'read'     => 5,
            'image'    => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=1400&q=80',
            'keywords' => 'blue lagoon cyprus, latsi boat trip, akamas peninsula, cyprus snorkelling',
            'body'     => '
<p>The Blue Lagoon is the single most photographed stretch of water in Cyprus, and the quiet harbour of Latsi is its natural gateway. Here is how to make the most of a day on the Akamas coast.</p>
<h2>Getting there</h2>
<p>Most boats reach the Blue Lagoon in around 30–45 minutes from Latsi, hugging the rugged, undeveloped shoreline of the Akamas National Park along the way. Keep an eye out for sea turtles, which are common in these waters.</p>
<h2>What to bring</h2>
<ul>
<li>Reef-safe sunscreen and a hat — shade is limited at anchor</li>
<li>A snorkel and mask; the visibility here is superb</li>
<li>Water and snacks (most boats provide a cool box)</li>
<li>A waterproof bag for phones and cameras</li>
</ul>
<h2>Best time to go</h2>
<p>Arrive early or opt for a late-afternoon departure to enjoy the lagoon before or after the midday crowds. Catamarans are especially popular here thanks to their stable decks and easy swim access.</p>
<h2>Plan your trip</h2>
<p>Browse the boats based in Latsi and send an inquiry with your preferred date. Our local partner will confirm the best departure time for calm seas and quiet swimming.</p>',
        ],
        'do-you-need-a-licence-to-rent-a-boat-cyprus' => [
            'slug'     => 'do-you-need-a-licence-to-rent-a-boat-cyprus',
            'title'    => 'Do You Need a Licence to Rent a Boat in Cyprus?',
            'excerpt'  => 'Crewed or self-drive? Here is a clear explanation of the licence rules for renting a boat in Cyprus, so you know exactly what to expect.',
            'category' => 'Guides',
            'author'   => 'The BoatRent Cyprus Team',
            'date'     => '2024-06-18',
            'read'     => 4,
            'image'    => 'https://images.unsplash.com/photo-1593351415075-3bac9f45c877?auto=format&fit=crop&w=1400&q=80',
            'keywords' => 'boat licence cyprus, self drive boat hire, do i need a licence to rent a boat',
            'body'     => '
<p>It is the most common question we are asked, and the good news is that the answer is usually reassuringly simple.</p>
<h2>Crewed charters: no licence needed</h2>
<p>The majority of our boats come with a professional skipper. In this case you need no licence, no experience and no preparation — just turn up and enjoy the day while an expert handles the navigation.</p>
<h2>Self-drive boats: a licence is usually required</h2>
<p>If you would like to take the helm yourself, most powered boats require a valid boating or powerboat licence. Some lower-powered vessels can be operated without one, depending on engine size and local regulations.</p>
<h2>How we make it easy</h2>
<p>Every listing clearly states whether the boat is crewed or self-drive. When you send an inquiry, our team confirms the exact licence requirements for that vessel and your group, so there are never any surprises on the day.</p>
<h2>Not licensed? No problem</h2>
<p>Simply choose a crewed boat, or add an optional skipper to a self-drive vessel where available. You still get the freedom of a private charter without needing any qualifications.</p>',
        ],
        'best-time-to-sail-in-cyprus' => [
            'slug'     => 'best-time-to-sail-in-cyprus',
            'title'    => 'The Best Time of Year to Sail in Cyprus',
            'excerpt'  => 'A month-by-month guide to weather, sea temperature and crowds, to help you pick the perfect time for a boat trip in Cyprus.',
            'category' => 'Guides',
            'author'   => 'The BoatRent Cyprus Team',
            'date'     => '2024-06-25',
            'read'     => 6,
            'image'    => 'https://images.unsplash.com/photo-1473116763249-2faaef81ccda?auto=format&fit=crop&w=1400&q=80',
            'keywords' => 'best time to sail cyprus, cyprus weather sea temperature, when to visit cyprus boat',
            'body'     => '
<p>Cyprus is one of the sunniest spots in the Mediterranean, with a long boating season that stretches well beyond the summer. Here is what to expect through the year.</p>
<h2>Spring (April–May)</h2>
<p>Mild, green and gloriously quiet. The sea is warming up and the coastline is at its most lush. A wonderful time for relaxed cruising and sightseeing without the summer crowds.</p>
<h2>Summer (June–August)</h2>
<p>Peak season. Long, hot, cloudless days and warm, swimmable seas around 25–27°C. This is prime time for swimming stops, water toys and sunset cruises — book ahead, as the best boats fill up fast.</p>
<h2>Autumn (September–October)</h2>
<p>Many sailors\' favourite. The sea is at its warmest after a summer of sunshine, the heat is gentler and the crowds have thinned. Ideal conditions for long days on the water.</p>
<h2>Winter (November–March)</h2>
<p>Quieter and cooler, but Cyprus still serves up plenty of bright, calm days. Great for sightseeing cruises and dolphin spotting, though swimming is for the hardy.</p>
<h2>The verdict</h2>
<p>For the perfect balance of warm seas, sunshine and space, late spring and early autumn are hard to beat. Whatever month you choose, browse our fleet and we will help you find a boat for the conditions.</p>',
        ],
    ];
}

/**
 * Unique SEO editorial per city, shown in the bento section on city pages.
 * Keyed by city slug.
 */
function get_city_seo(): array
{
    return [
        'limassol' => [
            'best_for'   => 'Luxury day charters & sunset cruises',
            'departure'  => 'Limassol Marina & Old Port',
            'intro'      => 'As Cyprus\' largest coastal city and home to its biggest marina, Limassol is the natural place to rent a yacht or charter a boat on the south coast. From sleek motor yachts to family catamarans and self-drive speedboats, the Limassol fleet suits every occasion — a champagne sunset cruise, a swim stop at Lady\'s Mile, or a full day exploring Akrotiri Bay. Send one inquiry and we\'ll match you with a licensed local skipper.',
            'highlights' => ['Lady\'s Mile Beach', 'Akrotiri Bay & the Blue Flag coast', 'Limassol Marina waterfront', 'Governor\'s Beach white cliffs'],
            'image'      => 'https://images.unsplash.com/photo-1605281317010-fe5ffe798166?auto=format&fit=crop&w=1200&q=80',
        ],
        'paphos' => [
            'best_for'   => 'Sea caves & sunset sailing',
            'departure'  => 'Paphos Harbour',
            'intro'      => 'Rent a boat in Paphos and you sail straight into Greek myth — this is the legendary birthplace of Aphrodite. Charter a yacht or speedboat from the harbour to reach the crystal sea caves at Coral Bay, swim beneath Aphrodite\'s Rock, and drop anchor in quiet coves the coast road can\'t reach. With crewed yachts and self-drive options, Paphos charters work just as well for romantic escapes as for family adventures.',
            'highlights' => ['Coral Bay sea caves', 'Aphrodite\'s Rock (Petra tou Romiou)', 'Lara Bay turtle beach', 'Blue Lagoon day trips'],
            'image'      => 'https://images.unsplash.com/photo-1540946485063-a40da27545f8?auto=format&fit=crop&w=1200&q=80',
        ],
        'larnaca' => [
            'best_for'   => 'Diving trips & family days out',
            'departure'  => 'Larnaca Marina',
            'intro'      => 'Larnaca\'s calm, shallow waters make it one of the easiest places in Cyprus to rent a boat — ideal for families and first-time charterers. Charter a yacht or motorboat from the marina to snorkel the famous Zenobia wreck, one of the world\'s top dive sites, or simply cruise the relaxed southern shoreline at your own pace. Crewed and self-drive boats are available right through the year.',
            'highlights' => ['The Zenobia wreck dive site', 'Mackenzie Beach', 'Cape Kiti & the salt-lake coast', 'Calm family swimming bays'],
            'image'      => 'https://images.unsplash.com/photo-1500627964684-141351970a7f?auto=format&fit=crop&w=1200&q=80',
        ],
        'ayia-napa' => [
            'best_for'   => 'Sea caves, lagoons & party cruises',
            'departure'  => 'Ayia Napa Harbour',
            'intro'      => 'Ayia Napa is the vibrant heart of Cyprus boating, and renting a speedboat or yacht here puts the island\'s most electric-blue water at your feet. Charter a boat to weave through the Cape Greco sea caves, anchor over the neon shallows of the Blue Lagoon, or book a lively sunset party cruise. Fast self-drive speedboats and spacious group catamarans are the local favourites.',
            'highlights' => ['Cape Greco sea caves & arch', 'Blue Lagoon (Ayia Napa)', 'Konnos Bay', 'Nissi Beach'],
            'image'      => 'https://images.unsplash.com/photo-1593351415075-3bac9f45c877?auto=format&fit=crop&w=1200&q=80',
        ],
        'protaras' => [
            'best_for'   => 'Calm family cruising',
            'departure'  => 'Protaras & Pernera',
            'intro'      => 'With its sheltered, shallow bays, Protaras is made for easy, relaxed days on the water — and renting a boat here couldn\'t be simpler. Charter a yacht or speedboat to reach Fig Tree Bay\'s turquoise shallows, snorkel around Cape Greco, or cruise the quiet coves north of the resort. The gentle seas make Protaras an especially good choice for families with younger children.',
            'highlights' => ['Fig Tree Bay', 'Cape Greco National Park', 'Konnos Bay', 'Green Bay snorkelling'],
            'image'      => 'https://images.unsplash.com/photo-1473116763249-2faaef81ccda?auto=format&fit=crop&w=1200&q=80',
        ],
        'latsi' => [
            'best_for'   => 'Blue Lagoon & Akamas trips',
            'departure'  => 'Latsi Harbour',
            'intro'      => 'Tucked beneath the wild Akamas peninsula, Latsi is the gateway to the most famous swim spot in Cyprus. Rent a boat or charter a yacht from the little harbour and within the hour you are anchored in the glowing turquoise of the Blue Lagoon. The untouched Akamas coastline, resident sea turtles and string of hidden coves make every Latsi charter feel like a genuine expedition.',
            'highlights' => ['The Blue Lagoon', 'Akamas National Park coastline', 'Manijin Island', 'Sea-turtle spotting'],
            'image'      => 'https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&w=1200&q=80',
        ],
    ];
}

/** Single post by slug, or null. */
function get_blog_post(string $slug): ?array
{
    $posts = get_blog_posts();
    return $posts[$slug] ?? null;
}

/** Other posts for the "related reading" block. */
function get_related_posts(string $excludeSlug, int $limit = 3): array
{
    $posts = array_filter(get_blog_posts(), fn($p) => $p['slug'] !== $excludeSlug);
    return array_slice(array_values($posts), 0, $limit);
}
