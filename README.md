# TZM Portfolio

Personal portfolio and CMS built with **Laravel**, **Inertia.js**, and **Vue 3**. Public marketing pages, blog, and contact flow are database-driven; a protected admin area manages profile, projects, experience, skills, services, testimonials, posts, and inbound messages.

**Repository:** [github.com/HikaruCanny/tzm-portfolio](https://github.com/HikaruCanny/tzm-portfolio)

## Stack

| Layer | Technology |
|--------|------------|
| Backend | Laravel 13, PHP 8.3+ |
| Frontend | Vue 3, Inertia v3, Vite, Tailwind CSS v4 |
| Auth | Laravel Fortify (admin roles, invitations, 2FA for superadmin) |
| Routes (typed) | Laravel Wayfinder |
| Tests | Pest, PHPStan, Pint, GitHub Actions |

## Features

**Public**

- Home, About, Experience, Skills, Services, Projects, Contact
- Blog with Markdown, syntax highlighting, RSS (`/feed.xml`)
- Contact form with honeypot and admin inbox
- SEO: meta tags, Open Graph, JSON-LD, `/sitemap.xml`, `/robots.txt`
- Light / dark theme

**Admin** (`/admin`)

- CRUD for portfolio content, blog posts/tags, technologies
- Site profile (photo, resume, contact links, phone)
- Contact messages: read, reply by email, mark handled
- In-app notifications for new contact messages
- Activity log and user management (superadmin)

## Requirements

- PHP 8.3+, Composer
- Node.js 20+ and npm
- MySQL (or compatible database configured in `.env`)

## Quick start

```bash
git clone https://github.com/HikaruCanny/tzm-portfolio.git
cd tzm-portfolio

composer install
npm install
cp .env.example .env
# Edit .env: APP_URL, DB_*, PORTFOLIO_* (see below)

php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed --class=PortfolioContentSeeder
php artisan portfolio:personalize
npm run build
```

Run the app:

```bash
composer run dev
```

Open [http://localhost:8000](http://localhost:8000).

Shortcut for first-time install: `composer run setup` (then run migrate seed, `storage:link`, `portfolio:personalize`, and seed if you need demo content).

### Personal contact info

Set these in `.env` (also in `.env.example`):

```env
PORTFOLIO_NAME="Your Name"
PORTFOLIO_TITLE="Your title"
PORTFOLIO_CONTACT_EMAIL="you@example.com"
PORTFOLIO_PHONE="+..."
PORTFOLIO_GITHUB_URL="https://github.com/..."
PORTFOLIO_LINKEDIN_URL="https://www.linkedin.com/in/..."
```

Then sync the database profile:

```bash
php artisan portfolio:personalize
```

Replace photo and resume in **Admin → Site Profile** (`/admin/profile`).

### Default admin (after seed)

| | |
|---|---|
| Login | `/login` |
| Email | `admin@tzm.dev` |
| Password | `password` |

Change this password before any public deployment.

### Email (local)

Use [Mailpit](https://github.com/axllent/mailpit) with `MAIL_MAILER=smtp`, `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`. Contact replies and notifications appear in the Mailpit UI (default http://127.0.0.1:8025).

For local development you can set `QUEUE_CONNECTION=sync` so mail sends immediately without `php artisan queue:work`.

## Development

```bash
# Format PHP (changed files)
vendor/bin/pint --dirty

# Run tests
php artisan test --compact

# Full CI-style check
composer ci:check
```

## Project docs

| File | Purpose |
|------|---------|
| [Project Resources/LOCAL_SETUP.md](Project%20Resources/LOCAL_SETUP.md) | Terminals, git workflow, Mailpit, queues |
| [Project Resources/CONTENT_CHECKLIST.md](Project%20Resources/CONTENT_CHECKLIST.md) | Content to replace before launch |
| [Project Resources/IMPLEMENTATION_PLAN.md](Project%20Resources/IMPLEMENTATION_PLAN.md) | Phase tracker vs HLD |

## License

MIT