<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $body_markdown
 * @property string|null $cover_image
 * @property string $status
 * @property Carbon|null $published_at
 * @property int $reading_time
 * @property int $views_count
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body_markdown',
        'cover_image',
        'status',
        'published_at',
        'reading_time',
        'views_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            $words = str_word_count(strip_tags($post->body_markdown));
            $post->reading_time = max(1, (int) ceil($words / 200));

            if ($post->status === 'published' && $post->published_at === null) {
                $post->published_at = now();
            }

            if ($post->status !== 'published') {
                $post->published_at = null;
            }
        });
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }
}
