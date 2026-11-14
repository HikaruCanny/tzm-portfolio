<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'projects_count' => Project::count(),
            'published_projects_count' => Project::published()->count(),
            'skills_count' => Skill::count(),
            'experiences_count' => Experience::count(),
            'services_count' => Service::count(),
            'unread_messages_count' => ContactMessage::unread()->count(),
            'total_messages_count' => ContactMessage::count(),
        ];

        $recentProjects = Project::latest()->take(5)->get();
        $recentMessages = ContactMessage::latest()->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentProjects' => $recentProjects,
            'recentMessages' => $recentMessages,
        ]);
    }
}
