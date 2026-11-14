<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $icon
 * @property int $sort_order
 */
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /**
     * Icon names the public pages know how to draw.
     *
     * @var list<string>
     */
    public const ICONS = [
        'LayoutDashboard',
        'Server',
        'ShoppingCart',
        'Zap',
        'ShieldCheck',
        'Database',
        'Wrench',
    ];

    protected $fillable = ['title', 'description', 'icon', 'sort_order'];
}
