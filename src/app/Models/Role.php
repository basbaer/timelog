<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const ADMIN = 'admin';
    /**
     * Get the users for the role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function scopeWorker($query)
    {
        return $query->where('slug', '!=', 'admin');
    }

    public function scopeAdmin($query)
    {
        return $query->where('slug', 'admin');
    }

    public static function admin(): ?self
    {
        return static::where('slug', 'admin')->first();
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}


