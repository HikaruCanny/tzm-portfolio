<?php

namespace App\Support;

use App\Models\Post;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Support\Str;

final class Seo
{
    /**
     * @return array<string, mixed>
     */
    public static function forHome(Profile $profile): array
    {
        $title = "{$profile->name} – {$profile->title}";
        $description = Str::limit($profile->tagline ?: $profile->bio ?: config('seo.default_description'), 160, '');

        return self::build(
            title: $title,
            description: $description,
            path: '/',
            jsonLd: StructuredData::person($profile),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function forStatic(string $title, string $description, string $path, ?array $jsonLd = null): array
    {
        $fullTitle = str_contains($title, '–')
            ? $title
            : "{$title} – ".config('seo.site_name');

        return self::build(
            title: $fullTitle,
            description: Str::limit($description, 160, ''),
            path: $path,
            jsonLd: $jsonLd,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function forProject(Project $project): array
    {
        $description = Str::limit($project->summary ?: $project->problem ?: config('seo.default_description'), 160, '');
        $title = "{$project->title} – Case Study";

        return self::build(
            title: "{$title} – ".config('seo.site_name'),
            description: $description,
            path: route('projects.show', $project->slug, absolute: false),
            ogImage: PortfolioCover::absoluteUrl($project->cover_image, PortfolioCover::PROJECT),
            ogType: 'article',
            jsonLd: StructuredData::creativeWork($project),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function forPost(Post $post, string $description, string $ogImage): array
    {
        return self::build(
            title: "{$post->title} – ".config('seo.site_name'),
            description: Str::limit($description, 160, ''),
            path: route('blog.post', $post->slug, absolute: false),
            ogImage: $ogImage,
            ogType: 'article',
            jsonLd: StructuredData::blogPosting($post, $description, $ogImage),
        );
    }

    /**
     * @return array{seo: array<string, mixed>, json_ld: array<string, mixed>|list<array<string, mixed>>|null}
     */
    public static function viewData(
        string $title,
        string $description,
        string $path,
        ?string $ogImage = null,
        string $ogType = 'website',
        ?array $jsonLd = null,
    ): array {
        $seo = self::build($title, $description, $path, $ogImage, $ogType, $jsonLd);

        return [
            'seo' => $seo,
            'json_ld' => $jsonLd,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function build(
        string $title,
        string $description,
        string $path,
        ?string $ogImage = null,
        string $ogType = 'website',
        ?array $jsonLd = null,
    ): array {
        $canonical = url($path);
        $image = $ogImage ?: self::defaultOgImage();

        return [
            'title' => $title,
            'description' => $description,
            'og_title' => Str::before($title, ' – '.config('seo.site_name')) ?: $title,
            'og_description' => $description,
            'og_image' => $image,
            'og_url' => $canonical,
            'og_type' => $ogType,
            'og_site_name' => config('seo.site_name'),
            'twitter_card' => 'summary_large_image',
            'twitter_site' => config('seo.twitter_site'),
            'canonical' => $canonical,
            'json_ld' => $jsonLd,
        ];
    }

    public static function defaultOgImage(): string
    {
        $configured = config('seo.default_og_image');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return PortfolioCover::absoluteUrl(null, PortfolioCover::BLOG);
    }
}
