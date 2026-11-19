<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string|null $bio
 * @property string|null $goals
 * @property string|null $working_style
 * @property string|null $photo_path
 * @property string|null $resume_path
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $github_url
 * @property string|null $linkedin_url
 * @property string|null $location
 * @property string|null $tagline
 */
class Profile extends Model
{
    protected $table = 'profile';

    protected $fillable = [
        'name',
        'title',
        'bio',
        'goals',
        'working_style',
        'photo_path',
        'resume_path',
        'email',
        'phone',
        'github_url',
        'linkedin_url',
        'location',
        'tagline',
    ];

    /** Return the single site profile row (creates default if missing). */
    public static function instance(): static
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'name' => config('portfolio.name'),
                'title' => config('portfolio.title'),
                'tagline' => 'Laravel APIs and Vue interfaces that stay maintainable after launch.',
                'bio' => 'I build full-stack web products with Laravel, Inertia, and Vue 3 — admin panels, REST APIs, and recruiter-friendly portfolio experiences.',
                'goals' => 'Join a product-minded engineering team (remote-friendly) where I can own features end-to-end.',
                'working_style' => 'Async-first collaboration, short written specs, and small PRs with demos.',
                'location' => config('portfolio.location'),
                'email' => config('portfolio.contact_email'),
                'phone' => config('portfolio.phone'),
                'github_url' => config('portfolio.github_url'),
                'linkedin_url' => config('portfolio.linkedin_url'),
            ]
        );
    }

    public function hasResume(): bool
    {
        return is_string($this->resume_path)
            && $this->resume_path !== ''
            && Storage::disk('public')->exists($this->resume_path);
    }

    public function resumeDownloadFileName(): string
    {
        $base = Str::slug($this->name) ?: 'tzm';

        return "{$base}-resume.pdf";
    }

    /**
     * Public pages: omit stored path; expose only whether download is available.
     *
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'tagline' => $this->tagline,
            'bio' => $this->bio,
            'goals' => $this->goals,
            'working_style' => $this->working_style,
            'email' => $this->email,
            'phone' => $this->phone,
            'github_url' => $this->github_url,
            'linkedin_url' => $this->linkedin_url,
            'location' => $this->location,
            'photo_path' => $this->photo_path,
            'has_resume' => $this->hasResume(),
            'resume_url' => $this->hasResume() ? route('resume.download') : null,
        ];
    }
}
