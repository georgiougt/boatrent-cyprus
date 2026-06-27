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
3. **Permissions** — the `data/` directory must be writable by PHP (typically already `755`/owned by your user). The bundled `data/.htaccess` blocks the database from web access.
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

> Change the default password before deploying: it's seeded in `includes/db.php` (`init_schema`). Update the row in the `admins` table with a new `password_hash(...)` value.

## Structure

| Path | Purpose |
|------|---------|
| `index.php` | Homepage — hero search, destinations, featured fleet, how-it-works |
| `boats.php` | Full fleet browse with filters (town, type, guests, sort, search) |
| `city.php?slug=` | Per-city listing page (Limassol, Paphos, Larnaca, Ayia Napa, Protaras, Latsi) |
| `boat.php?id=` | Vessel detail + inquiry form |
| `submit-inquiry.php` | Inquiry POST handler (CSRF-protected) → saves to DB |
| `about.php`, `contact.php` | Static marketing pages |
| `includes/` | `db.php` (schema + seed), `functions.php` (helpers), `header.php`, `footer.php`, `boat-card.php` |
| `admin/` | Login, dashboard, boats CRUD, inquiries inbox |
| `css/style.css`, `js/main.js` | Shared front-end assets |

## Notes

- Inquiries are stored in the database and shown in the dashboard (no mail server needed). To add email notifications later, hook into `submit-inquiry.php` after the `INSERT`.
- Placeholder photos load from Unsplash, so an internet connection is needed for images. Swap the URLs in `includes/db.php` (and the hero `<img>` tags) for your own once you have real photography; you can drop files into `images/`.
- Forms use CSRF tokens and server-side validation; the admin area is session-protected.
