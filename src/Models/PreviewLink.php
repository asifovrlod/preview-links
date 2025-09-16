<?php

namespace PreviewLinks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PreviewLink extends Model
{
    protected $fillable = [
        'token',
        'collection',
        'entry_id',
        'entry_slug',
        'entry_title',
        'entry_data',
        'expires_at',
        'access_count',
        'last_accessed_at',
        'created_by'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'entry_data' => 'array'
    ];

    public static function generateToken(): string
    {
        return Str::random(32);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function incrementAccess(): void
    {
        $this->increment('access_count');
        $this->update(['last_accessed_at' => now()]);
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function getPreviewUrl(): string
    {
        return route('preview-links.show', $this->token);
    }

    public function getRemainingDays(): int
    {
        return now()->diffInDays($this->expires_at, false);
    }
}