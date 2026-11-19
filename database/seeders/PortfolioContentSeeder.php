<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\Tag;
use App\Models\Technology;
use App\Models\User;
use App\Support\PortfolioPersonalization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PortfolioContentSeeder extends Seeder
{
    public function run(): void
    {
        $author = $this->seedSuperadmin();
        $this->seedProfile();
        $techModels = $this->seedTechnologies();
        $this->seedSkills();
        $this->seedProjects($techModels);
        $this->seedServices();
        $this->seedExperiences();
        $this->seedBlogPosts($author);

        $personalization = new PortfolioPersonalization;
        $personalization->syncProfileFromConfig();
        $personalization->installDefaultAssets(force: false);
        $personalization->unpublishSampleTestimonials();
    }

    private function seedSuperadmin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@tzm.dev'],
            [
                'name' => config('portfolio.name'),
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedProfile(): void
    {
        Profile::updateOrCreate(
            ['id' => 1],
            [
                'name' => config('portfolio.name'),
                'title' => config('portfolio.title'),
                'tagline' => 'Laravel APIs and Vue interfaces that stay maintainable after launch.',
                'bio' => 'I build full-stack web products with Laravel, Inertia, and Vue 3 — admin panels, REST APIs, and recruiter-friendly portfolio experiences. I care about clear domain modeling, testable PHP, and UIs that feel fast without unnecessary SPA complexity.',
                'goals' => 'Join a product-minded engineering team (remote-friendly) where I can own features end-to-end: schema design, API contracts, Inertia screens, and pragmatic tests. I want to deepen system design and shipping discipline on a live product.',
                'working_style' => 'Async-first collaboration, short written specs, small PRs, and demos over long status meetings. I prefer pairing on tricky auth or data-model decisions and documenting trade-offs in the repo.',
                'email' => config('portfolio.contact_email'),
                'phone' => config('portfolio.phone'),
                'github_url' => config('portfolio.github_url'),
                'linkedin_url' => config('portfolio.linkedin_url'),
                'location' => config('portfolio.location'),
            ]
        );
    }

    /**
     * @return array<string, Technology>
     */
    private function seedTechnologies(): array
    {
        $techs = [
            'Laravel', 'Vue.js', 'Inertia.js', 'Tailwind CSS', 'PHP', 'TypeScript',
            'MySQL', 'Redis', 'Docker', 'Pest', 'Vite',
        ];

        $models = [];
        foreach ($techs as $tech) {
            $models[$tech] = Technology::firstOrCreate(
                ['name' => $tech],
                ['slug' => Str::slug($tech)]
            );
        }

        return $models;
    }

    private function seedSkills(): void
    {
        $categoriesData = [
            'Backend' => [
                ['name' => 'PHP 8.4', 'level' => 95],
                ['name' => 'Laravel', 'level' => 95],
                ['name' => 'REST API design', 'level' => 90],
                ['name' => 'Queues & events', 'level' => 85],
            ],
            'Frontend' => [
                ['name' => 'Vue 3 (Composition API)', 'level' => 90],
                ['name' => 'Inertia.js', 'level' => 90],
                ['name' => 'Tailwind CSS', 'level' => 95],
                ['name' => 'TypeScript', 'level' => 85],
            ],
            'Data' => [
                ['name' => 'MySQL', 'level' => 90],
                ['name' => 'Eloquent & migrations', 'level' => 95],
                ['name' => 'Redis caching', 'level' => 80],
            ],
            'Quality & delivery' => [
                ['name' => 'Pest / PHPUnit', 'level' => 90],
                ['name' => 'GitHub Actions CI', 'level' => 85],
                ['name' => 'Docker for local dev', 'level' => 75],
            ],
        ];

        $sortCat = 1;
        foreach ($categoriesData as $catName => $skills) {
            $cat = SkillCategory::firstOrCreate(
                ['name' => $catName],
                ['sort_order' => $sortCat++]
            );

            $sortSkill = 1;
            foreach ($skills as $skill) {
                Skill::firstOrCreate(
                    [
                        'skill_category_id' => $cat->id,
                        'name' => $skill['name'],
                    ],
                    [
                        'level' => $skill['level'],
                        'sort_order' => $sortSkill++,
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, Technology>  $techModels
     */
    private function seedProjects(array $techModels): void
    {
        $projects = [
            [
                'slug' => 'tzm-portfolio-cms',
                'title' => 'TZM Portfolio & Admin CMS',
                'summary' => 'This site — Inertia + Vue public pages with a full admin CMS, blog, contact inbox, and notifications.',
                'problem' => 'Recruiters need a fast portfolio, but static sites cannot showcase real Laravel depth or let me update content without redeploying.',
                'solution' => 'Built a monolithic Laravel app with role-based admin, Markdown blog, contact pipeline, and a cohesive design system.',
                'role' => 'Solo full-stack developer',
                'status' => 'published',
                'demo_url' => config('app.url'),
                'repo_url' => null,
                'is_featured' => true,
                'tech' => ['Laravel', 'Vue.js', 'Inertia.js', 'Tailwind CSS', 'Pest'],
            ],
            [
                'slug' => 'inventory-ops-dashboard',
                'title' => 'Inventory Operations Dashboard',
                'summary' => 'Internal Laravel admin for stock levels, purchase orders, and audit-friendly activity logging.',
                'problem' => 'Spreadsheet workflows caused mismatches between warehouse counts and the sales portal.',
                'solution' => 'Centralized inventory with transactional adjustments, Redis-backed reporting queries, and exportable audit trails.',
                'role' => 'Backend lead · API + admin UI',
                'status' => 'published',
                'demo_url' => null,
                'repo_url' => null,
                'is_featured' => true,
                'tech' => ['Laravel', 'Vue.js', 'MySQL', 'Redis'],
            ],
            [
                'slug' => 'customer-support-api',
                'title' => 'Customer Support Ticketing API',
                'summary' => 'Versioned JSON API for ticket lifecycle, attachments metadata, and agent assignment rules.',
                'problem' => 'A mobile client team needed stable contracts and predictable pagination without coupling to a legacy monolith.',
                'solution' => 'Designed REST resources with Sanctum auth, policy gates, and OpenAPI examples generated from feature tests.',
                'role' => 'API engineer',
                'status' => 'published',
                'demo_url' => null,
                'repo_url' => null,
                'is_featured' => true,
                'tech' => ['Laravel', 'PHP', 'MySQL', 'Pest'],
            ],
            [
                'slug' => 'vue-form-kit',
                'title' => 'Vue Form Kit for Internal Tools',
                'summary' => 'Composable Vue inputs (date ranges, async selects, validation states) shared across admin screens.',
                'problem' => 'Repeated form patterns slowed new admin feature delivery.',
                'solution' => 'Extracted typed composables and accessible Tailwind primitives used by three internal dashboards.',
                'role' => 'Frontend engineer',
                'status' => 'published',
                'demo_url' => null,
                'repo_url' => null,
                'is_featured' => false,
                'tech' => ['Vue.js', 'TypeScript', 'Tailwind CSS', 'Vite'],
            ],
            [
                'slug' => 'university-attendance-portal',
                'title' => 'University Attendance Portal',
                'summary' => 'Capstone web app for lecturers to record attendance and students to view compliance reports.',
                'problem' => 'Paper attendance was error-prone and slow to aggregate at term end.',
                'solution' => 'Laravel auth with role separation, CSV exports, and Vue charts for weekly summaries.',
                'role' => 'Team lead (4 students)',
                'status' => 'published',
                'demo_url' => null,
                'repo_url' => null,
                'is_featured' => false,
                'tech' => ['Laravel', 'Vue.js', 'MySQL'],
            ],
            [
                'slug' => 'local-dev-docker-starter',
                'title' => 'Dockerized Local Dev Starter',
                'summary' => 'Compose stack for PHP, MySQL, Redis, and Mailpit matching production-like Laravel workflows.',
                'problem' => 'New contributors spent days aligning PHP extensions and mail drivers locally.',
                'solution' => 'Documented compose profiles, queued worker scripts, and seeded sample data command.',
                'role' => 'DevOps-minded developer',
                'status' => 'published',
                'demo_url' => null,
                'repo_url' => null,
                'is_featured' => false,
                'tech' => ['Docker', 'Laravel', 'MySQL', 'Redis'],
            ],
        ];

        foreach ($projects as $data) {
            $tech = $data['tech'];
            unset($data['tech']);

            $project = Project::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    ...$data,
                    'published_at' => now()->subDays(random_int(30, 400)),
                ]
            );

            $ids = collect($tech)
                ->map(fn (string $name) => $techModels[$name]->id ?? null)
                ->filter()
                ->values()
                ->all();

            $project->technologies()->sync($ids);
        }
    }

    private function seedServices(): void
    {
        $services = [
            [
                'title' => 'Laravel application development',
                'description' => 'Greenfield APIs, admin panels, and refactors with migrations, policies, queues, and Pest coverage.',
                'icon' => 'Database',
                'sort_order' => 1,
            ],
            [
                'title' => 'Inertia + Vue product UI',
                'description' => 'Accessible, responsive screens with shared layouts, forms, and optimistic UX patterns.',
                'icon' => 'LayoutDashboard',
                'sort_order' => 2,
            ],
            [
                'title' => 'Technical documentation & handoff',
                'description' => 'README runbooks, ADR-style notes, and admin guides so your team can maintain the system.',
                'icon' => 'Server',
                'sort_order' => 3,
            ],
        ];

        foreach ($services as $svc) {
            Service::updateOrCreate(['title' => $svc['title']], $svc);
        }
    }

    private function seedExperiences(): void
    {
        $experiences = [
            [
                'type' => 'work',
                'organization' => 'Freelance & contract software projects',
                'role' => 'Full-Stack Developer (Laravel + Vue)',
                'start_date' => '2023-01-01',
                'end_date' => null,
                'description' => 'Delivered admin dashboards, REST APIs, and Vue frontends for SMEs and startup teams. Emphasis on maintainable schema design and CI-backed releases.',
                'sort_order' => 1,
            ],
            [
                'type' => 'work',
                'organization' => 'University software lab (part-time)',
                'role' => 'Web Developer',
                'start_date' => '2022-06-01',
                'end_date' => '2022-12-31',
                'description' => 'Built internal tools for faculty scheduling and student feedback collection using Laravel and Blade, later migrating key screens to Vue.',
                'sort_order' => 2,
            ],
            [
                'type' => 'education',
                'organization' => 'B.Sc. Computer Science',
                'role' => 'Undergraduate',
                'start_date' => '2019-09-01',
                'end_date' => '2023-07-31',
                'description' => 'Coursework in algorithms, databases, software engineering, and capstone team project (attendance portal).',
                'sort_order' => 3,
            ],
        ];

        foreach ($experiences as $exp) {
            Experience::updateOrCreate(
                ['organization' => $exp['organization'], 'role' => $exp['role']],
                $exp
            );
        }
    }

    private function seedBlogPosts(User $author): void
    {
        $tagModels = collect(['Laravel', 'Vue', 'Architecture'])->mapWithKeys(function (string $name) {
            $slug = Str::slug($name);

            return [$name => Tag::firstOrCreate(['slug' => $slug], ['name' => $name])];
        });

        $posts = [
            [
                'slug' => 'laravel-inertia-portfolio-lessons',
                'title' => 'Lessons from building this portfolio on Laravel and Inertia',
                'excerpt' => 'Starter post — edit in admin. Why a monolith with Inertia beat a split SPA for this portfolio.',
                'meta_description' => 'Notes on routing, admin CRUD, and shared props when building a Laravel Inertia portfolio.',
                'is_featured' => true,
                'tag' => 'Laravel',
                'body_markdown' => <<<'MD'
## Why Inertia here

A portfolio needs SEO-friendly pages, fast navigation, and an admin CMS. Inertia lets me keep Laravel routing and policies while still writing Vue for interactive screens.

## Structure that paid off

- Single `Profile` row for hero content
- Feature tests per public page
- Admin gates for site settings vs portfolio CRUD

## Takeaway

Reach for Inertia when your “SPA” is really a set of document-like pages with a few rich forms—not when you need offline-first mobile clients.
MD,
            ],
            [
                'slug' => 'vue-composables-for-admin-forms',
                'title' => 'Vue composables I reuse in admin forms',
                'excerpt' => 'Starter post — edit in admin. Patterns for validation, uploads, and optimistic saves.',
                'meta_description' => 'Practical Vue 3 composable patterns for Inertia admin interfaces.',
                'is_featured' => false,
                'tag' => 'Vue',
                'body_markdown' => <<<'MD'
## Form ergonomics

Admin users notice lag and unclear errors more than fancy animations.

## Patterns

1. Wrap Inertia `useForm` with consistent error summary components
2. Centralize image preview state for uploads
3. Debounce Markdown preview calls to the server

## Closing thought

Composable extraction works best after the second copy-paste—not before the first screen ships.
MD,
            ],
            [
                'slug' => 'rest-apis-without-overfetching',
                'title' => 'Designing REST APIs without over-fetching',
                'excerpt' => 'Starter post — edit in admin. Pagination, includes, and policy checks for APIs.',
                'meta_description' => 'REST API design notes for Laravel: pagination, sparse fieldsets, and authorization.',
                'is_featured' => false,
                'tag' => 'Architecture',
                'body_markdown' => <<<'MD'
## Start from use cases

List the screens your API powers, then design resources—not the other way around.

## Laravel specifics

- Use API resources for stable JSON shapes
- Enforce `authorize()` in form requests
- Prefer cursor pagination for infinite feeds

## Testing

Feature tests that assert JSON schema catch more regressions than mocking controllers in isolation.
MD,
            ],
        ];

        foreach ($posts as $data) {
            $tag = $data['tag'];
            unset($data['tag']);

            $post = Post::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    ...$data,
                    'user_id' => $author->id,
                    'status' => 'published',
                    'published_at' => now()->subDays(random_int(7, 60)),
                    'views_count' => random_int(12, 240),
                ]
            );

            $post->tags()->sync([$tagModels[$tag]->id]);
        }
    }
}
