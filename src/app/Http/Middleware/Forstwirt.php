<?php

namespace App\Http\Middleware;

use \App\Models\User;

class Forstwirt extends WorkerBase
{
    protected function isRole(User $user): bool
    {
        return $user->isForstwirt();
    }
}
