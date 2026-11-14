<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', ActivityLog::class);

        $logs = ActivityLog::query()
            ->with('user:id,name,email')
            ->latest()
            ->paginate(8);

        return Inertia::render('Admin/Activity/Index', [
            'logs' => $logs,
        ]);
    }
}
