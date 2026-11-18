<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Services\StoresPortfolioImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct(private StoresPortfolioImage $images) {}

    public function edit(): Response
    {
        Gate::authorize('manage-site');

        $profile = Profile::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'TZM',
                'title' => 'Full-Stack Developer (Laravel + Vue.js)',
            ]
        );

        return Inertia::render('Admin/Profile/Edit', [
            'profile' => $profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('manage-site');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'goals' => 'nullable|string',
            'working_style' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'github_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'location' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'resume' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        unset($validated['photo'], $validated['resume']);

        $profile = Profile::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'TZM',
                'title' => 'Full-Stack Developer',
            ]
        );

        if ($request->hasFile('photo')) {
            $this->images->delete($profile->photo_path);
            $validated['photo_path'] = $this->images->store($request->file('photo'), 'profile');
        }

        if ($request->hasFile('resume')) {
            $this->images->delete($profile->resume_path);
            $validated['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        $profile->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
