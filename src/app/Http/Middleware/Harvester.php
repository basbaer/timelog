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
        $workerId = $request->route('worker_id') ?? $request->input('worker_id');

        if ($user->isAdmin()) {
            return $next($request);
        }

        // When there is a worker_id in the route or request, check if it matches the authenticated user's id
        if ($workerId !== null && (int) $workerId !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
