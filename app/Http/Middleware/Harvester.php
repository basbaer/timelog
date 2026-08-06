<?php

namespace App\Http\Middleware;

use \App\Models\User;

class Harvester extends WorkerBase
{
    protected function isRole(User $user): bool
    {
        return $user->isHarvester();
    }
}
