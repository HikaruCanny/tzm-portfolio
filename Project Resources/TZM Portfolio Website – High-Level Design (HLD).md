# TZM Portfolio Website – High-Level Design (HLD)

**Owner:** TZM  |  **Role positioning:** Full-Stack Developer (Laravel + Vue.js)  |  **Version:** 1.2 (Draft)

## 1. Purpose and Goals

This document describes the high-level design of a modern portfolio website with a clean layout and light and dark themes. The site's main goal is to help TZM get a professional developer job by showing real skills, real projects, and clear communication.

**Prototype:** a clickable prototype (pages, admin panel, login and registration, and admin notifications) accompanies this document: [TZM Portfolio Prototype](https://claude.ai/artifact/J8Zbw7GCZadJLUh3iS7wco).

**Goals**

- Present TZM as a credible Full-Stack Developer within the first 10 seconds of a visit.
- Showcase 10+ projects with filters and case studies.
- Publish tech blog posts to show learning and communication skills.
- Make it very easy for recruiters to contact TZM or download a resume.
- Offer an AI-powered "Ask TZM" chatbot that answers questions about TZM's skills and projects.
- Let TZM manage all content without editing code (custom admin panel), and be notified right away when a visitor sends a message.

**Non-goals (for v1)**

- Public visitor accounts or self-registration, comments system, e-commerce, or paid features. Only invited admins have accounts.
- Multi-language support (can be added later).
- Next.js or any non-Laravel backend.

**Content rule:** No invented experience, projects, or statistics. Anything not yet provided is shown as a clearly marked placeholder (e.g. `[PLACEHOLDER: project description]`) until TZM supplies real content.

## 2. Target Audience

| Audience | What they want | How the site helps |
| --- | --- | --- |
| Recruiters / HR | Quick skill summary, resume, contact | Hero section, skills, resume download, contact form |
| Hiring managers / tech leads | Real code, project depth, problem solving | Case studies, GitHub links, live demos, blog |
| Freelance / client visitors | What TZM can build for them | "What I Can Build" page, contact |
| Other developers | Useful tech content | Blog with tags and search |

## 3. Site Map and Pages

The site has 8 main pages plus detail pages.

| # | Page | Route | Purpose |
| --- | --- | --- | --- |
| 1 | Home | `/` | Hero, short intro, featured projects, testimonials, latest blog posts, call to action |
| 2 | About | `/about` | Story, goals, working style, photo, downloadable resume |
| 3 | Experience | `/experience` | Timeline of work, internships, education, certifications |
| 4 | Skills | `/skills` | Skills grouped by category (Frontend, Backend, Database, Tools), with optional level bars |
| 5 | Projects | `/projects` | Filterable grid of 10+ projects (by tech, type, category) |
| 5a | Project detail | `/projects/{slug}` | Case study: problem, solution, tech, screenshots, links |
| 6 | What I Can Build | `/services` | Types of apps TZM can build (admin dashboards, REST APIs, web apps) |
| 7 | Blog | `/blog` | Tech articles with tags, search, pagination |
| 7a | Blog post | `/blog/{slug}` | Article with code highlighting, reading time, related posts |
| 8 | Contact | `/contact` | Contact form, FAQ, email, GitHub, LinkedIn |
| – | Floating chatbot | all pages | "Ask TZM" AI assistant widget |
| – | Admin | `/admin` | Private content management for admin and superadmin (login required) |
| – | 404 / 500 | – | Friendly error pages in site style |

**Authentication pages:** `/login` (admin sign in with email, password, remember me, and forgot password) and `/register` (invite-only admin registration with an invite code from the superadmin). There is no public sign-up. Visiting `/admin` while signed out redirects to `/login`.

Optional later additions: a `/uses` page (tools and setup), a `/now` page, and a `/resume` web page.

## 4. Technology Stack

| Layer | Choice | Reason |
| --- | --- | --- |
| Backend | Laravel (PHP) | Matches TZM's target role; routing, auth, validation, queues built in |
| Frontend | Vue.js 3 (Composition API) | Matches target role; component-based UI |
| Glue | Inertia.js | Single-page feel without building a separate API for every page |
| Styling | Tailwind CSS | Fast, consistent light and dark themes with design tokens |
| Database | MySQL (or MariaDB) | Common with Laravel; MySQL-compatible hosting everywhere |
| Build tool | Vite | Default in Laravel; fast dev server and builds |
| Auth (admin) | Laravel Breeze (Inertia + Vue stack) or Fortify | Secure login for two roles (admin, superadmin); permissions enforced with Laravel policies and gates |
| Rich text for blog | Markdown (stored in DB) rendered on the server | Easy for developers, safe, good for code blocks |
| Code highlighting | Shiki or highlight.js | Nice code blocks in blog posts |
| Email | Laravel Mail with SMTP provider (Resend, Mailgun, Brevo, or similar) | Contact form notifications |
| AI | LLM provider API called from Laravel only | No API key in the browser |
| Cache / queue | Database or Redis driver | Start simple, scale later |
| Version control / CI | Git, GitHub, GitHub Actions | Also doubles as portfolio evidence |

Supporting skills shown on the site (from TZM's skill list): Vue.js, JavaScript, HTML, CSS, Tailwind, Laravel, PHP, Node.js, Express.js, REST API, MySQL, SQL Server, Git, GitHub, Postman, VS Code, IntelliJ IDEA.

**Notifications:** new-message alerts use Laravel notifications (database and mail channels) with Laravel Reverb (or Pusher) and Laravel Echo for live push in the admin panel.

## 5. System Architecture

```
   Visitor (Browser)
         |
         |  HTTPS
         v
  +----------------+
  |   Web server   |   Nginx + PHP-FPM (TLS, gzip, caching headers)
  +-------+--------+
          |
          v
  +--------------------------------------------+
  |            Laravel application             |
  |                                            |
  |  Routes -> Controllers -> Services         |
  |      |                        |            |
  |  Inertia responses       Chatbot service --+--> LLM provider API
  |  (Vue pages as props)    Mail service  ----+--> SMTP provider
  |      |                                     |
  |  Eloquent models                           |
  +----------+---------------------------------+
             |
             v
      +--------------+       +------------------+
      |    MySQL     |       | Storage (images, |
      |              |       | resume PDF)      |
      +--------------+       +------------------+
```

**How a page request works**

1. The browser requests a URL such as `/projects`.
2. Laravel runs the controller, reads data through Eloquent models, and returns an Inertia response.
3. The first load renders full HTML (good for SEO); later navigation swaps Vue pages without a full reload.
4. Vue renders the page from the props, and Tailwind styles it.

**Rendering and SEO:** Use Inertia server-side rendering (SSR) for public pages, or at minimum render the meta tags and key content on the server, so Google and social previews can read the pages. Blog and project pages must be indexable.

## 6. Data Model (High-Level)

| Table | Key fields | Notes |
| --- | --- | --- |
| `users` | id, name, email, password | Admin and superadmin accounts (TZM is the superadmin); role column |
| `profile` | name, title, bio, photo, resume\_path, email, github\_url, linkedin\_url, location | Single row, drives Home/About/Contact |
| `experiences` | id, type (work/education/cert), organization, role, start\_date, end\_date, description, sort\_order | Timeline |
| `skill_categories` | id, name, sort\_order | Frontend, Backend, Database, Tools |
| `skills` | id, category\_id, name, level (optional), icon, sort\_order | Level is optional and only if TZM wants it |
| `projects` | id, title, slug, summary, problem, solution, role, status, cover\_image, demo\_url, repo\_url, is\_featured, published\_at | Case study fields |
| `project_images` | id, project\_id, path, caption, sort\_order | Screenshots |
| `technologies` | id, name, slug | Used for project filters |
| `project_technology` | project\_id, technology\_id | Many-to-many |
| `services` | id, title, description, icon, sort\_order | "What I Can Build" cards |
| `posts` | id, title, slug, excerpt, body\_markdown, cover\_image, status (draft/published), published\_at, reading\_time | Blog |
| `tags` | id, name, slug | Blog tags |
| `post_tag` | post\_id, tag\_id | Many-to-many |
| `contact_messages` | id, name, email, subject, message, ip\_hash, created\_at, handled\_at | Stored and emailed |
| `chat_sessions` | id, session\_token, created\_at | Anonymous sessions |
| `chat_messages` | id, session\_id, role, content, created\_at | For review and improvement; auto-delete after a retention period |
| `knowledge_chunks` | id, source\_type, source\_id, content, updated\_at | Approved content the chatbot may answer from |

**Added for roles and replies:** `users.role` (`admin` or `superadmin`), `contact_replies` (id, message\_id, user\_id, body, sent\_at) for replies sent from the admin inbox, and `activity_log` (id, user\_id, action, subject\_type, subject\_id, created\_at) recording who created, edited, or deleted content.

**Added for login and notifications:** `invitations` (id, code\_hash, role, created\_by, expires\_at, used\_at), `notifications` (Laravel default table: id, type, notifiable\_id, data, read\_at, created\_at), `password_reset_tokens` (Laravel default), and a `read_at` column on `contact_messages`.

## 7. Feature Design

### 7.1 Public Site

- **Home:** hero with name, title, short pitch, two buttons (View Projects, Contact Me), tech stack strip, featured projects, latest posts, final call to action.
- **Projects:** client-side filter chips (technology, category), search box, sort by newest or featured. Each card shows cover, title, summary, tech tags, and links. Each project has a case study page with: overview, problem, solution, my role, tech stack, key features, challenges and lessons, screenshots, and live and GitHub links.
- **Blog:** list with tag filter, search, and pagination; post page with table of contents, code highlighting, copy-code button, reading time, share links, and related posts. RSS feed at `/feed.xml`.
- **Contact:** validated form (name, email, subject, message), spam protection, success message, and direct links to email, GitHub, and LinkedIn.
- **Resume:** PDF download button on About and Contact; file managed from admin.

### 7.2 Admin Panel and Roles (custom, Inertia + Vue)

- Login-protected area at `/admin` with a dashboard (unread message count and quick actions), a left menu, and tables for posts, projects, and messages.
- Admin and superadmin: CRUD for experience, skills, projects (with image upload), services, blog posts (Markdown editor with preview, draft/publish), tags, and technologies.
- Inbox for contact messages: read, reply by email, and mark as handled, with an unread count and live notifications (admin and superadmin).
- Superadmin only: profile and resume, site settings, chatbot knowledge manager and chat log viewer, user and role management, and activity log.
- Image uploads are validated, resized, and converted to WebP.

**Roles and permissions.** The admin handles daily content work; the superadmin has full control. A single `role` column on `users` is enough at launch; `spatie/laravel-permission` can replace it if more roles are needed later.

| Area | Admin | Superadmin |
| --- | --- | --- |
| Blog posts and tags (create, edit, delete, publish) | Yes | Yes |
| Projects, experience, skills, services, technologies | Yes | Yes |
| Contact messages (read, reply, mark handled) | Yes | Yes |
| Profile, resume, site settings | No | Yes |
| Create, disable, and change roles of admin accounts | No | Yes |
| Chatbot settings, knowledge base, chat logs | No | Yes |
| Security settings, backups, activity log | No | Yes |

This matrix is a starting point and can be tightened later, for example letting admins edit but not delete projects.

### 7.3 "Ask TZM" AI Chatbot

**Behavior**

- A floating button at the bottom right opens a chat panel on every public page.
- It answers questions about TZM's skills, projects, experience, blog topics, and availability, and points visitors to the right page or the contact form.
- It must not invent facts. If the answer is not in the approved knowledge, it says so and suggests contacting TZM.

**Design**

1. The browser sends the message to `POST /api/chat` on the Laravel backend. No AI key is ever exposed to the frontend.
2. Laravel validates the input, applies rate limiting, and loads the recent conversation (limited number of turns).
3. `ChatbotService` retrieves relevant content from `knowledge_chunks` (built from profile, experience, skills, projects, and published posts). At launch, simple keyword or full-text search is enough; embeddings can be added later.
4. Laravel calls the LLM provider with a strict system prompt (role, tone, "answer only from the provided context", refuse unrelated or unsafe requests) plus the retrieved context.
5. The response is streamed or returned to the widget and saved in `chat_messages`.

**Safeguards**

- Rate limits per IP and per session; daily global spending cap in the provider dashboard.
- Maximum message length and maximum conversation length.
- Prompt-injection hardening: user text is never allowed to change the system rules; the model gets no tools and no access to private data.
- A friendly fallback message if the AI provider fails or the budget is reached (links to the contact page).
- Visible notice that the chatbot is AI-generated and chats may be stored; chat logs auto-deleted after a retention period (for example 30 to 90 days).
- Knowledge base contains only public portfolio content.

### 7.4 Login and Registration

**Pages:** `/login`, `/register`, `/forgot-password`, and `/reset-password/{token}`. Visiting `/admin` while signed out redirects to `/login`.

**Login**

- Email and password, with a show/hide password toggle, a "Remember me" option, and a "Forgot password?" link.
- Server-side validation with safe error messages (never reveal whether an email exists).
- Rate limiting (for example 5 attempts per minute per IP and email) and temporary lockout after repeated failures.
- The session is regenerated after login; the superadmin must also pass a two-factor code.

**Registration (invite-only)**

- There is no public sign-up. The superadmin creates an invitation (single-use code or signed link, with an expiry date and the role to grant).
- `/register` asks for full name, email, invite code, password, and password confirmation. A valid invite creates an admin account; without one the form is rejected.
- Passwords are hashed by Laravel (bcrypt or Argon2), with a minimum length of 8 characters (12 recommended).
- Password reset uses an emailed, signed, expiring link.

### 7.5 Admin Notifications for New Messages

**Goal:** TZM sees a new visitor message immediately, without checking the inbox.

**Flow**

1. A visitor submits the contact form (`POST /contact`); Laravel validates it and saves a `contact_messages` row. Spam-flagged submissions (honeypot) are saved quietly and do not notify.
2. A `NewContactMessage` event is dispatched and handled by a queued listener.
3. The listener creates a database notification for each admin and the superadmin, sends an email notification, and broadcasts a real-time event on a private admin channel (Laravel Reverb or Pusher with Laravel Echo). If real-time is not available, the admin panel polls every 30 to 60 seconds.

**Admin panel behavior**

- A bell icon in the top bar with a red unread count.
- A toast popup showing the sender and a short preview, with an "Open inbox" link.
- A "New" tag on unread messages, an unread count on the Messages menu item, and an unread count card on the dashboard.
- The unread count also appears in the browser tab title.
- Opening a message marks it as read (`read_at`); "Mark all as read" is available.
- Later options: Telegram or Slack webhook, or browser push notifications.

## 8. Key Routes and API Endpoints

| Method | Path | Purpose |
| --- | --- | --- |
| GET | `/`, `/about`, `/experience`, `/skills`, `/services` | Public pages (Inertia) |
| GET | `/projects`, `/projects/{slug}` | Project list and case study |
| GET | `/blog`, `/blog/{slug}`, `/blog/tag/{tag}` | Blog pages |
| GET | `/contact` | Contact page |
| POST | `/contact` | Submit contact form (throttled, validated, honeypot) |
| POST | `/api/chat` | Chatbot message (throttled) |
| GET | `/sitemap.xml`, `/feed.xml`, `/robots.txt` | SEO and RSS |
| GET/POST | `/admin/*` | Authenticated admin CRUD (user, settings, and chatbot routes are superadmin only) |

**Auth and notification routes:** `GET/POST /login`, `GET/POST /register` (invite code required), `POST /logout`, `GET/POST /forgot-password`, `GET/POST /reset-password/{token}`, `GET /admin/notifications`, `POST /admin/notifications/{id}/read`, and `POST /admin/notifications/read-all`. Live updates use the private `admin` channel on Laravel Echo.

## 9. UI / UX Design Direction

**Style:** Clean, modern, and professional, with a big hero headline, a top navigation bar, and both light and dark themes. The layout follows the structure of the Alex Rivera portfolio template (as a reference only, not a copy). The clickable prototype shows the direction.

- **Colors:** white and near-black base colors with soft card surfaces and one indigo accent (as in the prototype) used for links, buttons, and highlights; no yellow. Define them as Tailwind theme tokens so they can be changed in one place.
- **Typography:** a clean sans-serif for text (such as Inter) and a monospace font (such as JetBrains Mono) for code blocks only.
- **Details:** bordered cards with a soft hover lift, colored project thumbnails, skill progress bars, a testimonials section, a FAQ accordion on the contact page, and smooth page transitions.
- **Motion:** light and purposeful (fade, slide, hover); respects `prefers-reduced-motion`.
- **Layout:** sticky top navigation, clear spacing, max content width, mobile-first responsive design with a mobile menu.
- **Theme:** light and dark themes with a toggle that remembers the visitor's choice; the default follows the visitor's system setting.
- **Accessibility:** WCAG AA contrast, keyboard navigation, visible focus states, alt text on images, semantic HTML.

A short Figma or wireframe pass for Home, Projects, and Blog is recommended before building the UI.

## 10. Security

- HTTPS everywhere, HSTS, and secure cookies.
- Laravel's built-in CSRF, validation, and Eloquent protection against SQL injection.
- Blog Markdown is sanitized when rendered to prevent XSS.
- Admin: strong password, rate-limited login, optional two-factor authentication, and the admin URL not linked publicly.
- Contact form: honeypot field, rate limiting, and optional Cloudflare Turnstile or reCAPTCHA.
- File uploads: type and size validation, random filenames, stored outside executable paths.
- Secrets only in `.env` on the server; never committed to Git.
- Security headers (CSP, X-Frame-Options, Referrer-Policy) added through middleware.
- Regular dependency updates (Composer and npm audits, Dependabot).
- Privacy: short privacy note covering contact form data and chatbot logs.

**Role and access controls**

- Laravel policies and gates enforce permissions on the server; hiding menu items in Vue is only a convenience.
- Only the superadmin can create or disable admin accounts or change roles; the last superadmin cannot be deleted or demoted.
- Two-factor authentication is required for the superadmin and recommended for admins.
- An activity log records who created, edited, or deleted content and who changed roles.

**Login and registration security**

- Registration is invite-only: single-use, expiring invitations created by the superadmin; the register route rejects anything without a valid invite.
- Login rate limiting and lockout, generic error messages, and session regeneration after sign-in.
- Passwords hashed with bcrypt or Argon2; password reset through signed, expiring links.
- Session timeout for idle admins, and a logout button on every admin screen.
- Notification content is escaped before display to prevent XSS from visitor messages.

## 11. Performance and SEO

- Image optimization (WebP, responsive sizes, lazy loading).
- Code splitting per page with Vite; only the chatbot widget code loads lazily.
- Laravel caching for config, routes, views, and for rarely changing queries.
- CDN and caching headers for static assets (Cloudflare free tier is enough).
- Target Lighthouse score of 90+ on Performance, Accessibility, Best Practices, and SEO.
- SEO: unique title and meta description per page, Open Graph and Twitter cards, canonical URLs, JSON-LD (Person, BlogPosting), sitemap, and robots.txt.
- Optional privacy-friendly analytics (Plausible, Umami, or Cloudflare Web Analytics).

## 12. Hosting and Deployment (Recommendation)

**Recommended: a small VPS with a managed deployment workflow.**

- **Server:** a low-cost VPS (for example Hetzner or DigitalOcean) running Ubuntu, Nginx, PHP-FPM, and MySQL.
- **Why a VPS:** full control for Laravel (queues, scheduler, SSR with Node, caching), and it gives real DevOps experience that is also a good talking point in interviews.
- **Easier alternative:** Laravel Forge or a similar tool to provision and deploy the VPS with less manual setup.
- **Not recommended:** basic shared hosting. It often makes Inertia SSR, queues, and Node builds difficult.
- **Domain and DNS:** a custom domain (for example `tzm.dev` or `yourname.com`) managed through Cloudflare for DNS, SSL, and CDN.
- **Environments:** local development, optional staging, and production.
- **CI/CD:** GitHub Actions runs tests and builds assets, then deploys on push to `main` (SSH deploy or Forge webhook).
- **Backups:** daily database backup (for example with `spatie/laravel-backup`) to off-server storage, plus weekly server snapshots.
- **Monitoring:** uptime check (UptimeRobot or similar), Laravel logs, and error tracking (Sentry free tier or Flare).

Final hosting can be changed later without changing the design, because the app only needs PHP, MySQL, and (optionally) Node for SSR.

## 13. Testing Strategy

- **Backend:** Pest or PHPUnit feature tests for contact form, admin CRUD, blog and project queries, and chatbot endpoint (with the LLM provider mocked).
- **Frontend:** component tests with Vitest for filters, forms, and chat widget.
- **End-to-end:** a few Playwright tests for the main flows (browse projects, read a post, send a message, open the chatbot).
- **Quality:** Laravel Pint and ESLint/Prettier, GitHub Actions on every pull request.
- **Manual:** cross-browser and mobile checks, Lighthouse and accessibility audits before launch.

## 14. Project Plan and Milestones

| Phase | Scope | Result |
| --- | --- | --- |
| 0. Content and design | Collect real content, choose accent color and fonts, a clickable prototype and wireframes for key pages | Content checklist and design tokens |
| 1. Foundation | Laravel + Inertia + Vue + Tailwind setup, layout, navigation, light and dark themes, CI | Running skeleton, deployed to staging |
| 2. Data and admin | Migrations, models, admin login with login, invite-only registration, password reset, admin and superadmin roles, policies, CRUD for profile, experience, skills, projects, services | TZM can manage content |
| 3. Public pages | Home, About, Experience, Skills, Services, Projects with filters and case studies | Core portfolio complete |
| 4. Blog | Posts, tags, Markdown editor, code highlighting, RSS, search | Blog live |
| 5. Contact | Form, email and in-app admin notifications (bell, unread count, live push), spam protection, resume download | Contact flow works |
| 6. AI chatbot | Widget, backend service, knowledge base, rate limits, logging | "Ask TZM" live |
| 7. Polish and launch | SEO, performance, accessibility, security review, backups, domain and SSL | Public launch |

A realistic first goal is to launch phases 0 to 5 first, then add the chatbot, so the site can already be used in job applications.

## 15. Risks and Mitigations

| Risk | Impact | Mitigation |
| --- | --- | --- |
| Not enough real content (projects, experience) | Site looks empty or fake | Content checklist in phase 0; placeholders clearly marked; build fewer, stronger case studies first |
| Scope too large | Never launches | Phased plan; launch without the chatbot if needed |
| AI cost or abuse | Unexpected bill, spam | Rate limits, token caps, provider spending limit, fallback message |
| Chatbot gives wrong answers | Hurts credibility | Answer only from approved context, "I don't know" fallback, review logs regularly |
| Poor SEO with SPA behavior | Low visibility | Inertia SSR and server-rendered meta tags |
| Security issues in admin | Defacement or data leak | Role-based permissions, strong auth, two-factor for superadmin, validation, updates, backups |
| Design looks generic | Weak first impression | Wireframes first, one distinctive accent, strong typography, real screenshots |

## 16. Content Checklist (To Be Provided by TZM)

- Professional photo or avatar, short bio, and one-line pitch.
- Resume PDF.
- Work, internship, and education history with dates (only real entries).
- 10+ projects, each with: title, goal, tech used, your role, screenshots, GitHub link, live link if any.
- 2 to 3 first blog post topics (for example: Laravel + Inertia setup, Vue composables, REST API design notes).
- Contact email, GitHub, and LinkedIn URLs, plus any real testimonials (with permission) and FAQ answers.
- Domain name preference.

## 17. Open Questions

1. Do you approve the indigo accent used in the prototype, or do you prefer another color (not yellow)?
2. Which LLM provider and monthly budget do you want for the chatbot?
3. Do you want Inertia SSR enabled from the start (better SEO, slightly more server setup)?
4. Is the site English only, or also another language?
5. Besides the in-app bell and email, should new-message alerts also go to Telegram or Slack?

## 18. Summary

The TZM portfolio is a Laravel + Vue + Inertia + Tailwind application with a clean, modern design in light and dark themes, eight main pages (including a blog), a custom admin panel with admin and superadmin roles, invite-only login and registration, real-time notifications for new messages, and a secure, backend-only AI chatbot. It is hosted on a small VPS with automated deployment. The phased plan lets TZM launch the core portfolio early and add the chatbot afterward.
