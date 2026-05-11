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
        if (session()->has('username')) {
            return redirect()->route('projects.index');
        }

        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('name', $data['username'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()
                ->withErrors(['login' => 'Invalid username or password.'])
                ->withInput(['username' => $data['username']]);
        }

        session(['username' => $user->name]);

        return redirect()->route('projects.index');
    }

    public function logout(): RedirectResponse
    {
        session()->forget('username');

        return redirect()->route('login.form');
    }
}
