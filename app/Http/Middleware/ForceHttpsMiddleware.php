<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttpsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Force HTTPS in production, but not on Render (which handles SSL automatically)
        if (!$request->secure() && app()->environment('production') && !$this->isRenderEnvironment()) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }

    /**
     * Check if we're running on Render
     */
    private function isRenderEnvironment(): bool
    {
        return !empty(env('RENDER')) || 
               str_contains(env('APP_URL', ''), 'onrender.com') ||
               str_contains(env('APP_URL', ''), 'adis-ci.net');
    }
}
