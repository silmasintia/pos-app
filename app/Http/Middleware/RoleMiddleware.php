<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Pastikan user login
        if (!$user) {
            abort(403, 'Unauthorized. User not logged in.');
        }

        // Pastikan user punya role
        if (!$user->role) {
            abort(403, 'Unauthorized. Role not assigned.');
        }

        // Ambil slug role user dan buat case-insensitive
        $userRole = strtolower($user->role->slug);
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized. You do not have permission.');
        }

        return $next($request);
    }
}
