<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Service;
use App\Models\SkillCategory;
use App\Models\Testimonial;
use App\Support\Seo;
use App\Support\StructuredData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortfolioController extends Controller
{
    public function home(): Response
    {
        $profile = Profile::instance();

        return $this->inertiaWithSeo('Welcome', $this->landingProps(), Seo::forHome($profile));
    }

    public function about(): Response
    {
        $profile = Profile::instance();

        return $this->inertiaWithSeo('About', [
            'profile' => $profile->toPublicArray(),
        ], Seo::forStatic(
            title: "About – {$profile->name}",
            description: (string) ($profile->bio ?: config('seo.default_description')),
            path: '/about',
            jsonLd: StructuredData::person($profile),
        ));
    }

    public function experience(): Response
    {
        return $this->inertiaWithSeo('Experience', [
            'experiences' => Experience::query()->orderBy('sort_order')->orderByDesc('start_date')->get(),
        ], Seo::forStatic(
            title: 'Experience & Education',
            description: 'Professional work history, internships, and computer science education.',
            path: '/experience',
        ));
    }

    public function skills(): Response
    {
        return $this->inertiaWithSeo('Skills', [
            'categories' => SkillCategory::query()->with('skills')->orderBy('sort_order')->get(),
        ], Seo::forStatic(
            title: 'Skills & Technologies',
            description: 'Laravel, Vue, Inertia, API design, databases, testing, and delivery tooling.',
            path: '/skills',
        ));
    }

    public function services(): Response
    {
        return $this->inertiaWithSeo('Services', [
            'services' => Service::query()->orderBy('sort_order')->get(),
        ], Seo::forStatic(
            title: 'What I Can Build',
            description: 'Laravel applications, Inertia + Vue UIs, and maintainable handoff documentation.',
            path: '/services',
        ));
    }

    public function resume(): StreamedResponse
    {
        $profile = Profile::instance();

        if (! $profile->hasResume()) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $profile->resume_path,
            $profile->resumeDownloadFileName(),
            ['Content-Type' => 'application/pdf'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function landingProps(): array
    {
        return [
            'profile' => Profile::instance()->toPublicArray(),
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
