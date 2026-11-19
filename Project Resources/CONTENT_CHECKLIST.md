# TZM Portfolio — Content checklist (Phase 0)

Aligned with HLD §16. Run **`php artisan db:seed --class=PortfolioContentSeeder`** after clone or when refreshing demo content.

**Identity overrides (`.env`):** `PORTFOLIO_NAME`, `PORTFOLIO_TITLE`, `PORTFOLIO_CONTACT_EMAIL`, `PORTFOLIO_GITHUB_URL`, `PORTFOLIO_LINKEDIN_URL`, `PORTFOLIO_LOCATION`

---

## Design tokens (locked)

| Token | Value | Where |
|-------|--------|--------|
| Accent | Primary navy `#18283a` | `tailwind.config.js`, `resources/css/app.css` |
| Sans fonts | Inter, Figtree | Google fonts in `resources/views/app.blade.php` |
| Mono | JetBrains Mono | Blog code blocks |
| Dark mode | `class` on `<html>` | `PublicLayout` + admin toggle |

---

## Checklist

| Item | Seeded? | You still need to |
|------|---------|-------------------|
| Display name & title | Yes | Confirm spelling in `.env` or Site Profile |
| One-line pitch / bio / goals / working style | Yes | Edit in **Site Profile** with your real story |
| Professional photo | Placeholder SVG | Run `php artisan portfolio:personalize` or upload in Site Profile |
| Resume PDF | Placeholder PDF | Same command or upload real PDF in Site Profile |
| Contact email, GitHub, LinkedIn | `.env` `PORTFOLIO_*` | Update with your real URLs when ready |
| Skills & categories | Yes | Adjust levels/names in admin if needed |
| Services (3) | Yes | Edit copy in admin |
| Experience / education | Yes | Replace with your real dates & orgs |
| Projects (6 case studies) | Yes | Add screenshots, repo/demo links, more projects |
| Testimonials | Empty (samples hidden) | Add quotes in Admin → Testimonials |
| Blog posts (3 starters) | Yes, published | Edit/unpublish in Admin → Posts |
| FAQs on Contact | Yes (in code) | Edit `ContactController::faqs()` if needed |
| Domain name | No | DNS when you deploy (Phase 7 / ops) |

---

## Placeholder policy

- Seeded **repo/demo URLs** are `null` unless this repo’s app URL is set for the portfolio project itself.
- Sample testimonials are **fictional** — swap before launch.
- Anything marked “Yes” in the table is safe to run locally; treat “You still need to” as pre-launch.

---

## Commands

```bash
php artisan storage:link
php artisan db:seed --class=PortfolioContentSeeder
php artisan portfolio:personalize   # sync .env identity + placeholder photo/resume
php artisan portfolio:personalize --force   # replace existing photo/resume files
```