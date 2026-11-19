<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('resume download returns not found when no pdf is stored', function () {
    Storage::fake('public');

    Profile::query()->updateOrCreate(
        ['id' => 1],
        ['name' => 'TZM', 'title' => 'Developer', 'resume_path' => null]
    );

    $this->get(route('resume.download'))->assertNotFound();
});

test('resume download returns not found when path is missing on disk', function () {
    Storage::fake('public');

    Profile::query()->updateOrCreate(
        ['id' => 1],
        ['name' => 'TZM', 'title' => 'Developer', 'resume_path' => 'resumes/missing.pdf']
    );

    $this->get(route('resume.download'))->assertNotFound();
});

test('superadmin can upload resume and public visitors can download it', function () {
    Storage::fake('public');

    $superadmin = User::factory()->withTwoFactor()->create(['role' => 'superadmin']);

    $pdf = UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf');

    $this->actingAs($superadmin)
        ->post(route('admin.profile.update'), [
            'name' => 'Thant Zin Moe',
            'title' => 'Full-Stack Developer',
            'resume' => $pdf,
        ])
        ->assertRedirect();

    $profile = Profile::instance();

    expect($profile->hasResume())->toBeTrue()
        ->and(Storage::disk('public')->exists($profile->resume_path))->toBeTrue();

    $this->get(route('resume.download'))
        ->assertOk()
        ->assertDownload('thant-zin-moe-resume.pdf');

    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('profile.has_resume', true)
            ->where('profile.resume_url', route('resume.download'))
        );
});

test('superadmin can remove uploaded resume', function () {
    Storage::fake('public');

    $path = UploadedFile::fake()->create('resume.pdf', 50, 'application/pdf')->store('resumes', 'public');

    Profile::query()->updateOrCreate(
        ['id' => 1],
        ['name' => 'TZM', 'title' => 'Developer', 'resume_path' => $path]
    );

    $superadmin = User::factory()->withTwoFactor()->create(['role' => 'superadmin']);

    $this->actingAs($superadmin)
        ->post(route('admin.profile.update'), [
            'name' => 'TZM',
            'title' => 'Developer',
            'remove_resume' => '1',
        ])
        ->assertRedirect();

    $profile = Profile::instance()->fresh();

    expect($profile->resume_path)->toBeNull()
        ->and($profile->hasResume())->toBeFalse();

    $this->get(route('resume.download'))->assertNotFound();
});
