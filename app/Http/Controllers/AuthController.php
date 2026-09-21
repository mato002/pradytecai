<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->homeRedirect(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $intended = $request->session()->pull('url.intended');
            $user = Auth::user();

            if (! $user->can('dashboard.view')) {
                return redirect()->route('admin.positions.index');
            }

            if ($intended
                && str_starts_with($intended, '/admin')
                && ! str_contains($intended, '/profile')
                && ! str_contains($intended, '/login')) {
                return redirect($intended);
            }

            return redirect()->route('admin.dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    private function homeRedirect($user)
    {
        if (! $user->can('dashboard.view')) {
            return redirect()->route('admin.positions.index');
        }

        return redirect()->route('admin.dashboard');
    }
}
