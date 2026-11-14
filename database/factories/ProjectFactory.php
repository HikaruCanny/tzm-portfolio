<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->paragraph(1),
            'problem' => fake()->paragraph(2),
            'solution' => fake()->paragraph(2),
            'role' => 'Full-Stack Developer',
            'status' => 'published',
            'demo_url' => fake()->optional()->url(),
            'repo_url' => fake()->optional()->url(),
            'is_featured' => false,
            'published_at' => now(),
        ];
    }
}
