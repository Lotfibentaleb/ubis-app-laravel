<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Session;
use Closure;

class Authenticate
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
//        if (! $request->expectsJson()) {
//            return route('login');
//        }
//
        if (!Session::has('bearer_token')) {
            return route('login');
        }
        return $next($request);
    }
}
