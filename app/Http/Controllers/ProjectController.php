<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Technology;
use App\Support\Seo;
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

        return $this->inertiaWithSeo('Projects', [
            'projects' => $projects,
            'technologies' => $technologies,
        ], Seo::forStatic(
            title: 'Projects Portfolio',
            description: 'Case studies across Laravel APIs, Inertia admin tools, and Vue product interfaces.',
            path: '/projects',
        ));
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

        return $this->inertiaWithSeo('Projects/Show', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
        ], Seo::forProject($project));
    }
}
