<?php

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;

test('activity log shows eight entries per page', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);

    foreach (range(1, 9) as $index) {
        ActivityLog::query()->create([
            'user_id' => $superadmin->id,
            'action' => 'updated',
            'subject_type' => Project::class,
            'subject_id' => $index,
        ]);
    }

    $this->actingAs($superadmin)
        ->get('/admin/activity')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Admin/Activity/Index')
            ->has('logs.data', 8)
            ->where('logs.total', 9)
            ->where('logs.per_page', 8)
        );
});

test('regular admins cannot view the activity log', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get('/admin/activity')->assertForbidden();
});
