<?php

namespace App\Services;

use App\Models\RueckezugLog;

class RueckezugLogService extends BaseLogService
{
    protected function getModel(): string
    {
        return RueckezugLog::class;
    }
}