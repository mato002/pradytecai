<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            // Redirect based on user role
            $user = Auth::user();
            if ($user->isHrManager()) {
                return redirect()->route('admin.positions.index');
            }
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
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
            
            // Clear any intended URL, especially if it points to profile
            $intended = $request->session()->pull('url.intended');
            
            // Redirect based on user role - never redirect to profile
            $user = Auth::user();
            if ($user->isHrManager()) {
                // HR Managers always go to positions page
                return redirect()->route('admin.positions.index');
            }
            
            // For admins, only use intended if it's a valid admin route (not profile)
            if ($intended && 
                str_starts_with($intended, '/admin') && 
                !str_contains($intended, '/profile') &&
                !str_contains($intended, '/login')) {
                return redirect($intended);
            }
            
            // Default: go to dashboard
            return redirect()->route('admin.dashboard');
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}

