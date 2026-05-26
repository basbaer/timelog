<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForstwirtLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'working_type_id',
        'date',
        'start',
        'end',
        'pause',
        'sum',
        'comment',
    ];



    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workingType(): BelongsTo
    {
        return $this->belongsTo(ForstwirtWorkingType::class);
    }
}

    
