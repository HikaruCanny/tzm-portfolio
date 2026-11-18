<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\SkillCategory;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortfolioController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Welcome', $this->landingProps());
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'profile' => Profile::instance(),
        ]);
    }

    public function experience(): Response
    {
        return Inertia::render('Experience', [
            'experiences' => Experience::query()->orderBy('sort_order')->orderByDesc('start_date')->get(),
        ]);
    }

    public function skills(): Response
    {
        return Inertia::render('Skills', [
            'categories' => SkillCategory::query()->with('skills')->orderBy('sort_order')->get(),
        ]);
    }

    public function services(): Response
    {
        return Inertia::render('Services', [
            'services' => Service::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function resume(): StreamedResponse
    {
        $profile = Profile::instance();

        if ($profile->resume_path === null || ! Storage::disk('public')->exists($profile->resume_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($profile->resume_path, 'TZM-Resume.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function landingProps(): array
    {
        return [
            'profile' => Profile::instance(),
            'experiences' => Experience::query()->orderBy('sort_order')->orderByDesc('start_date')->get(),
            'categories' => SkillCategory::query()->with('skills')->orderBy('sort_order')->get(),
            'services' => Service::query()->orderBy('sort_order')->get(),
            'featuredProjects' => $this->featuredProjects(),
            'latestPosts' => Post::published()->with('tags:id,name,slug')->latest('published_at')->take(3)->get(['id', 'title', 'slug', 'excerpt', 'published_at', 'reading_time']),
            'testimonials' => Testimonial::query()->published()->orderBy('sort_order')->orderByDesc('id')->get(),
        ];
    }

    /**
     * @return Collection<int, Project>
     */
    private function featuredProjects(): Collection
    {
        return Project::published()
            ->featured()
            ->with('technologies')
            ->latest('published_at')
            ->take(3)
            ->get();
    }
}
