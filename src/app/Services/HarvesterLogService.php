<?php

namespace App\Services;

use App\Models\HarvesterLog;

class HarvesterLogService extends BaseLogService
{
    protected function getModel(): string
    {
        return HarvesterLog::class;
    }
}