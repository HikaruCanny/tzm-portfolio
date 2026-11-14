<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TechnologyController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('manage-portfolio');

        return Inertia::render('Admin/Technologies/Index', [
            'technologies' => Technology::query()->withCount('projects')->orderBy('name')->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-portfolio');

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:technologies,name',
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $count = 1;

        while (Technology::query()->where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        Technology::query()->create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return back()->with('success', 'Technology added.');
    }

    public function destroy(Technology $technology): RedirectResponse
    {
        Gate::authorize('manage-portfolio');

        $technology->delete();

        return back()->with('success', 'Technology removed.');
    }
}
