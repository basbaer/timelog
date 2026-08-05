<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ForstwirtWorkingType extends Model
{
    public const MOTORSAGE = 'motorsage';
    public const FREISCHNEIDER = 'freischneider';
    public const SEILMASCHINE = 'seilmaschine';
    public const MESSKLUPPE = 'messkluppe';
    public const REPARATUR = 'reparatur';
    public const OTHER = 'other';

    protected $fillable = [
        'slug',
        'name',
    ];

    /**
     * Get the logs for the working type.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ForstwirtLog::class, 'working_type_id');
    }

}
