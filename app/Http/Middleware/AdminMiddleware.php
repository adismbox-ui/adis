<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['Vous devez être connecté pour accéder à cette page.']);
        }
        
        // Vérifier si l'utilisateur est un admin
        $user = Auth::user();
        if ($user->type_compte !== 'admin') {
            // Redirection selon le type de compte
            switch ($user->type_compte) {
                case 'apprenant':
                    return redirect()->route('apprenants.dashboard');
                case 'formateur':
                    // Si c'est un formateur-assistant, rediriger vers le dashboard assistant
                    if ($user->assistant) {
                        return redirect()->route('assistant.dashboard');
                    }
                    return redirect()->route('formateurs.dashboard');
                case 'assistant':
                    return redirect()->route('assistant.dashboard');
                default:
                    return redirect('/');
            }
        }
        
        return $next($request);
    }
}
