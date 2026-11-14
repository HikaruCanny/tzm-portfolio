# TZM Portfolio – Implementation Plan (living document)

**Source HLD:** `TZM Portfolio Website – High-Level Design (HLD).md` v1.2  
**Last codebase audit:** 2026-11-11 (after Phase 1 and Phase 2 implementation)  
**Test suite:** 66 Pest tests passing (`php artisan test --compact`)

**Status legend:** `DONE` | `PARTIAL` | `NOT STARTED`

---

## Phase summary (executive)

| Phase | Name | Status | % (estimate) | Notes |
|-------|------|--------|--------------|-------|
| 0 | Content & design | PARTIAL | ~20% | Seeded/demo content in UI; no repo checklist artifact |
| 1 | Foundation | DONE | ~95% | Error pages, blog nav, theme, CI. Staging server deploy is still an ops task |
| 2 | Data & admin | DONE | ~95% | Invites, roles, technologies, WebP uploads, activity log, superadmin 2FA gate |
| 3 | Public pages | PARTIAL | ~75% | DB-driven pages; HLD home/blog/testimonials gaps |
| 4 | Blog | NOT STARTED | ~10% | Static `Blog.vue` / `BlogPost.vue` only |
| 5 | Contact & notifications | PARTIAL | ~45% | Form + inbox + honeypot; no mail/Reverb/bell |
| 6 | AI chatbot | NOT STARTED | 0% | — |
| 7 | Polish & launch | NOT STARTED | ~5% | CI only; no SEO/ops bundle |

**Finished in code:** **Phase 1** and **Phase 2**. Staging hosting is the only Phase 1 item left, and it needs a server. Contact email, live notifications, the blog, and the chatbot are later phases.

**Closest unfinished product work:** **Phase 4** (real blog) and **Phase 5** (email and live notifications).

**Recommended next work:** finish **Phase 2 gaps** → **Phase 4** → **Phase 5** → minimal **Phase 7** for launch → **Phase 6** optional.

---

## Phase 0 — Content & design

| Task | Status | Evidence |
|------|--------|----------|
| Real content checklist (HLD §16) | NOT STARTED | No tracked checklist in repo |
| Placeholder policy for production | PARTIAL | Empty states exist; seeder uses demo copy |
| Design tokens (indigo, Inter, etc.) | PARTIAL | Indigo + Tailwind in `PublicLayout`, pages |
| Wireframes / prototype alignment | PARTIAL | HLD links prototype; UI matches direction |

**Remaining:** TZM-supplied content; replace demo defaults; lock accent/fonts in theme config.

---

## Phase 1 — Foundation

| Task | Status | Evidence |
|------|--------|----------|
| Laravel + Inertia + Vue + Vite + Tailwind | DONE | Project structure, `PublicLayout`, pages |
| Light / dark theme + persistence | DONE | `PublicLayout.vue`, admin theme toggle |
| Sticky nav, mobile menu | DONE | `PublicLayout.vue` |
| Admin shell layout | DONE | `AdminLayout.vue` |
| GitHub Actions CI | DONE | `.github/workflows/tests.yml`, `composer ci:check` |
| Custom 404 / 403 / 500 (Inertia) | DONE | `resources/js/Pages/Error.vue` |
| Staging deployment | NOT STARTED | Needs a server; not part of the app code |
| Blog link in public nav | DONE | `PublicLayout.vue` |

**Remaining:** Error pages, add Blog to nav when Phase 4 ships, staging deploy doc.

---

## Phase 2 — Data model & admin

### Schema

| Table (HLD §6) | Status |
|----------------|--------|
| `users` + `role` | DONE |
| `profile` | DONE |
| `experiences`, `skill_categories`, `skills` | DONE |
| `projects`, `project_images`, `technologies`, `project_technology` | DONE (images table; upload UI unclear) |
| `services`, `contact_messages`, `invitations`, `activity_log` | DONE (migrations) |
| `posts`, `tags`, `post_tag` | NOT STARTED |
| `contact_replies` | NOT STARTED |
| `chat_*`, `knowledge_chunks` | NOT STARTED |
| `notifications` table | NOT STARTED (model trait on User only) |

**Migrations:** `2026_11_09_164214_*`, `2026_11_09_164239_*`, `2026_11_09_142427_*`.

### Auth & roles

| Task | Status | Evidence |
|------|--------|----------|
| Login, forgot/reset password | DONE | Fortify + Inertia auth pages |
| 2FA | PARTIAL | Fortify 2FA + `TwoFactorChallengeTest` |
| `/admin` → login when guest | DONE | `EnsureUserIsAdmin` + `AdminTest` |
| Superadmin-only `/admin/profile` | DONE | Middleware + tests |
| Invite-only registration | DONE | `RegisterInvitedAdmin` + invite code on `/register` |
| Laravel policies / gates | DONE | `app/Policies`, `manage-portfolio`, `manage-site` |
| Superadmin user/invite management UI | DONE | `/admin/users`, `/admin/invitations` |
| Activity log writes + viewer | DONE | `RecordsContentActivity`, `/admin/activity` |
| Superadmin 2FA | PARTIAL | Two-factor can be turned on from Security settings. Sign-in goes straight to `/admin` |

