<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Get the workers assigned to this project.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    
    /**
     * Get all Forstwirt logs associated with this project.
     */
    public function forstwirtLogs(): HasMany
    {
        return $this->hasMany(ForstwirtLog::class);
    }

    /**
     * Get all Harvester logs associated with this project.
     */
    public function harvesterLogs(): HasMany
    {
        return $this->hasMany(HarvesterLog::class);
    }

    public function rueckezugLogs(): HasMany
    {
        return $this->hasMany(RueckezugLog::class);
    }

    public function scopeOpenProjects($query)
    {
        return $query->whereNull('end_date');
    }

    public function scopeClosedProjects($query)
    {
        return $query->whereNotNull('end_date');
    }
}
