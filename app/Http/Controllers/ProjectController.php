<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Technology;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = Project::published()
            ->with('technologies')
            ->latest('published_at')
            ->get();

        $technologies = Technology::query()
            ->has('projects')
            ->orderBy('name')
            ->pluck('name');

        return Inertia::render('Projects', [
            'projects' => $projects,
            'technologies' => $technologies,
        ]);
    }

    public function show(string $slug): Response
    {
        $project = Project::published()
            ->with(['technologies', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProjects = Project::published()
            ->where('id', '!=', $project->id)
            ->with('technologies')
            ->latest('published_at')
            ->take(3)
            ->get();

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
        ]);
    }
}
