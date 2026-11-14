<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('manage-portfolio');

        return Inertia::render('Admin/Tags/Index', [
            'tags' => Tag::query()->withCount('posts')->orderBy('name')->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-portfolio');

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:tags,name',
        ]);

        Tag::query()->create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
        ]);

        return back()->with('success', 'Tag added.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        Gate::authorize('manage-portfolio');

        $tag->delete();

        return back()->with('success', 'Tag removed.');
    }

    private function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $slug = $slug === '' ? 'tag' : $slug;
        $original = $slug;
        $count = 2;

        while (Tag::query()->where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}
