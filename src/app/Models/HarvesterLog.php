<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Project;
use App\Models\User;

class HarvesterLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'date',
        'start',
        'end',
        'pause',
        'sum',
        'bs_from',
        'bs_to',
        'bs_diff',
        'fm_amount',
        'fm_total',
        'fm_day',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
