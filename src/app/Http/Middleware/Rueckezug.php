<?php

namespace App\Http\Middleware;

use \App\Models\User;

class Rueckezug extends Worker
{
    protected function isRole(User $user): bool
    {
        return $user->isRueckezug();
    }
}