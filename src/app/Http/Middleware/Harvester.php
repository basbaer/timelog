<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Harvester
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/');
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isHarvester() && !$user->isAdmin()) {
            return redirect('/');
        }

        // Check permission to view another user's log: only admins may view other users' logs
        $userId = $request->route('id') ?? $request->input('id');

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($userId !== null && (int) $userId !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
