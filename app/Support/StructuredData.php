<?php

namespace App\Support;

use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;

final class StructuredData
{
    /**
     * @return array<string, mixed>
     */
    public static function person(Profile $profile): array
    {
        $sameAs = array_values(array_filter([
            $profile->github_url,
            $profile->linkedin_url,
        ]));

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $profile->name,
            'jobTitle' => $profile->title,
            'url' => url('/'),
            'email' => $profile->email,
            'telephone' => $profile->phone,
            'sameAs' => $sameAs,
            'description' => $profile->tagline ?: $profile->bio,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function blogPosting(Post $post, string $description, string $image): array
    {
        $post->loadMissing('author:id,name');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $description,
            'image' => $image,
            'datePublished' => $post->published_at?->toAtomString(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author?->name ?? config('seo.site_name'),
            ],
            'mainEntityOfPage' => route('blog.post', $post->slug),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function creativeWork(Project $project): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'description' => $project->summary,
            'url' => route('projects.show', $project->slug),
        ];
    }
}
