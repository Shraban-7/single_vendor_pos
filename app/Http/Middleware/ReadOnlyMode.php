<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $except = [
            'logout',
            'admin.ecommerce-sales.update-status',
            'admin.ecommerce-sales.update-tracking',
            'admin.ecommerce-sales.update-notes',
            'admin.cashRegister.open',
            'admin.cashRegister.close',
            'admin.pos.store',
            'admin.pos.update',
            'admin.pos.saveDraft',
            'admin.pos.cart.add',
            'admin.pos.cart.update',
            'admin.pos.cart.remove',
            'admin.pos.cart.updateItemPrice',
            'admin.pos.cart.clear',
        ];

        if ($request->route() && in_array($request->route()->getName(), $except)) {
            return $next($request);
        }

        if (!$request->isMethod('get')) {
            if ($request->ajax() || $request->expectsJson()) {
                return errorResponse('This action is disabled in the demo version.', 403);
            }

            return redirect()->back()->with('error', 'This action is disabled in the demo version.');
        }

        return $next($request);
    }
}
