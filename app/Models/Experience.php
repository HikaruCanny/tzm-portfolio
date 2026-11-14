<?php

namespace App\Models;

use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $type work|education|certification
 * @property string $organization
 * @property string $role
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string|null $description
 * @property int $sort_order
 */
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    protected $fillable = [
        'type', 'organization', 'role', 'start_date', 'end_date', 'description', 'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
