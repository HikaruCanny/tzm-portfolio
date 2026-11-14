<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Technology;
use App\Services\StoresPortfolioImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(private StoresPortfolioImage $images) {}

    public function index(): Response
    {
        $projects = Project::with('technologies')->latest()->paginate(10);

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create(): Response
    {
        $technologies = Technology::orderBy('name')->get();

        return Inertia::render('Admin/Projects/Form', [
            'technologies' => $technologies,
            'project' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'problem' => 'nullable|string',
            'solution' => 'nullable|string',
            'role' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'demo_url' => 'nullable|url',
            'repo_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'technology_ids' => 'array',
            'technology_ids.*' => 'exists:technologies,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $technologyIds = $validated['technology_ids'] ?? [];
        unset($validated['technology_ids'], $validated['cover_image'], $validated['images']);

        $validated['slug'] = Str::slug($validated['title']);
        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Project::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$originalSlug}-{$count}";
            $count++;
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $project = Project::create($validated);
        $project->technologies()->sync($technologyIds);
        $this->storeUploads($request, $project);

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project): Response
    {
        $project->load(['technologies', 'images']);
        $technologies = Technology::orderBy('name')->get();

        return Inertia::render('Admin/Projects/Form', [
            'project' => $project,
            'technologies' => $technologies,
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'nullable|string|max:500',
            'problem' => 'nullable|string',
            'solution' => 'nullable|string',
            'role' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'demo_url' => 'nullable|url',
            'repo_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'technology_ids' => 'array',
            'technology_ids.*' => 'exists:technologies,id',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'integer',
        ]);

        $technologyIds = $validated['technology_ids'] ?? [];
        unset($validated['technology_ids'], $validated['cover_image'], $validated['images'], $validated['remove_image_ids']);

        if ($validated['status'] === 'published' && ! $project->published_at) {
            $validated['published_at'] = now();
        }

        $project->update($validated);
        $project->technologies()->sync($technologyIds);
        $this->removeImages($project, $request->input('remove_image_ids', []));
        $this->storeUploads($request, $project);

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->images->delete($project->cover_image);

        foreach (ProjectImage::query()->whereBelongsTo($project)->get() as $image) {
            $this->images->delete($image->path);
        }

        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }

    private function storeUploads(Request $request, Project $project): void
    {
        if ($request->hasFile('cover_image')) {
            $this->images->delete($project->cover_image);
            $project->update([
                'cover_image' => $this->images->store($request->file('cover_image'), 'projects/covers'),
            ]);
        }

        $uploads = $request->file('images', []);

        if (! is_array($uploads)) {
            return;
        }

        $sortOrder = (int) $project->images()->max('sort_order');

        foreach ($uploads as $upload) {
            $sortOrder++;
            $project->images()->create([
                'path' => $this->images->store($upload, 'projects/gallery'),
                'sort_order' => $sortOrder,
            ]);
        }
    }

    /**
     * @param  array<int, mixed>  $imageIds
     */
    private function removeImages(Project $project, array $imageIds): void
    {
        $ids = array_filter($imageIds, fn (mixed $id): bool => is_numeric($id));

        ProjectImage::query()
            ->where('project_id', $project->id)
            ->whereIn('id', $ids)
            ->get()
            ->each(function (ProjectImage $image): void {
                $this->images->delete($image->path);
                $image->delete();
            });
    }
}
