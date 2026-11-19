<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property Carbon|null $archived_at
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
        'archived_at',
        'is_spam',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'handled_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_spam' => 'boolean',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at')->where('is_spam', false);
    }

    public function scopeNotSpam($query)
    {
        return $query->where('is_spam', false);
    }

    public function scopeSpam($query)
    {
        return $query->where('is_spam', true);
    }

    public function scopeHandled($query)
    {
        return $query->whereNotNull('handled_at')->notSpam();
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ContactReply::class);
    }
}
