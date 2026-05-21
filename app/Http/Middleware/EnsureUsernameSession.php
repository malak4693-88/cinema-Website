<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUsernameSession
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        // If the session does not have a username, the user is not logged in.
        if (! $request->session()->has('username')) {
            // Redirect guests back to the login page.
            return redirect()->route('login.form');
        }

        // If the username exists in session, allow the request to continue.
        return $next($request);
    }
}
