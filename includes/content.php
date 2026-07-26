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
            'image'    => '/assets/scenery/latsi.webp',
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
            'image'    => '/assets/scenery/coast-gold.webp',
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
            'image'    => '/assets/scenery/paphos.webp',
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
            'image'    => '/assets/scenery/coast-blue.webp',
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
            'image'    => '/assets/scenery/protaras.webp',
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
            'image'      => '/assets/scenery/coast-gold.webp',
        ],
        'paphos' => [
            'best_for'   => 'Sea caves & sunset sailing',
            'departure'  => 'Paphos Harbour',
            'intro'      => 'Rent a boat in Paphos and you sail straight into Greek myth — this is the legendary birthplace of Aphrodite. Charter a yacht or speedboat from the harbour to reach the crystal sea caves at Coral Bay, swim beneath Aphrodite\'s Rock, and drop anchor in quiet coves the coast road can\'t reach. With crewed yachts and self-drive options, Paphos charters work just as well for romantic escapes as for family adventures.',
            'highlights' => ['Coral Bay sea caves', 'Aphrodite\'s Rock (Petra tou Romiou)', 'Lara Bay turtle beach', 'Blue Lagoon day trips'],
            'image'      => '/assets/scenery/sailing.webp',
        ],
        'larnaca' => [
            'best_for'   => 'Diving trips & family days out',
            'departure'  => 'Larnaca Marina',
            'intro'      => 'Larnaca\'s calm, shallow waters make it one of the easiest places in Cyprus to rent a boat — ideal for families and first-time charterers. Charter a yacht or motorboat from the marina to snorkel the famous Zenobia wreck, one of the world\'s top dive sites, or simply cruise the relaxed southern shoreline at your own pace. Crewed and self-drive boats are available right through the year.',
            'highlights' => ['The Zenobia wreck dive site', 'Mackenzie Beach', 'Cape Kiti & the salt-lake coast', 'Calm family swimming bays'],
            'image'      => '/assets/scenery/latsi.webp',
        ],
        'ayia-napa' => [
            'best_for'   => 'Sea caves, lagoons & party cruises',
            'departure'  => 'Ayia Napa Harbour',
            'intro'      => 'Ayia Napa is the vibrant heart of Cyprus boating, and renting a speedboat or yacht here puts the island\'s most electric-blue water at your feet. Charter a boat to weave through the Cape Greco sea caves, anchor over the neon shallows of the Blue Lagoon, or book a lively sunset party cruise. Fast self-drive speedboats and spacious group catamarans are the local favourites.',
            'highlights' => ['Cape Greco sea caves & arch', 'Blue Lagoon (Ayia Napa)', 'Konnos Bay', 'Nissi Beach'],
            'image'      => '/assets/scenery/coast-blue.webp',
        ],
        'protaras' => [
            'best_for'   => 'Calm family cruising',
            'departure'  => 'Protaras & Pernera',
            'intro'      => 'With its sheltered, shallow bays, Protaras is made for easy, relaxed days on the water — and renting a boat here couldn\'t be simpler. Charter a yacht or speedboat to reach Fig Tree Bay\'s turquoise shallows, snorkel around Cape Greco, or cruise the quiet coves north of the resort. The gentle seas make Protaras an especially good choice for families with younger children.',
            'highlights' => ['Fig Tree Bay', 'Cape Greco National Park', 'Konnos Bay', 'Green Bay snorkelling'],
            'image'      => '/assets/scenery/protaras.webp',
        ],
        'latsi' => [
            'best_for'   => 'Blue Lagoon & Akamas trips',
            'departure'  => 'Latsi Harbour',
            'intro'      => 'Tucked beneath the wild Akamas peninsula, Latsi is the gateway to the most famous swim spot in Cyprus. Rent a boat or charter a yacht from the little harbour and within the hour you are anchored in the glowing turquoise of the Blue Lagoon. The untouched Akamas coastline, resident sea turtles and string of hidden coves make every Latsi charter feel like a genuine expedition.',
            'highlights' => ['The Blue Lagoon', 'Akamas National Park coastline', 'Manijin Island', 'Sea-turtle spotting'],
            'image'      => '/assets/scenery/paphos.webp',
        ],
    ];
}

/**
 * Long-form landing content per city — prose, a sample one-day itinerary and
 * FAQs. Powers the deep destination pages (and the per-city FAQPage schema).
 * Keyed by city slug.
 */
