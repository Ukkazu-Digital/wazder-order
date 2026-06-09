<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $tenantId = auth()->user()->tenant_id ?? null;

        if (!$tenantId) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Unauthorized: No Tenant ID'], 403)
                : redirect()->route('login')->with('error', 'No tenant access');
        }

        $tenant = Tenant::findOrFail($tenantId);
        app()->instance('currentTenant', $tenant);

        return $next($request);
    }
}
