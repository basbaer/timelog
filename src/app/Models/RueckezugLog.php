<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RueckezugLog extends Model
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
        'loadings',
        'averarge_distance',
    ];

        public function project(): BelongsTo
        {
            return $this->belongsTo(Project::class);
        }

        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }
}
