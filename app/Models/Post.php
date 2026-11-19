<?php

namespace App\Models;

use App\Models\Concerns\HasPortfolioCover;
use App\Support\PortfolioCover;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string|null $meta_description
 * @property string $body_markdown
 * @property string|null $cover_image
 * @property-read string $cover_url
 * @property string $status
 * @property bool $is_featured
 * @property Carbon|null $published_at
 * @property int $reading_time
 * @property int $views_count
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use HasPortfolioCover;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'meta_description',
        'body_markdown',
        'cover_image',
        'status',
        'is_featured',
        'published_at',
        'reading_time',
        'views_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    protected function portfolioCoverDefault(): string
    {
        return PortfolioCover::BLOG;
    }
}
