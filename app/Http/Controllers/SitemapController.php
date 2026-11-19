<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        URL::forceRootUrl(config('app.url'));

        $static = [
            ['loc' => route('home'), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.9'],
            ['loc' => route('experience'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('skills'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('services'), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('projects'), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => route('blog'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('contact'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('feed'), 'changefreq' => 'daily', 'priority' => '0.5'],
        ];

        $projects = Project::published()
            ->orderByDesc('updated_at')
            ->get(['slug', 'updated_at'])
            ->map(fn (Project $project) => [
                'loc' => route('projects.show', $project->slug),
                'lastmod' => $project->updated_at?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ]);

        $posts = Post::published()
            ->orderByDesc('published_at')
            ->get(['slug', 'updated_at', 'published_at'])
            ->map(fn (Post $post) => [
                'loc' => route('blog.post', $post->slug),
                'lastmod' => ($post->updated_at ?? $post->published_at)?->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ]);

        return response()
            ->view('sitemap', [
                'urls' => collect($static)->concat($projects)->concat($posts),
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
