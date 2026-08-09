# BoatRent Cyprus

A yacht & boat rental marketplace for Cyprus — per-city listings, vessel detail pages with an inquiry flow, and a full admin dashboard. Built with PHP, HTML, Tailwind (CDN) and SQLite.

## Running locally

PHP 8+ is required (ships with SQLite support). From inside this folder:

```bash
php -S localhost:8011 router.php
```

Then open **http://localhost:8011** in your browser. The `router.php` argument enables clean URLs (e.g. `/paphos`, `/boat/azure-princess`, `/blog/best-time-to-sail-in-cyprus`) on the built-in dev server. In production on Apache, the bundled `.htaccess` provides the same routing (no router argument needed); on nginx, map the same rules via `try_files`.

The SQLite database is created and seeded automatically on first load at `data/boatrent.sqlite` (6 cities, 16 sample boats, 2 sample inquiries). To start fresh, delete that file and reload any page.

## Deploying to Hostinger

This runs on Hostinger shared hosting (LiteSpeed + PHP 8 + PDO SQLite) with no code changes.

1. **PHP version** — in hPanel → *Advanced → PHP Configuration*, set PHP to **8.0+** and make sure `pdo_sqlite` is enabled (it is by default).
2. **Upload the files** — put the contents of this folder into `public_html` (the domain's web root). Either:
   - hPanel → *Git* → connect this repo and deploy, or
   - upload via the File Manager / SFTP.
   Note: `data/boatrent.sqlite` is gitignored and is **created automatically** on first visit.
3. **Permissions** — the `data/` and `uploads/reels/` directories must be writable by PHP (typically already `755`/owned by your user). The bundled `data/.htaccess` blocks the database from web access, and `uploads/.htaccess` stops anything in the guest-upload folder from being executed.
   The bundled `.user.ini` raises `upload_max_filesize`/`post_max_size` so guest reels can be uploaded — Hostinger reads it automatically. If reels fail with "too large" even under the stated limit, check *Advanced → PHP Configuration* for a lower hard cap.
4. **Clean URLs** — handled by the bundled root `.htaccess` (no `router.php` needed in production; that file is only for the local dev server).
5. **SSL** — enable Hostinger's free SSL for the domain.

### Temporary domain & going live
While on a Hostinger temporary `*.hostingersite.com` domain, every page is automatically `noindex` and `robots.txt` returns `Disallow: /`, so the temp domain stays out of Google. Canonical tags, Open Graph URLs and the sitemap use the live request host, so they're always correct.

**When the real domain is ready:** set `LIVE_HOST` in `includes/config.php` to your domain (e.g. `boatrentcyprus.com`). Indexing, `robots.txt` (`Allow` + sitemap) and the meta tags switch on automatically — no other changes needed.

## Admin dashboard

Visit **http://localhost:8011/admin/login.php**

- **Username:** `admin`
- **Password:** `admin123`

From the dashboard you can:
- View stats and recent inquiries
- Add / edit / remove boats (`Boats` tab)
- Read customer inquiries and set their status — New / Contacted / Closed (`Inquiries` tab)
- Moderate guest video reels and pick which ones run on the homepage (`Reels` tab)

> Change the default password before deploying: it's seeded in `includes/db.php` (`init_schema`). Update the row in the `admins` table with a new `password_hash(...)` value.

## Structure

| Path | Purpose |
|------|---------|
| `index.php` | Homepage — hero search, destinations, featured fleet, how-it-works |
| `boats.php` | Full fleet browse with filters (town, type, guests, sort, search) |
| `city.php?slug=` | Per-city listing page (Limassol, Paphos, Larnaca, Ayia Napa, Protaras, Latsi) |
| `boat.php?id=` | Vessel detail + inquiry form |
| `submit-inquiry.php` | Inquiry POST handler (CSRF-protected) → saves to DB |
| `share-reel.php` | `/share-your-reel` — guests upload a short video testimonial |
| `submit-reel.php` | Reel upload handler — size/type checks, rate limit → moderation queue |
| `about.php`, `contact.php` | Static marketing pages |
| `includes/` | `db.php` (schema + seed), `functions.php` (helpers), `header.php`, `footer.php`, `boat-card.php`, `reels-section.php` |
| `admin/` | Login, dashboard, boats CRUD, inquiries inbox, reel moderation |
| `uploads/reels/` | Guest-uploaded videos + captured poster frames (gitignored) |
| `css/style.css`, `js/main.js` | Shared front-end assets |

## Notes

- Inquiries are stored in the database and shown in the dashboard (no mail server needed). To add email notifications later, hook into `submit-inquiry.php` after the `INSERT`.
- Placeholder photos load from Unsplash, so an internet connection is needed for images. Swap the URLs in `includes/db.php` (and the hero `<img>` tags) for your own once you have real photography; you can drop files into `images/`.
- Forms use CSRF tokens and server-side validation; the admin area is session-protected.
- **Destination covers** — the homepage destination cards use `images/destinations/<city-slug>.webp`: a yacht that operates from that town, pre-cropped to the card's 800×576. Serving the originals (1600–1920px wide) would cost ~1.3 MB for pixels the browser crops away; cropped they total ~350 KB. `city_cover_image()` in `includes/functions.php` names the vessel behind each one and falls back to the city's scenery photo if a file is missing. To swap one, drop a new 800×576 WebP at that path — or regenerate from a boat photo:

  ```bash
  php -r '$s="images/azimut-62/image-1.webp"; $o="images/destinations/ayia-napa.webp";
  [$w,$h]=getimagesize($s); $im=imagecreatefromwebp($s); $ta=800/576;
  if ($w/$h > $ta) { $sw=(int)round($h*$ta); $sh=$h; $sx=(int)round(($w-$sw)/2); $sy=0; }
  else { $sw=$w; $sh=(int)round($w/$ta); $sx=0; $sy=(int)round(($h-$sh)/2); }
  $d=imagecreatetruecolor(800,576); imagecopyresampled($d,$im,0,0,$sx,$sy,800,576,$sw,$sh); imagewebp($d,$o,82);'
  ```

  City *hero* banners still use the scenery photos (`cities.image_url`) — only the homepage cards changed.
- **Compressing reels** — shared hosting can't transcode (no ffmpeg, `exec` disabled), so whatever is uploaded is exactly what every visitor downloads. Phone and stock clips are wildly over-bitrate for a player that's ~420px wide: the three starter clips totalled 100 MB before re-encoding and 17.5 MB after, with no visible difference. Run new reels through this before featuring them:

  ```bash
  ffmpeg -i in.mp4 -vf "scale='min(1080,iw)':-2" -c:v libx264 -profile:v high -pix_fmt yuv420p -crf 24 -preset slow -maxrate 3M -bufsize 6M -c:a aac -b:a 128k -movflags +faststart out.mp4
  ```

  `+faststart` is the important flag — it moves the index to the front of the file so playback starts while the rest is still downloading. `yuv420p` keeps it playable in every browser.
- **Upload caching** — `uploads/.htaccess` serves reels with `Cache-Control: immutable, max-age=1 year`, which is safe because every upload gets a random, never-reused filename. The corollary: **never overwrite a file in `uploads/reels/` in place** — browsers and CDNs won't re-fetch it. Replace a reel by uploading a new one and deleting the old.
- **Guest reels** — customers submit clips at `/share-your-reel`. Uploads are capped by `REEL_MAX_MB` in `includes/config.php` (60 MB), limited to MP4/MOV/WebM by sniffed MIME type, rate-limited to 3 per IP per hour, and stored under a random server-generated filename. Nothing is public until an admin approves it, and only reels ticked **On homepage** appear in the strip below the hero. The browser captures a poster frame on upload so the homepage shows images rather than loading every video.
