<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use App\Services\RendersBlogMarkdown;
use App\Services\StoresPortfolioImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function __construct(
        private StoresPortfolioImage $images,
        private RendersBlogMarkdown $markdown,
    ) {}

    public function index(): Response
    {
        Gate::authorize('manage-portfolio');

        $posts = Post::query()
            ->with('tags:id,name,slug')
            ->latest()
            ->paginate(10)
            ->through(fn (Post $post): array => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'status' => $post->status,
                'published_at' => $post->published_at,
                'views_count' => $post->views_count,
                'tags' => $post->tags,
            ]);

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('manage-portfolio');

        return Inertia::render('Admin/Posts/Form', [
            'post' => null,
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name']),
            'previewHtml' => '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-portfolio');

        $validated = $this->validated($request);

        $post = Post::query()->create([
            'title' => $validated['title'],
            'slug' => $this->uniqueSlug($validated['title']),
            'excerpt' => $validated['excerpt'],
            'body_markdown' => $validated['body'],
            'status' => $validated['status'],
            'cover_image' => $request->file('cover_image')
                ? $this->images->store($request->file('cover_image'), 'posts/covers')
                : null,
        ]);

        $post->tags()->sync($validated['tag_ids'] ?? []);

        return redirect()->route('admin.posts.index')->with('success', 'Post saved.');
    }

    public function edit(Post $post): Response
    {
        Gate::authorize('manage-portfolio');

        $post->load('tags:id,name');

        return Inertia::render('Admin/Posts/Form', [
            'post' => $post,
            'tags' => Tag::query()->orderBy('name')->get(['id', 'name']),
            'previewHtml' => $this->markdown->render($post->body_markdown)['html'],
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('manage-portfolio');

        $validated = $this->validated($request);
        $cover = $post->cover_image;

        if ($request->boolean('remove_cover') || $request->hasFile('cover_image')) {
            $this->images->delete($cover);
            $cover = null;
        }

        if ($request->hasFile('cover_image')) {
            $cover = $this->images->store($request->file('cover_image'), 'posts/covers');
        }

        $post->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'body_markdown' => $validated['body'],
            'status' => $validated['status'],
            'cover_image' => $cover,
        ]);

        $post->tags()->sync($validated['tag_ids'] ?? []);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('manage-portfolio');

        $this->images->delete($post->cover_image);
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    public function preview(Request $request): JsonResponse
    {
        Gate::authorize('manage-portfolio');

        $validated = $request->validate([
            'body' => 'nullable|string|max:50000',
        ]);

        return response()->json([
            'html' => $this->markdown->render($validated['body'] ?? '')['html'],
        ]);
    }

    /**
     * @return array{title: string, excerpt: string, body: string, status: string, tag_ids?: array<int, int>}
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:180',
            'excerpt' => 'required|string|max:500',
            'body' => 'required|string|max:50000',
            'status' => ['required', Rule::in(['draft', 'published'])],
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_cover' => 'nullable|boolean',
        ]);
    }

    private function uniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $slug = $slug === '' ? 'post' : $slug;
        $original = $slug;
        $count = 2;

        while (Post::query()->where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
