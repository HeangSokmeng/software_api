<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckUserRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $routeName = Route::currentRouteName();
            if ($user->account_type === 'superadmin') {
                return $next($request);
            }
            $adminRoutes = ['admin.*'];
            $teacherRoutes = ['teacher.*'];
            if ($user->account_type === 'admin') {
                if ($this->isRouteInArray($routeName, $adminRoutes)) {
                    return $next($request);
                } else {
                    return response()->json(['message' => 'Managers cannot access super admin routes'], 403);
                }
            }
            if ($user->account_type === 'teacher') {
                if ($this->isRouteInArray($routeName, $teacherRoutes)) {
                    return $next($request);
                } else {
                    return response()->json(['message' => 'Teachers cannot access admin routes'], 403);
                }
            }
        }
        return response()->json(['message' => 'Forbidden'], 403);
    }
    private function isRouteInArray($routeName, array $allowedRoutes)
    {
        foreach ($allowedRoutes as $allowed) {
            if (fnmatch($allowed, $routeName)) {
                return true;
            }
        }
        return false;
    }
}
