<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $subject
 * @property string $message
 * @property string|null $ip_hash
 * @property Carbon|null $read_at
 * @property Carbon|null $handled_at
 * @property bool $is_spam
 */
class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'ip_hash',
        'read_at',
        'handled_at',
        'is_spam',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'handled_at' => 'datetime',
        'is_spam' => 'boolean',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at')->where('is_spam', false);
    }
}
