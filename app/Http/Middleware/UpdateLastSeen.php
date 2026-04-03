<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->last_seen || $user->last_seen->diffInMinutes(now()) >= User::ACTIVITY_TIMEOUT) {
                $user->last_seen = now();
                $user->save();
            }
        }

        return $next($request);
    }
}
