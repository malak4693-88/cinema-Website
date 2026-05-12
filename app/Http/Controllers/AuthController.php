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
        if (session()->has('username') && session('server_pid') !== getmypid()) {
            session()->forget(['username', 'dashboard_access_key', 'server_pid']);
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

        session([
            'username' => $user->name,
            'dashboard_access_key' => bin2hex(random_bytes(16)),
            'dashboard_tab_key' => bin2hex(random_bytes(16)),
            'server_pid' => getmypid(),
        ]);

        return redirect()->route('dashboard', [
            'access' => session('dashboard_access_key'),
        ]);
    }

    public function logout(): RedirectResponse
    {
        session()->forget(['username', 'dashboard_access_key', 'dashboard_tab_key', 'server_pid']);

        return redirect()
            ->route('login.form')
            ->withoutCookie('dashboard_tab');
    }

    public function dashboardLoginRequired(): RedirectResponse
    {
        session()->forget(['username', 'dashboard_access_key', 'dashboard_tab_key', 'server_pid']);

        return redirect()
            ->route('login.form')
            ->withoutCookie('dashboard_tab');
    }
}
