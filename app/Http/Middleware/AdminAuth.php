<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && in_array($user->role, [UserRole::ADMIN, UserRole::MANAGER, UserRole::STAFF])) {
            return $next($request);
        }

        session()->flash('warning', 'Unauthorized!');

        return redirect()->route('home');
    }
}