function get_city_content(): array
{
    return [
        'limassol' => [
            'long' => [
                'Limassol is the busiest charter base in Cyprus, and for good reason. The city\'s marina sits right in the centre of the south coast, so a day afloat can go in almost any direction — west toward the white cliffs of Governor\'s Beach, or south around the Akrotiri peninsula to the long, shallow shelf of Lady\'s Mile. Because the marina is a full-service superyacht harbour, the fleet here is the island\'s deepest: flybridge motor yachts for a champagne day out, roomy catamarans for families, and quick self-drive speedboats when you just want to find a quiet cove and swim.',
                'Renting a boat in Limassol also means the shortest transfer from the city\'s hotels and the easiest provisioning — caterers, water toys and extra crew are all on hand at short notice. Most charters run as half-day (roughly four hours), full-day (about eight) or sunset cruises, with the crew tailoring the route to the wind and to what you want from the day. Whether you\'re marking a birthday, entertaining clients or simply escaping the heat, a Limassol charter is the simplest way onto the water on this coast.',
            ],
            'itinerary' => [
                ['time' => '09:30', 'title' => 'Cast off from Limassol Marina', 'text' => 'Meet your skipper on the pontoon, stow your bags and head out past the breakwater with coffee in hand as the city skyline falls away behind you.'],
                ['time' => '10:30', 'title' => 'Swim stop at Lady\'s Mile', 'text' => 'Anchor over the pale, shallow sand-shelf off Akrotiri where the water glows turquoise — the first swim, paddleboards out, and time on the water toys.'],
                ['time' => '12:30', 'title' => 'Lunch in Akrotiri Bay', 'text' => 'Drift into the sheltered bay for a long lunch on board. Crewed charters serve a mezze spread here while you dive off the swim platform between courses.'],
                ['time' => '15:00', 'title' => 'Cruise the Blue Flag coast', 'text' => 'Track east along the Blue Flag shoreline toward the dramatic white cliffs of Governor\'s Beach, with a final snorkel stop in a quiet cove.'],
                ['time' => '18:00', 'title' => 'Sunset run back to the marina', 'text' => 'Turn for home as the light turns gold, arriving back at Limassol Marina with the harbour lights coming on.'],
            ],
            'faqs' => [
                ['q' => 'How much does it cost to rent a boat in Limassol?', 'a' => 'Self-drive speedboats and smaller day boats start from a few hundred euro for a half day, while crewed motor yachts and catamarans run from around €1,800 for a full day up to €95,000 a week for a superyacht. Every listing shows its own rate — browse the Limassol fleet for live prices.'],
                ['q' => 'Do I need a licence, or can I hire a skipper?', 'a' => 'Most of the Limassol fleet is crewed, so no licence is needed — a professional skipper handles the boat while you relax. A few smaller self-drive boats can be hired without a licence within harbour limits; larger self-drive vessels require a recognised boating licence.'],
                ['q' => 'Where do charters depart from in Limassol?', 'a' => 'Almost all charters leave from Limassol Marina, with some traditional day boats departing the Old Port. Both are central and a short taxi from any city hotel; your exact meeting point is confirmed when you book.'],
                ['q' => 'What will we see on the water?', 'a' => 'Typical Limassol day charters take in Lady\'s Mile, Akrotiri Bay and the Blue Flag coast toward Governor\'s Beach, with swim and snorkel stops along the way. Your skipper adjusts the route to the wind and to what you want from the day.'],
                ['q' => 'When is the best time to charter in Limassol?', 'a' => 'The season runs year-round, but April to October brings the warmest, calmest seas. July and August are peak — book well ahead — while late spring and early autumn offer warm water with fewer crowds.'],
            ],
        ],
        'paphos' => [
            'long' => [
                'Paphos rewards anyone who takes to the water. The harbour sits below the medieval castle and the Tombs of the Kings, and within minutes of leaving it you are cruising a coastline steeped in Greek myth — this is the legendary birthplace of Aphrodite. Head north and the shoreline breaks into the crystalline sea caves of Coral Bay and the wild, protected sands of Lara Bay; head south and you pass beneath Aphrodite\'s Rock, where the water is famously clear and deep.',
                'The Paphos fleet leans toward crewed yachts and comfortable motorboats, with self-drive options for shorter coastal hops. Because much of this coast is a marine and turtle-nesting reserve, a boat is genuinely the best — often the only — way to reach its finest swimming spots. Charters here suit romantic sunset sails just as well as family adventures, and the west-facing coast means some of the best sunset cruising on the island.',
            ],
            'itinerary' => [
                ['time' => '09:30', 'title' => 'Leave Paphos Harbour', 'text' => 'Slip out beneath Paphos Castle and the harbour front, setting a course north along the archaeological coast toward Coral Bay.'],
                ['time' => '10:45', 'title' => 'Snorkel the Coral Bay sea caves', 'text' => 'Anchor off the honeycomb of sea caves and coves at Coral Bay for the first swim and snorkel in glass-clear water.'],
                ['time' => '12:30', 'title' => 'Lunch off Lara Bay', 'text' => 'Push on to the wild, protected sands of Lara Bay — a turtle-nesting reserve — for lunch on board and a swim off the back of the boat.'],
                ['time' => '15:00', 'title' => 'Cruise past Aphrodite\'s Rock', 'text' => 'Turn south down the coast to drift beneath Petra tou Romiou, the sea stack where legend says Aphrodite rose from the waves.'],
                ['time' => '18:30', 'title' => 'Sunset sail home', 'text' => 'Ride the west-facing coast back to the harbour as the sky lights up — Paphos serves some of the best sunsets in Cyprus.'],
            ],
            'faqs' => [
                ['q' => 'How much does it cost to rent a boat in Paphos?', 'a' => 'Prices depend on the boat and the length of charter. Self-drive day boats start from a few hundred euro, while crewed yachts and catamarans are typically hired by the half-day or full-day. Send an inquiry with your dates and group size and we\'ll match you with the right boat and a firm quote.'],
                ['q' => 'Can I reach the sea caves and Lara Bay without a boat?', 'a' => 'Not easily — much of this coast is a protected reserve with no road access, so the sea caves at Coral Bay and the turtle sands of Lara Bay are best reached by boat. That\'s exactly what makes a Paphos charter special.'],
                ['q' => 'Do I need a licence to hire a boat in Paphos?', 'a' => 'No — most charters here are crewed, so a licensed skipper takes care of everything. Small self-drive boats can be rented without a licence close to the harbour; larger self-drive vessels need a recognised boating licence.'],
                ['q' => 'Is Paphos good for a sunset cruise?', 'a' => 'Yes — the coast faces west, so Paphos is one of the best places in Cyprus for a sunset sail. Many couples book an evening charter with drinks on board as the sun drops behind the sea.'],
                ['q' => 'When is the best time to charter in Paphos?', 'a' => 'April through October offers warm, settled conditions. Peak summer is busiest and warmest; spring and autumn give you gentle seas, comfortable temperatures and quieter coves.'],
            ],
        ],
        'larnaca' => [
            'long' => [
                'Larnaca is the easy-going choice on the south coast. The bay is calm and shallow, the marina sits right on the palm-lined seafront, and the sailing is relaxed — which makes it a favourite for families, first-time charterers and anyone who wants a gentle day rather than a fast one. It is also Cyprus\' diving capital: just offshore lies the Zenobia, a vast sunken ferry regularly ranked among the best wreck dives in the world.',
                'The Larnaca fleet ranges from comfortable motor cruisers and catamarans to self-drive day boats, and many charters build in a stop over the Zenobia so snorkellers can peer down at the wreck while divers explore below. Beyond the wreck, the coast unfolds gently toward Cape Kiti and the salt-lake shore, dotted with quiet swimming bays. With its own international airport ten minutes away, Larnaca is also the most convenient base if you\'re short on time.',
            ],
            'itinerary' => [
                ['time' => '09:30', 'title' => 'Depart Larnaca Marina', 'text' => 'Board on the seafront and head out into the wide, sheltered bay — calm water and an easy start to the day.'],
                ['time' => '10:30', 'title' => 'Snorkel over the Zenobia', 'text' => 'Anchor above the famous wreck. Snorkellers look down over the shallows of the hull while the crew tells the story of the ship that sank in 1980.'],
                ['time' => '12:30', 'title' => 'Lunch off Mackenzie Beach', 'text' => 'Cruise back toward the buzz of Mackenzie for lunch on board, with swimming and paddleboards in the warm shallows.'],
                ['time' => '15:00', 'title' => 'Cruise to Cape Kiti', 'text' => 'Follow the salt-lake coast south to the quiet bays around Cape Kiti for a final, peaceful swim away from the crowds.'],
                ['time' => '17:30', 'title' => 'Return to the marina', 'text' => 'An unhurried cruise back across the bay to Larnaca Marina rounds off the day.'],
            ],
            'faqs' => [
                ['q' => 'How much does it cost to rent a boat in Larnaca?', 'a' => 'Rates vary with the boat and the length of the day. Self-drive day boats are the most affordable option, while crewed cruisers and catamarans are hired by the half or full day. Tell us your dates and group and we\'ll send exact prices.'],
                ['q' => 'Can we see the Zenobia wreck on a boat trip?', 'a' => 'Yes. The wreck lies just off the marina and sits close enough to the surface that snorkellers can see it in good conditions, while certified divers can explore it fully. Many Larnaca charters include a stop over the site.'],
                ['q' => 'Is Larnaca good for families?', 'a' => 'Very — the bay is calm and shallow, transfers are short, and the relaxed pace suits younger children. It\'s one of the easiest places in Cyprus to spend a first day on a boat.'],
                ['q' => 'Do I need a licence to charter in Larnaca?', 'a' => 'No licence is needed for crewed charters, where a skipper runs the boat. Some small self-drive boats can be hired without one; larger self-drive vessels require a recognised boating licence.'],
                ['q' => 'When is the best time to charter in Larnaca?', 'a' => 'The warm season runs April to October, with the calmest, warmest water in summer. Larnaca\'s sheltered bay stays comfortable even when other coasts get breezy, so it charters well right through the season.'],
            ],
        ],
        'ayia-napa' => [
            'long' => [
                'Ayia Napa has the most electric water in Cyprus, and a boat is the best way to enjoy it. Leaving the harbour you\'re quickly among the sculpted sea caves and rock arches of Cape Greco, then out over the neon-blue shallows that give the local Blue Lagoon its name. The seabed here is pale sand and the sun is relentless, so the sea turns an almost unreal shade of turquoise — perfect for swimming, jumping off the boat and snorkelling.',
                'The fleet reflects the resort: fast self-drive speedboats for zipping between coves, and big, sociable catamarans built for groups and celebrations. Sunset party cruises are an Ayia Napa institution, but the same coast is just as good for a laid-back family day of swim stops and cave-spotting. Whatever the vibe, the highlights — Cape Greco, the Blue Lagoon, Konnos Bay and Nissi Beach — are all within an easy cruise of the harbour.',
            ],
            'itinerary' => [
                ['time' => '10:00', 'title' => 'Leave Ayia Napa Harbour', 'text' => 'Head east out of the harbour with the day\'s first stretch of coast — sea caves and bright water — opening up ahead.'],
                ['time' => '10:45', 'title' => 'Explore the Cape Greco caves', 'text' => 'Nose in among the sea caves and the natural rock arch at Cape Greco, with time to swim through the clear water around them.'],
                ['time' => '12:00', 'title' => 'Anchor at the Blue Lagoon', 'text' => 'Drop anchor over the glowing shallows of the Blue Lagoon for the main swim stop — snorkels, jumps off the boat and lunch on board.'],
                ['time' => '14:30', 'title' => 'Swim at Konnos Bay', 'text' => 'Cruise to the sheltered horseshoe of Konnos Bay, one of the prettiest swimming spots on this coast.'],
                ['time' => '16:30', 'title' => 'Cruise past Nissi Beach', 'text' => 'Ease back west past the famous sands of Nissi Beach before returning to the harbour — or stay out for a sunset party cruise.'],
            ],
            'faqs' => [
                ['q' => 'How much does it cost to rent a boat in Ayia Napa?', 'a' => 'Self-drive speedboats are the most popular and affordable option for short hops, while group catamarans and crewed cruises are priced by the trip or the day. Prices depend on the boat and season — send your dates for a quote.'],
                ['q' => 'Can I hire a speedboat to drive myself?', 'a' => 'Yes — self-drive speedboats are an Ayia Napa speciality. Smaller boats can often be hired without a licence within a set area; more powerful boats require a recognised boating licence. Skippered options are also available.'],
                ['q' => 'Are sunset and party cruises available?', 'a' => 'They are — sociable catamaran party cruises, including sunset departures with music and drinks, are one of the most popular ways to get on the water in Ayia Napa. Private group charters can be arranged too.'],
                ['q' => 'What are the best spots to visit by boat?', 'a' => 'The classics are the Cape Greco sea caves and arch, the Blue Lagoon, Konnos Bay and Nissi Beach — all within an easy cruise of the harbour and all with excellent swimming.'],
                ['q' => 'When is the best time to charter in Ayia Napa?', 'a' => 'High summer (June to September) is the liveliest and warmest, ideal for swimming and party cruises. Late spring and early autumn are quieter but still warm and bright.'],
            ],
        ],
        'protaras' => [
            'long' => [
                'Protaras is the gentle side of Cyprus\' south-east corner. Its bays are shallow and sheltered, the water is famously clear, and the whole area has a calmer, more family-friendly feel than neighbouring Ayia Napa. Renting a boat here usually starts from Protaras or nearby Pernera, and within minutes you\'re anchored over the turquoise shallows of Fig Tree Bay, one of the most photographed beaches on the island.',
                'The local fleet favours easy, comfortable day boats and speedboats — the kind of vessels that suit a relaxed cruise with children on board. From Fig Tree Bay the coast leads down to the protected headland of Cape Greco and the pretty cove of Konnos Bay, with plenty of quiet spots to stop, swim and snorkel along the way. If you want a calm, unhurried day on beautiful water, Protaras is hard to beat.',
            ],
            'itinerary' => [
                ['time' => '10:00', 'title' => 'Depart Protaras / Pernera', 'text' => 'Set off from the sheltered shore into calm, shallow water — an easy, gentle start well suited to families.'],
                ['time' => '10:40', 'title' => 'Swim at Fig Tree Bay', 'text' => 'Anchor over the turquoise shallows of Fig Tree Bay for the first swim, with paddleboards and snorkels out.'],
                ['time' => '12:15', 'title' => 'Snorkel at Green Bay', 'text' => 'Cruise to Green Bay, known for its clear water and easy snorkelling over rocks and seagrass close to the surface.'],
                ['time' => '14:00', 'title' => 'Lunch off Cape Greco', 'text' => 'Round toward the Cape Greco National Park headland for lunch on board and a swim in a quiet, protected cove.'],
                ['time' => '16:30', 'title' => 'Cruise back via Konnos Bay', 'text' => 'Call in at the sheltered horseshoe of Konnos Bay for a last swim before the gentle run home.'],
            ],
            'faqs' => [
                ['q' => 'How much does it cost to rent a boat in Protaras?', 'a' => 'Self-drive day boats and speedboats are the most common and budget-friendly choices, with crewed options available too. The exact price depends on the boat, the season and the length of the day — send your details for a quote.'],
                ['q' => 'Is Protaras suitable for young children?', 'a' => 'Ideal, in fact. The bays are shallow and sheltered and the pace is relaxed, which makes Protaras one of the best places in Cyprus to take younger children out on a boat.'],
                ['q' => 'Where do boats depart from in Protaras?', 'a' => 'Charters typically leave from the Protaras shore or the small harbour at nearby Pernera. Both are close to the resort\'s hotels; your exact meeting point is confirmed when you book.'],
                ['q' => 'Do I need a licence?', 'a' => 'No licence is needed for crewed or skippered charters. Small self-drive boats can often be hired without one within a set area; larger self-drive vessels need a recognised boating licence.'],
                ['q' => 'When is the best time to charter in Protaras?', 'a' => 'April to October brings the warmest, calmest conditions. The sheltered bays here stay comfortable through the season, and spring and autumn are especially peaceful.'],
            ],
        ],
        'latsi' => [
            'long' => [
                'Latsi is the launch point for the most famous swim in Cyprus. This small fishing harbour sits at the edge of the wild Akamas peninsula, and from it the glowing turquoise of the Blue Lagoon is less than an hour away by boat. Because the Akamas is a protected national park with almost no road access, chartering from Latsi is the way to reach a coastline of untouched coves, sea caves and resident sea turtles that most visitors never see.',
                'Boats from Latsi range from crewed day cruisers to self-drive speedboats and traditional wooden boats, and nearly every trip builds around the Blue Lagoon and the Akamas coast. It\'s a more adventurous, expedition-like day than the busy resort coasts — quieter water, dramatic scenery and a real sense of getting away from it all. For nature lovers and anyone chasing that perfect turquoise photo, Latsi is the standout charter base in the west.',
            ],
            'itinerary' => [
                ['time' => '09:30', 'title' => 'Cast off from Latsi Harbour', 'text' => 'Leave the little fishing harbour and turn along the wild Akamas coast, the scenery growing more dramatic with every mile.'],
                ['time' => '10:30', 'title' => 'Anchor at the Blue Lagoon', 'text' => 'Drop anchor in the glowing turquoise of the Blue Lagoon — the main event, with snorkelling, swimming and jumps off the boat.'],
                ['time' => '12:30', 'title' => 'Explore the Akamas coves', 'text' => 'Cruise the protected Akamas coastline, stopping in hidden coves and sea caves for lunch on board and quiet swims.'],
                ['time' => '14:30', 'title' => 'Swim off Manijin Island', 'text' => 'Pause by the little island of Manijin, a good spot for snorkelling and, with luck, sighting a sea turtle.'],
                ['time' => '16:30', 'title' => 'Return to Latsi', 'text' => 'A scenic cruise back along the peninsula to the harbour, salty and sun-tired in the best way.'],
            ],
            'faqs' => [
                ['q' => 'How much does it cost to rent a boat in Latsi?', 'a' => 'Prices depend on the boat and the length of the trip. Self-drive boats and shared Blue Lagoon cruises are the most affordable; private crewed charters cost more but give you the coast at your own pace. Send your dates for a quote.'],
                ['q' => 'Is the Blue Lagoon worth chartering a boat for?', 'a' => 'Absolutely — the Blue Lagoon and the surrounding Akamas coast have little or no road access, so a boat is the best way to reach them. Private charters also let you arrive early or late and enjoy the lagoon without the day-trip crowds.'],
                ['q' => 'Might we see sea turtles?', 'a' => 'Often, yes. The Akamas coast is a nesting area for green and loggerhead turtles, and they\'re regularly spotted in the water around Latsi and Manijin Island, especially in the warmer months.'],
                ['q' => 'Do I need a licence to charter from Latsi?', 'a' => 'No — crewed and skippered charters need no licence. Small self-drive boats can be hired without one in a set area; larger self-drive vessels require a recognised boating licence.'],
                ['q' => 'When is the best time to charter in Latsi?', 'a' => 'May to October gives the warmest, calmest seas for the Akamas run. Midsummer is busiest at the Blue Lagoon, so late spring and early autumn are lovely times for a quieter trip.'],
            ],
        ],
    ];
}

