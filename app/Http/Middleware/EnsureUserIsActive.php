<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user && property_exists($user, 'actif') && !$user->actif) {
            \Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'Votre compte est désactivé.']);
        }
        return $next($request);
    }
}

