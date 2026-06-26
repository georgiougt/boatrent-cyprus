# BoatRent Cyprus

A yacht & boat rental marketplace for Cyprus — per-city listings, vessel detail pages with an inquiry flow, and a full admin dashboard. Built with PHP, HTML, Tailwind (CDN) and SQLite.

## Running locally

PHP 8+ is required (ships with SQLite support). From inside this folder:

```bash
php -S localhost:8011 router.php
```

Then open **http://localhost:8011** in your browser. The `router.php` argument enables clean URLs (e.g. `/paphos`, `/boat/azure-princess`, `/blog/best-time-to-sail-in-cyprus`) on the built-in dev server. In production on Apache, the bundled `.htaccess` provides the same routing (no router argument needed); on nginx, map the same rules via `try_files`.

The SQLite database is created and seeded automatically on first load at `data/boatrent.sqlite` (6 cities, 16 sample boats, 2 sample inquiries). To start fresh, delete that file and reload any page.

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
