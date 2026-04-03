<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'is_read',
        'replied_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'replied_at' => 'datetime',
    ];

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeReplied($query)
    {
        return $query->whereNotNull('replied_at');
    }

    public function scopeNotReplied($query)
    {
        return $query->whereNull('replied_at');
    }

    // Helpers
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function markAsReplied(): void
    {
        $this->update(['replied_at' => now()]);
    }

    public function hasBeenReplied(): bool
    {
        return !is_null($this->replied_at);
    }
}
