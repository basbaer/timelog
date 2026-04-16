<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForstwirtLogEntry extends Model
{
    protected $fillable = [
        'forstwirt_log_id',
        'working_type_id',
        'date',
        'hours',
        'comment',
    ];

    public function log(): BelongsTo
    {
        return $this->belongsTo(ForstwirtLog::class, 'forstwirt_log_id');
    }

    public function workingType(): BelongsTo
    {
        return $this->belongsTo(ForstwirtWorkingType::class, 'working_type_id');
    }
}
