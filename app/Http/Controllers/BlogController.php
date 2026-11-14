<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use App\Services\RendersBlogMarkdown;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function __construct(private RendersBlogMarkdown $markdown) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Blog', [
            'posts' => $this->posts($request),
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'filters' => [
                'search' => $request->string('search')->trim()->toString(),
                'tag' => null,
            ],
        ]);
    }

    public function tag(Request $request, Tag $tag): Response
    {
        return Inertia::render('Blog', [
            'posts' => $this->posts($request, $tag),
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'filters' => [
                'search' => $request->string('search')->trim()->toString(),
                'tag' => $tag->only(['name', 'slug']),
            ],
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->status === 'published' && $post->published_at !== null, 404);

        $post->load('tags:id,name,slug');
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

        return Inertia::render('BlogPost', [
            'post' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'cover_image' => $post->cover_image,
                'published_at' => $post->published_at,
                'reading_time' => $post->reading_time,
                'html' => $rendered['html'],
                'toc' => $rendered['toc'],
                'tags' => $post->tags,
            ],
            'relatedPosts' => $related,
        ]);
    }

    /**
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    private function posts(Request $request, ?Tag $tag = null): LengthAwarePaginator
    {
        $search = $request->string('search')->trim()->toString();

        return Post::published()
            ->with('tags:id,name,slug')
            ->when($tag, function (Builder $query) use ($tag): void {
                $query->whereHas('tags', function (Builder $query) use ($tag): void {
                    $query->whereKey($tag->id);
                });
            })
            ->when($search !== '', function (Builder $query) use ($search): void {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $query->where(function (Builder $query) use ($like): void {
                    $query->where('title', 'like', $like)->orWhere('excerpt', 'like', $like);
                });
            })
            ->latest('published_at')
            ->paginate(6)
            ->withQueryString()
            ->through(fn (Post $post): array => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'cover_image' => $post->cover_image,
                'published_at' => $post->published_at,
                'reading_time' => $post->reading_time,
                'tags' => $post->tags,
            ]);
    }
}
