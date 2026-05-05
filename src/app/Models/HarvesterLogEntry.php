<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HarvesterLogEntry extends Model
{
    protected $fillable = [
        'harvester_log_id',
        'working_type_id',
        'hours',
        'comment',
    ];

    public function log(): BelongsTo
    {
        return $this->belongsTo(HarvesterLog::class, 'harvester_log_id');
    }
}
