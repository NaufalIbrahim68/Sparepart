<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'npk' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('npk', 'password');

        // Fetch the user by npk
        $user = \App\Models\User::where('npk', $request->input('npk'))->first();

        // Check if the user exists and the password matches (using hash verification)
        if ($user && Hash::check($request->input('password'), $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            // Redirect based on user role
            return redirect()->intended($user->isAdmin() ? 'dashboard' : 'dashboarduser');
        }

        throw ValidationException::withMessages([
            'npk' => 'NPK atau Password salah tolong login kembali',
        ]);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
