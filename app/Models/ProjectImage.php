<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $project_id
 * @property string $path
 * @property string|null $caption
 * @property int $sort_order
 */
class ProjectImage extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'path', 'caption', 'sort_order'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
