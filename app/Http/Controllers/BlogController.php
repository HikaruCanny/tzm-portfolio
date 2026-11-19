<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Profile;
use App\Models\Tag;
use App\Models\User;
use App\Services\RendersBlogMarkdown;
use App\Support\PortfolioCover;
use App\Support\Seo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Response;

class BlogController extends Controller
{
    public function __construct(private RendersBlogMarkdown $markdown) {}

    public function index(Request $request): Response
    {
        return $this->listing($request);
    }

    public function tag(Request $request, Tag $tag): Response
    {
        return $this->listing($request, $tag);
    }

    public function show(Request $request, Post $post): Response
    {
        abort_unless($post->status === 'published' && $post->published_at !== null, 404);

        $this->recordView($request, $post);

        $post->load(['tags:id,name,slug', 'author:id,name']);
        $rendered = $this->markdown->render($post->body_markdown);
        $tagIds = $post->tags->pluck('id');

        $related = Post::published()
            ->whereKeyNot($post->id)
            ->when($tagIds->isNotEmpty(), function (Builder $query) use ($tagIds): void {
                $query->whereHas('tags', function (Builder $query) use ($tagIds): void {
                    $query->whereIn('tags.id', $tagIds);
                });
            })
            ->latest('published_at')
            ->take(3)
            ->get(['id', 'title', 'slug', 'excerpt']);

        if ($related->isEmpty()) {
            $related = Post::published()
                ->whereKeyNot($post->id)
                ->latest('published_at')
                ->take(3)
                ->get(['id', 'title', 'slug', 'excerpt']);
        }

        $description = $post->meta_description ?: $post->excerpt;
        $ogImage = PortfolioCover::absoluteUrl($post->cover_image, PortfolioCover::BLOG);

        $seo = Seo::forPost($post, $description, $ogImage);

        return $this->inertiaWithSeo('BlogPost', [
            'post' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'meta_description' => $description,
                'cover_image' => $post->cover_image,
                'cover_url' => $post->cover_url,
                'og_image' => $ogImage,
                'published_at' => $post->published_at,
                'reading_time' => $post->reading_time,
                'views_count' => $post->views_count,
                'html' => $rendered['html'],
                'toc' => $rendered['toc'],
                'tags' => $post->tags,
                'author' => $this->authorPayload($post->author),
            ],
            'relatedPosts' => $related,
        ], $seo);
    }

    private function listing(Request $request, ?Tag $tag = null): Response
    {
        $search = $request->string('search')->trim()->toString();
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['newest', 'popular'], true) ? $sort : 'newest';

        $featured = Post::published()
            ->with(['tags:id,name,slug', 'author:id,name'])
            ->featured()
            ->when($tag, function (Builder $query) use ($tag): void {
                $query->whereHas('tags', function (Builder $query) use ($tag): void {
                    $query->whereKey($tag->id);
                });
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $this->applySearch($query, $search);
            })
            ->latest('published_at')
            ->first();

        $description = $tag
            ? "Articles tagged {$tag->name} on Laravel, Vue, and web architecture."
            : 'Technical writing on Laravel, Inertia, Vue, and API design.';

        $title = $tag ? "{$tag->name} – Technical Blog" : 'Technical Blog';

        $path = $tag ? "/blog/tag/{$tag->slug}" : '/blog';

        return $this->inertiaWithSeo('Blog', [
            'posts' => $this->posts($request, $tag, $featured?->id),
            'featuredPost' => $featured ? $this->card($featured) : null,
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'tag' => $tag?->only(['name', 'slug']),
            ],
        ], Seo::forStatic(
            title: $title,
            description: $description,
            path: $path,
        ));
    }

    /**
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    private function posts(Request $request, ?Tag $tag, ?int $excludeId): LengthAwarePaginator
    {
        $search = $request->string('search')->trim()->toString();
        $sort = $request->string('sort')->toString() === 'popular' ? 'popular' : 'newest';

        return Post::published()
            ->with(['tags:id,name,slug', 'author:id,name'])
            ->when($excludeId, function (Builder $query) use ($excludeId): void {
                $query->whereKeyNot($excludeId);
            })
            ->when($tag, function (Builder $query) use ($tag): void {
                $query->whereHas('tags', function (Builder $query) use ($tag): void {
                    $query->whereKey($tag->id);
                });
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $this->applySearch($query, $search);
            })
            ->when($sort === 'popular', function (Builder $query): void {
                $query->orderByDesc('views_count')->latest('published_at');
            }, function (Builder $query): void {
                $query->latest('published_at');
            })
            ->paginate(6)
            ->withQueryString()
            ->through(fn (Post $post): array => $this->card($post));
    }

    /**
     * @param  Builder<Post>  $query
     */
    private function applySearch(Builder $query, string $search): void
    {
        $like = '%'.addcslashes($search, '%_\\').'%';
        $query->where(function (Builder $query) use ($like): void {
            $query->where('title', 'like', $like)->orWhere('excerpt', 'like', $like);
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function card(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'cover_image' => $post->cover_image,
            'cover_url' => $post->cover_url,
            'published_at' => $post->published_at,
            'reading_time' => $post->reading_time,
            'views_count' => $post->views_count,
            'is_featured' => $post->is_featured,
            'tags' => $post->tags,
            'author' => $this->authorPayload($post->author),
        ];
    }

    /**
     * @return array{name: string, title: string|null, photo_path: string|null}
     */
    private function authorPayload(?User $user): array
    {
        $profile = Profile::instance();

        return [
            'name' => $user?->name ?: $profile->name,
            'title' => $profile->title,
            'photo_path' => $profile->photo_path,
        ];
    }

    private function recordView(Request $request, Post $post): void
    {
        /** @var list<int> $viewed */
        $viewed = array_map(
            static fn (mixed $id): int => (int) $id,
            $request->session()->get('viewed_posts', []),
        );

        if (in_array($post->id, $viewed, true)) {
            return;
        }

        Post::query()->whereKey($post->id)->increment('views_count');
        $post->views_count = (int) $post->views_count + 1;

        $viewed[] = $post->id;
        $request->session()->put('viewed_posts', $viewed);
    }
}
