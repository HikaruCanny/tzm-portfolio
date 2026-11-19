# Local setup — new device

Paths assume the repo is cloned to your machine. Replace `/path/to/vue-ai-laravel-project` with your folder.

---

## Git — first time on a new device

```bash
# Clone (HTTPS or SSH)
git clone <your-remote-url> vue-ai-laravel-project
cd vue-ai-laravel-project

# See current branch and last commits
git status
git log -5 --oneline

# After you pull updates from another machine
git pull origin main
```

**Do not commit** `.env` (secrets). It is gitignored; copy from `.env.example`.

**Typical workflow**

```bash
git status
git add <files>          # or git add -A for all tracked changes
git commit -m "message"
git push origin main     # when remote is configured
```

---

## Terminal 1 — one-time setup (not left running)

```bash
cd /path/to/vue-ai-laravel-project

composer install
npm install
cp .env.example .env     # if .env missing
# Edit .env: DB_*, PORTFOLIO_*, MAIL_*, APP_URL

php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed --class=PortfolioContentSeeder
php artisan portfolio:personalize
php artisan config:clear
npm run build
```

| Step | Purpose |
|------|---------|
| `composer install` / `npm install` | PHP and JS dependencies |
| `.env` | Database, mail, contact info (`PORTFOLIO_*`) |
| `migrate` | Database tables |
| `storage:link` | Public URLs for photo/resume uploads |
| `db:seed` / `portfolio:personalize` | Content + sync profile from config |
| `npm run build` | Compiled frontend assets |

Shortcut: `composer run setup` (then seed/personalize/storage:link if not included).

---

## Terminal 2 — run the site (keep open while developing)

**Option A — single command**

```bash
composer run dev
```

**Option B — two processes**

```bash
# 2a — backend
php artisan serve

# 2b — frontend hot reload
npm run dev
```

Open **http://localhost:8000**

| Admin login (after seed) | |
|--------------------------|--|
| URL | `/login` |
| Email | `admin@tzm.dev` |
| Password | `password` |
| Site Profile (phone, links) | `/admin/profile` (superadmin) |

---

## Terminal 3 — Mailpit (optional; email testing)

```bash
mailpit
```

- UI: http://127.0.0.1:8025  
- `.env`: `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`  
- Then: `php artisan config:clear`

---

## Terminal 4 — queue worker (optional)

Only if `QUEUE_CONNECTION=database` and jobs do not run on their own:

```bash
php artisan queue:work
```

For local dev you can set `QUEUE_CONNECTION=sync` in `.env` and skip this terminal.

---

## After `git pull`

```bash
composer install
npm install
php artisan migrate
php artisan portfolio:personalize
npm run build
php artisan config:clear
```

---

## Quick checks

```bash
php artisan test --compact
```