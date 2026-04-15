<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function scopeAdmin($query)
    {
        /**
         * Filters the query to only include users with the admin role.
         * 
         * Uses a HasMany or HasOne relationship constraint to check the related 'role' model.
         * The whereHas() method ensures only users whose role record has a slug of 'admin' are returned.
         * 
         * This approach is used instead of a direct join because:
         * - It maintains relationship integrity and leverages Eloquent's relationship definitions
         * - It efficiently filters based on related model attributes without explicit joins
         * - It allows for cleaner, more readable code when filtering by relationship conditions
         * - It supports both single and multiple related records through the same interface
         * 
         * @param \Illuminate\Database\Eloquent\Builder $query
         * @return \Illuminate\Database\Eloquent\Builder
         */
        return $query->whereHas('role', function ($q) {
                    $q->where('slug', 'admin');
                });
    }

    /**
     * Scope a query to only include workers (every role except admin).
     */
    #[Scope]
    public function scopeWorker(Builder $query):void
    {
        $query->whereHas('role', function ($q) {
            $q->whereNot('slug', Role::ADMIN);
        });
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === Role::ADMIN;
    }

    public function isRueckezug(): bool
    {
        return $this->role?->slug === Role::RUECKEZUG;
    }

    public function isForstwirt(): bool
    {
        return $this->role?->slug === Role::FORSTWIRT;
    }

    public function isHarvester(): bool
    {
        return $this->role?->slug === Role::HARVESTER;
    }

    public function openProjects(): Builder
    {
        return Project::query()
            ->openProjects()
            ->whereHas('roles', function (Builder $query) {
                $query->whereKey($this->role_id);
            });
    }
}
