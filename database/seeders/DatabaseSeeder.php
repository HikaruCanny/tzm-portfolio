<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Superadmin User
        $superadmin = User::firstOrCreate(
            ['email' => 'admin@tzm.dev'],
            [
                'name' => 'TZM Superadmin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Profile
        Profile::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'TZM',
                'title' => 'Full-Stack Developer (Laravel + Vue.js)',
                'tagline' => 'Architecting reliable backend APIs and reactive single-page interfaces.',
                'bio' => 'Full-stack software developer specialized in Laravel and Vue.js ecosystem. Proven track record building robust web applications, fine-grained RBAC systems, and high-performance REST APIs.',
                'email' => 'contact@tzm.dev',
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://linkedin.com',
                'location' => 'Available for Global Remote Roles',
            ]
        );

        // 3. Skill Categories & Skills
        $categoriesData = [
            'Backend' => [
                ['name' => 'PHP 8.4', 'level' => 95],
                ['name' => 'Laravel Framework', 'level' => 95],
                ['name' => 'RESTful API Architecture', 'level' => 90],
                ['name' => 'Node.js & Express', 'level' => 75],
            ],
            'Frontend' => [
                ['name' => 'Vue.js 3 (Composition API)', 'level' => 90],
                ['name' => 'Inertia.js v2', 'level' => 90],
                ['name' => 'Tailwind CSS', 'level' => 95],
                ['name' => 'TypeScript', 'level' => 85],
            ],
            'Database' => [
                ['name' => 'MySQL / MariaDB', 'level' => 90],
                ['name' => 'PostgreSQL', 'level' => 80],
                ['name' => 'Redis', 'level' => 80],
            ],
            'Tools & DevOps' => [
                ['name' => 'Git & GitHub', 'level' => 90],
                ['name' => 'Vite Bundler', 'level' => 85],
                ['name' => 'Docker', 'level' => 75],
                ['name' => 'Pest Testing', 'level' => 85],
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

        // 4. Technologies
        $techs = ['Laravel', 'Vue.js', 'Inertia.js', 'Tailwind CSS', 'PHP', 'TypeScript', 'MySQL', 'Redis', 'Docker'];
        $techModels = [];
        foreach ($techs as $tech) {
            $techModels[$tech] = Technology::firstOrCreate(
                ['name' => $tech],
                ['slug' => Str::slug($tech)]
            );
        }

        // 5. Projects
        $p1 = Project::updateOrCreate(
            ['slug' => 'tzm-portfolio-cms'],
            [
                'title' => 'TZM Portfolio & Custom CMS',
                'summary' => 'Single-page portfolio and administrative dashboard built with Inertia.js v2, Vue 3, and Laravel.',
                'problem' => 'Modern portfolios need to be blazing fast, easily manageable without code changes, and showcase true full-stack depth.',
                'solution' => 'Engineered a monolithic Inertia application with dedicated admin CRUD, role validation, and reactive public screens.',
                'role' => 'Lead Full-Stack Architect',
                'status' => 'published',
                'demo_url' => 'https://tzm.dev',
                'repo_url' => 'https://github.com',
                'is_featured' => true,
                'published_at' => now(),
            ]
        );
        $p1->technologies()->sync([$techModels['Laravel']->id, $techModels['Vue.js']->id, $techModels['Inertia.js']->id, $techModels['Tailwind CSS']->id]);

        $p2 = Project::updateOrCreate(
            ['slug' => 'real-time-order-logistics'],
            [
                'title' => 'Real-Time Order Logistics Hub',
                'summary' => 'Order routing and inventory management system with event streaming and real-time dashboard.',
                'problem' => 'High volume operations required automated order tracking with zero desynchronization between database and UI.',
                'solution' => 'Implemented transactional database pipelines with WebSockets broadcast and reactive Vue frontend.',
                'role' => 'Backend & API Engineer',
                'status' => 'published',
                'demo_url' => 'https://demo.example.com',
                'repo_url' => 'https://github.com',
                'is_featured' => true,
                'published_at' => now(),
            ]
        );
        $p2->technologies()->sync([$techModels['Laravel']->id, $techModels['MySQL']->id, $techModels['Redis']->id]);

        // 6. Services
        $services = [
            [
                'title' => 'Custom Admin Dashboards & CMS',
                'description' => 'Single-page administrative backends with Inertia.js & Vue 3, robust role-based access control, analytics tables, and responsive forms.',
                'icon' => 'LayoutDashboard',
                'sort_order' => 1,
            ],
            [
                'title' => 'RESTful API Engineering',
                'description' => 'Clean JSON API services conforming to REST conventions with rate limiting, Sanctum/OAuth authentication, and automated OpenAPI documentation.',
                'icon' => 'Server',
                'sort_order' => 2,
            ],
            [
                'title' => 'Relational Database Design',
                'description' => 'Well-structured schemas, optimized queries, foreign keys, constraints, and migration strategies across MySQL & PostgreSQL.',
                'icon' => 'Database',
                'sort_order' => 3,
            ],
        ];

        foreach ($services as $svc) {
            Service::updateOrCreate(['title' => $svc['title']], $svc);
        }

        // 7. Experiences
        $experiences = [
            [
                'type' => 'work',
                'organization' => 'Software Engineering & Web Systems',
                'role' => 'Full-Stack Developer',
                'start_date' => '2023-01-01',
                'end_date' => null,
                'description' => 'Engineering enterprise solutions with Laravel, Vue 3, and MySQL. Building reliable APIs and single-page apps.',
                'sort_order' => 1,
            ],
            [
                'type' => 'education',
                'organization' => 'University of Computer Studies',
                'role' => 'B.Sc. in Computer Science',
                'start_date' => '2019-12-01',
                'end_date' => '2023-11-30',
                'description' => 'Core foundations in computer systems, algorithms, distributed networks, and database design.',
                'sort_order' => 2,
            ],
        ];

        foreach ($experiences as $exp) {
            Experience::updateOrCreate(
                ['organization' => $exp['organization'], 'role' => $exp['role']],
                $exp
            );
        }
    }
}