/**
 * Sailing routes — curated day itineraries with a map, keyed by slug.
 * Each stop carries lon/lat so render_route_map() can plot it.
 */
function get_sailing_routes(): array
{
    return [
        'limassol-akrotiri-bay' => [
            'title'     => 'Limassol & Akrotiri Bay Day Cruise',
            'city'      => 'limassol',
            'depart'    => 'Limassol Marina',
            'duration'  => 'Full day (~8 hrs)',
            'distance'  => '~22 nautical miles',
            'best_for'  => 'Swimming, sunbathing & a long lunch on board',
            'boats'     => 'Motor yachts & catamarans',
            'image'     => '/assets/scenery/coast-gold.webp',
            'intro'     => [
                'The classic Limassol day out: an easy loop along the south coast that packs in the best swimming on this stretch of shore without ever straying far from the marina. It works beautifully on a crewed motor yacht or a family catamaran, with plenty of time to anchor, swim and eat.',
                'Because Limassol Marina sits in the middle of the coast, the skipper can flip the route to suit the wind — running west to Akrotiri or east to Governor\'s Beach first. Either way you\'re never more than a short cruise from a sheltered anchorage.',
            ],
            'stops' => [
                ['name' => 'Limassol Marina', 'lon' => 33.04, 'lat' => 34.68, 'text' => 'Board and cast off from the heart of the city, cruising out past the breakwater into open water.'],
                ['name' => "Lady's Mile", 'lon' => 33.00, 'lat' => 34.62, 'text' => 'Anchor over the pale, shallow sand-shelf off Akrotiri for the first swim in glowing turquoise water.'],
                ['name' => 'Akrotiri Bay', 'lon' => 32.95, 'lat' => 34.57, 'text' => 'Drift into the sheltered bay below Cape Gata for a long lunch on board and time on the water toys.'],
                ['name' => "Governor's Beach", 'lon' => 33.29, 'lat' => 34.70, 'text' => 'Track east to the dramatic white cliffs and dark sand of Governor\'s Beach for a final snorkel stop.'],
                ['name' => 'Return to marina', 'lon' => 33.05, 'lat' => 34.675, 'text' => 'A gentle sunset cruise back to Limassol Marina as the light turns gold.'],
            ],
        ],
        'ayia-napa-cape-greco-blue-lagoon' => [
            'title'     => 'Cape Greco Sea Caves & Blue Lagoon',
            'city'      => 'ayia-napa',
            'depart'    => 'Ayia Napa Harbour',
            'duration'  => 'Full or half day',
            'distance'  => '~14 nautical miles',
            'best_for'  => 'Sea caves, snorkelling & the brightest water in Cyprus',
            'boats'     => 'Speedboats & group catamarans',
            'image'     => '/assets/scenery/coast-blue.webp',
            'intro'     => [
                'The south-east corner has the most electric water on the island, and this route strings its highlights together into one unforgettable day. From the sculpted sea caves of Cape Greco to the neon shallows of the Blue Lagoon, it\'s all about swimming, snorkelling and jumping off the boat.',
                'Fast self-drive speedboats zip between the coves, while sociable catamarans make it a party — sunset departures included. Every stop is within an easy cruise of Ayia Napa Harbour.',
            ],
            'stops' => [
                ['name' => 'Ayia Napa Harbour', 'lon' => 33.98, 'lat' => 34.99, 'text' => 'Head out of the harbour toward the bright water and caves that line the Cape Greco headland.'],
                ['name' => 'Cape Greco caves', 'lon' => 34.08, 'lat' => 34.96, 'text' => 'Nose in among the sea caves and the natural rock arch, swimming through crystal-clear water.'],
                ['name' => 'Blue Lagoon', 'lon' => 34.07, 'lat' => 34.98, 'text' => 'Anchor over the glowing turquoise shallows for the main swim stop, snorkels and lunch on board.'],
                ['name' => 'Konnos Bay', 'lon' => 34.07, 'lat' => 34.99, 'text' => 'Cruise to the sheltered horseshoe of Konnos Bay, one of the prettiest swimming spots on this coast.'],
                ['name' => 'Nissi Beach', 'lon' => 33.95, 'lat' => 34.985, 'text' => 'Ease back west past the famous sands of Nissi Beach before returning to the harbour.'],
            ],
        ],
        'paphos-sea-caves-aphrodite' => [
            'title'     => "Paphos Sea Caves & Aphrodite's Rock",
            'city'      => 'paphos',
            'depart'    => 'Paphos Harbour',
            'duration'  => 'Full day (~7 hrs)',
            'distance'  => '~26 nautical miles',
            'best_for'  => 'Myth, sea caves & the best sunset sailing in Cyprus',
            'boats'     => 'Crewed yachts & motorboats',
            'image'     => '/assets/scenery/sailing.webp',
            'intro'     => [
                'A cruise through Greek myth along a coast much of which has no road access at all. From the harbour below Paphos Castle you sail north to the crystal sea caves of Coral Bay and the protected turtle sands of Lara Bay, then south beneath Aphrodite\'s Rock.',
                'The west-facing coast means this is also the finest sunset route on the island — many couples book it as an evening sail with drinks on board as the sun drops into the sea.',
            ],
            'stops' => [
                ['name' => 'Paphos Harbour', 'lon' => 32.42, 'lat' => 34.75, 'text' => 'Slip out beneath the castle and set a course north along the archaeological coast.'],
                ['name' => 'Coral Bay caves', 'lon' => 32.35, 'lat' => 34.86, 'text' => 'Anchor off the honeycomb of sea caves at Coral Bay for the first swim and snorkel.'],
                ['name' => 'Lara Bay', 'lon' => 32.30, 'lat' => 34.93, 'text' => 'Push on to the wild, protected sands of Lara Bay — a turtle-nesting reserve — for lunch on board.'],
                ['name' => "Aphrodite's Rock", 'lon' => 32.63, 'lat' => 34.66, 'text' => 'Turn south to drift beneath Petra tou Romiou, where legend says Aphrodite rose from the waves.'],
                ['name' => 'Sunset sail home', 'lon' => 32.43, 'lat' => 34.74, 'text' => 'Ride the west-facing coast back to the harbour as the sky lights up.'],
            ],
        ],
        'latsi-blue-lagoon-akamas' => [
            'title'     => 'Latsi to the Blue Lagoon & Akamas',
            'city'      => 'latsi',
            'depart'    => 'Latsi Harbour',
            'duration'  => 'Full day (~7 hrs)',
            'distance'  => '~18 nautical miles',
            'best_for'  => 'The famous Blue Lagoon, turtles & untouched coves',
            'boats'     => 'Day cruisers & speedboats',
            'image'     => '/assets/scenery/latsi.webp',
            'intro'     => [
                'The route to the most famous swim in Cyprus. From the little fishing harbour of Latsi you cruise the wild Akamas peninsula — a protected national park with almost no road access — to the glowing turquoise of the Blue Lagoon.',
                'It feels more like an expedition than a resort day out: dramatic scenery, hidden coves and resident sea turtles. Arrive early or late on a private charter and you\'ll have the lagoon almost to yourself.',
            ],
            'stops' => [
                ['name' => 'Latsi Harbour', 'lon' => 32.42, 'lat' => 35.04, 'text' => 'Leave the fishing harbour and turn along the wild Akamas coast, the scenery growing as you go.'],
                ['name' => 'Blue Lagoon', 'lon' => 32.33, 'lat' => 35.075, 'text' => 'Drop anchor in the glowing turquoise for the main event — snorkelling, swimming and jumps off the boat.'],
                ['name' => 'Akamas coves', 'lon' => 32.29, 'lat' => 35.02, 'text' => 'Cruise the protected coastline, stopping in hidden coves and sea caves for lunch on board.'],
                ['name' => 'Manijin Island', 'lon' => 32.36, 'lat' => 35.06, 'text' => 'Pause by the little island of Manijin to snorkel and, with luck, spot a sea turtle.'],
                ['name' => 'Return to Latsi', 'lon' => 32.42, 'lat' => 35.045, 'text' => 'A scenic cruise back along the peninsula to the harbour.'],
            ],
        ],
        'larnaca-zenobia-coast' => [
            'title'     => 'Larnaca & the Zenobia Coast',
            'city'      => 'larnaca',
            'depart'    => 'Larnaca Marina',
            'duration'  => 'Half or full day',
            'distance'  => '~12 nautical miles',
            'best_for'  => 'Calm family cruising & the world-famous wreck',
            'boats'     => 'Cruisers & catamarans',
            'image'     => '/assets/scenery/larnaca.webp',
            'intro'     => [
                'The easy-going south-coast route, built around one of the world\'s great dive sites. Larnaca\'s bay is calm and shallow, so this is a relaxed day suited to families and first-time charterers — with a stop over the sunken ferry Zenobia as the centrepiece.',
                'Snorkellers can peer down at the wreck while divers explore below, before the route ambles on to Mackenzie Beach and the quiet bays around Cape Kiti.',
            ],
            'stops' => [
                ['name' => 'Larnaca Marina', 'lon' => 33.63, 'lat' => 34.92, 'text' => 'Board on the palm-lined seafront and head into the wide, sheltered bay.'],
                ['name' => 'Zenobia wreck', 'lon' => 33.60, 'lat' => 34.89, 'text' => 'Anchor over the famous wreck — snorkel the shallows above the hull as the crew tells its story.'],
                ['name' => 'Mackenzie Beach', 'lon' => 33.62, 'lat' => 34.90, 'text' => 'Cruise back toward the buzz of Mackenzie for lunch on board and a swim in the warm shallows.'],
                ['name' => 'Cape Kiti', 'lon' => 33.57, 'lat' => 34.82, 'text' => 'Follow the salt-lake coast south to the quiet bays around Cape Kiti for a final, peaceful swim.'],
                ['name' => 'Return to marina', 'lon' => 33.63, 'lat' => 34.915, 'text' => 'An unhurried cruise back across the bay to Larnaca Marina.'],
            ],
        ],
        'protaras-fig-tree-bay' => [
            'title'     => 'Protaras & Fig Tree Bay Family Cruise',
            'city'      => 'protaras',
            'depart'    => 'Protaras / Pernera',
            'duration'  => 'Half or full day',
            'distance'  => '~10 nautical miles',
            'best_for'  => 'Calm, shallow bays & easy days with children',
            'boats'     => 'Day boats & speedboats',
            'image'     => '/assets/scenery/protaras.webp',
            'intro'     => [
                'The gentle side of the south-east corner. Sheltered, shallow bays and famously clear water make this the calmest of our routes — ideal for a relaxed day with younger children on board.',
                'From the turquoise shallows of Fig Tree Bay the route drifts down to the Cape Greco headland, with easy snorkelling and quiet swim stops all the way.',
            ],
            'stops' => [
                ['name' => 'Protaras / Pernera', 'lon' => 34.05, 'lat' => 35.02, 'text' => 'Set off from the sheltered shore into calm, shallow water — an easy, gentle start.'],
                ['name' => 'Fig Tree Bay', 'lon' => 34.06, 'lat' => 35.01, 'text' => 'Anchor over the turquoise shallows for the first swim, with paddleboards and snorkels out.'],
                ['name' => 'Green Bay', 'lon' => 34.075, 'lat' => 35.00, 'text' => 'Cruise to Green Bay, known for clear water and easy snorkelling close to the surface.'],
                ['name' => 'Cape Greco', 'lon' => 34.08, 'lat' => 34.96, 'text' => 'Round toward the national-park headland for lunch on board and a swim in a protected cove.'],
                ['name' => 'Konnos Bay', 'lon' => 34.07, 'lat' => 34.985, 'text' => 'Call in at the sheltered horseshoe of Konnos Bay for a last swim before the gentle run home.'],
            ],
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
