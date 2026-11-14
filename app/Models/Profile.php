<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $title
 * @property string|null $bio
 * @property string|null $photo_path
 * @property string|null $resume_path
 * @property string|null $email
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
        'photo_path',
        'resume_path',
        'email',
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
                'name' => 'TZM',
                'title' => 'Full-Stack Developer (Laravel + Vue.js)',
                'tagline' => 'Architecting reliable backend APIs and reactive single-page interfaces.',
                'bio' => 'Full-stack software developer specialized in Laravel and Vue.js ecosystem.',
                'location' => 'Available for Global Remote Roles',
                'email' => 'contact@tzm.dev',
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://linkedin.com',
            ]
        );
    }
}