### Admin CRUD

| Area | Status |
|------|--------|
| Dashboard + unread message stat | PARTIAL | `ContactMessage::unread()` on dashboard |
| Projects CRUD + tech sync | DONE | `Admin\ProjectController` |
| Experiences, skills, services | DONE | Controllers + Vue index pages |
| Messages list, mark read, delete | PARTIAL | No `handled_at`, reply, email |
| Site profile (superadmin) | PARTIAL | `Admin\ProfileController` — verify resume/photo upload |
| Technologies admin CRUD | DONE | `/admin/technologies` |
| Project image upload + WebP | DONE | `StoresPortfolioImage` |
| Profile photo and resume upload | DONE | Superadmin profile form; public `/resume` download |
| Blog admin | NOT STARTED | — |

**Tests:** `tests/Feature/AdminTest.php` (dashboard, project create, role gates).

---

## Phase 3 — Public pages

| Page | Route | Status | Evidence |
|------|-------|--------|----------|
| Home | `/` | PARTIAL | `Welcome.vue` — hero, tech strip, featured projects; no latest blog/testimonials |
| About | `/about` | PARTIAL | Profile-driven; resume link if `resume_path` set |
| Experience | `/experience` | DONE | `PortfolioController@experience` |
| Skills | `/skills` | DONE | Categories + skills |
| Services | `/services` | DONE | |
| Projects list | `/projects` | DONE | Filters + search client-side |
| Project detail | `/projects/{slug}` | DONE | Case study fields, related, draft 404 test |
| Blog (public) | — | NOT STARTED | See Phase 4 |
| Contact | `/contact` | PARTIAL | Form; FAQ accordion per HLD unverified |

**Tests:** `tests/Feature/PublicPortfolioTest.php` (pages, contact, honeypot, project show).

**Remaining:** Home blog strip + testimonials; project sort; secure resume download route.

---

## Phase 4 — Blog

| Task | Status |
|------|--------|
| DB models + migrations | NOT STARTED |
| Public list/detail/tag routes | NOT STARTED | Stubs in `web.php` render static Vue |
| Admin Markdown editor + draft/publish | NOT STARTED |
| Code highlighting, RSS `/feed.xml` | NOT STARTED |
| Replace hardcoded `articles` in `Blog.vue` | NOT STARTED |

---

## Phase 5 — Contact, email & notifications

| Task | Status | Evidence |
|------|--------|----------|
| Validated contact form | DONE | `ContactController` |
| Throttle | DONE | `throttle:5,1` on route |
| Honeypot + `is_spam` | DONE | `_hp` field + tests |
| Email notification on new message | NOT STARTED | No Mailable/Event |
| `NewContactMessage` event + queue listener | NOT STARTED | |
| Database notifications + bell + Echo/Reverb | NOT STARTED | |
| Admin reply + `contact_replies` | NOT STARTED | |
| Mark handled + filter spam in inbox | PARTIAL | `handled_at` column; UI not wired |
| Resume PDF managed + download | PARTIAL | Column on profile; direct href only |

---

## Phase 6 — “Ask TZM” chatbot

| Task | Status |
|------|--------|
| All HLD §7.3 items | NOT STARTED |

---

## Phase 7 — Polish & launch

| Task | Status | Evidence |
|------|--------|----------|
| CI (Pest, Pint, npm check) | DONE | `composer ci:check` |
| `sitemap.xml`, `robots.txt`, `feed.xml` | NOT STARTED | |
| Per-page meta / OG / JSON-LD | PARTIAL | Inertia `<Head title>` only |
| Inertia SSR | NOT STARTED | |
| Security headers middleware | NOT STARTED | |
| Backups, monitoring, production deploy | NOT STARTED | |
| Playwright E2E | NOT STARTED | |

---

## Suggested sprint order (updated)

1. **Sprint A:** Phase 2 — invite registration, technologies CRUD, project images, activity log (minimal).
2. **Sprint B:** Phase 4 — full blog pipeline.
3. **Sprint C:** Phase 5 — mail + notifications (+ optional Reverb).
4. **Sprint D:** Phase 3 + 7 — home blog, nav blog, 404/500, SEO basics, deploy.
5. **Sprint E:** Phase 0 content pass + Phase 6 chatbot (optional).

---

## Audit commands (for next review)

```bash
php artisan test --compact
php artisan route:list --except-vendor
```

Re-run this audit after each major merge from another agent and update the **Phase summary** table and `%` estimates.
