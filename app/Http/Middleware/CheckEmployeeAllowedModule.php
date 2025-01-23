<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeAllowedModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $current_route = $request->route()->getName();
        $product = config('app.product');

        $notAllowed = [
            'opap' => [
                'routes' => [
                    '/employee/leave*'  
                ]
            ]
        ];

        // Check if the current route is not 'employee.dashboard'
        if ($current_route !== 'employee.dashboard') {
            if(Auth::guard('employee')->user()->information->employment_type_id !== 1) {
                if (isset($notAllowed[$product])) {
                    foreach ($notAllowed[$product]['routes'] as $routePattern) {
                        // Remove the leading slash and convert to match the pattern
                        $routePattern = ltrim($routePattern, '/');

                        // Check if the current route matches the wildcard pattern
                        if ($request->is($routePattern)) {
                            return redirect()->route('employee.dashboard');
                        }
                    }
                }
            }
        }

        
        return $next($request);
    }
}
