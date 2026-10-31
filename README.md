# Raza Sandhu — Portfolio

Multi-page portfolio for Raza Sandhu (Social Media Manager & Performance Marketing Specialist), built with Laravel 13, Blade, Bootstrap 5 (grid/utilities), Three.js, GSAP + ScrollTrigger and Lenis, compiled with Vite.

## Run locally

```sh
composer install
npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite   # SQLite needs extension=pdo_sqlite in php.ini
php artisan migrate --seed       # creates the tables and the admin account (prints its password)
php artisan storage:link         # optional: lets the web server serve uploads directly (Laravel serves them otherwise)
npm run build                    # or: npm run dev (hot reload)
php artisan serve
```

The site works even before the database exists — it then shows the default content from `config/portfolio.php`.

## Admin panel

Sign in at **/admin** with the seeded account. Forgot the password? Click **Forgot password?** on the sign-in page. A one-time reset link (valid 60 minutes) is emailed to the admin address, so email must be set up (see *Email* below). If email isn't working, `php artisan admin:password` on the server still sets a new password.

- **Website pages** — one editor per page (Home, About, Services, Portfolio, Results, International, Contact, Footer). Each mirrors the page section by section: every heading, paragraph, button, list, figure, image and video is editable, and lists (services, roles, countries, chart values…) can be added to, reordered and removed. Each section saves on its own and goes live immediately; **Restore original** brings back the default.
- **Messages** — every contact-form submission is saved here (and still emailed), with read/unread, search and delete.
- **Settings** — profile photo, name, sign-in email and password.

How it works: `config/portfolio.php` holds the default content and `config/admin.php` describes the editable sections. Saved sections are stored in the `contents` table and override the defaults (`App\Support\ContentRepository`). Uploaded images are resized and converted to WebP (`App\Support\MediaStore`).

Uploads are limited by PHP's `upload_max_filesize` / `post_max_size` (2 MB / 8 MB by default) — raise them in `php.ini` to upload screen recordings.

## Where things live

| What | Where |
| --- | --- |
| **Default copy, figures and links** | `config/portfolio.php` (or edit live in the admin panel) |
| Admin panel schema / controllers / views | `config/admin.php`, `app/Http/Controllers/Admin/`, `resources/views/admin/` |
| Routes | `routes/web.php` |
| Pages | `resources/views/pages/*.blade.php` |
| Layout, nav, footer | `resources/views/components/layouts/app.blade.php`, `resources/views/partials/` |
| Reusable Blade components | `resources/views/components/` (`icon`, `page-hero`, `section-head`) |
| Contact form | `ContactController`, `ContactRequest` (validation), `Mail/ContactMessage` |
| Styles (design tokens first) | `resources/scss/` |
| Motion (Lenis ↔ GSAP sync, reveals, counters, tilt) | `resources/js/core/motion.js` |
| Page-specific JS (loaded per page) | `resources/js/pages/` |
| Three.js scenes (one per page, lazy-loaded) | `resources/js/three/scenes/`, shared engine in `resources/js/three/engine.js` |

## Email (contact form + password reset)

Contact-form submissions go to `CONTACT_INBOX`; password reset links go to the admin's sign-in email. With the default `MAIL_MAILER=log`, emails are only written to `storage/logs/laravel.log`.

To send real emails for free with Gmail:

1. Turn on 2-Step Verification for the Gmail account, then create an app password at <https://myaccount.google.com/apppasswords>.
2. In `.env`, set:
   ```ini
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=you@gmail.com
   MAIL_PASSWORD="abcd efgh ijkl mnop"   # the app password, not your normal one
   MAIL_FROM_ADDRESS=you@gmail.com
   ```
3. Run `php artisan config:clear` and restart the server.

The form has CSRF protection, server-side validation, a honeypot field and a rate limit of 5 submissions per minute per IP.

## Tests

```sh
php artisan test
```

These cover every page rendering, the contact form (validation, delivery, honeypot, rate limit), and a check that no secondary/intermediate education (FSC, Matric, etc.) appears anywhere on the site.

## Performance & accessibility notes

- Three.js loads as a separate chunk after first paint. The home globe only loads when scrolled near. Scenes pause when off-screen or when the tab is hidden, and cap pixel ratio and particle counts on phones and low-power devices.
- `prefers-reduced-motion`: Lenis, reveals and counters are disabled, and scenes render one still frame.
- Without WebGL, a CSS gradient replaces each scene. Content never depends on JavaScript, and a fallback timer reveals everything if the animation bundle fails to load.

## Deploying to Wasmer Edge (free plan)

Locally the site uses SQLite. On Wasmer it uses Wasmer's free MySQL database, and a 1 GB volume holds uploads. Everything is in `deploy/wasmer/`:

| File | What it does |
| --- | --- |
| `app.yaml` | App name, settings, the MySQL database and the `storage` volume |
| `wasmer.toml` | Runs PHP 8.3's web server on `public/` through `router.php` |
| `php-config/php.ini` | Upload limits (20 MB) and the settings Laravel needs |
| `build.sh` | Builds a clean package (production packages, compiled CSS/JS) and deploys it |

**First deploy** (in Git Bash, from the project folder):

1. Install the Wasmer CLI (PowerShell): `iwr https://win.wasmer.io -useb | iex`, then run `wasmer login`.
2. Optional: change `name:` and `APP_URL` in `deploy/wasmer/app.yaml`. The site's address will be `https://<name>.wasmer.app`.
3. Run `bash deploy/wasmer/build.sh --deploy` and answer the prompts. Choose your account; the app name comes from `app.yaml`.
4. Add the secrets. In Git Bash, run these from `deploy/wasmer/` so the CLI knows which app you mean:
   ```sh
   cd deploy/wasmer
   wasmer app secrets create APP_KEY "$(php ../../artisan key:generate --show)"
   wasmer app secrets create ADMIN_PASSWORD "choose-a-strong-password"
   wasmer app secrets create MAIL_USERNAME "you@gmail.com"
   wasmer app secrets create MAIL_PASSWORD "your 16-character Gmail app password"
   wasmer app secrets create MAIL_FROM_ADDRESS "you@gmail.com"
   cd ../..
   ```
5. Deploy again so the app picks up the secrets: `bash deploy/wasmer/build.sh --deploy`.
6. Open the site once. The first visit creates the database tables and the admin account (your email with `ADMIN_PASSWORD`). Then sign in at `/admin`.

**Updating the live site later:** run `bash deploy/wasmer/build.sh --deploy`. New migrations run by themselves on the next visit (`AUTO_MIGRATE`, because the free plan has no SSH). Content, messages and uploads stay: they live in the database and on the volume, which deploys never touch.

Notes:
- Never rename the volume (`storage`) in `app.yaml`. A new name means a new, empty volume.
- Local edits and uploads are not copied to the live site. Make content edits in the live admin panel.
- `composer.json` pins `platform.php` to 8.3 so `composer update` only installs packages that run on Wasmer's PHP 8.3.
