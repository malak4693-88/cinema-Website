<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function loginForm(): View|RedirectResponse
    {
        // Show the login page to the admin.
        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        // Validate that both username and password were entered.
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Find the user from the users table using the entered username.
        $user = User::where('name', $data['username'])->first();

        // If the user does not exist or the password is wrong, return to login with an error.
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()
                ->withErrors(['login' => 'Invalid username or password.'])
                ->withInput(['username' => $data['username']]);
        }

        // Save the username in the Laravel session so protected pages know the user is logged in.
        session(['username' => $user->name]);

        // After successful login, open the dashboard.
        return redirect()->route('dashboard');
    }

    public function logout(): RedirectResponse
    {
        // Remove the username from the session to log the admin out.
        session()->forget('username');

        // Return the user to the login page after logout.
        return redirect()->route('login.form');
    }
}
