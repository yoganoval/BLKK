<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create()
    {
        return inertia('Auth/Login');
    }

    /**
     * Login admin/pengurus atau peserta
     * melalui satu halaman login.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        // 1. Coba login admin atau pengurus.
        if (Auth::guard('web')->attempt(
            $credentials,
            $remember
        )) {
            $request->session()->regenerate();

            return redirect()->intended(
                route('dashboard')
            );
        }

        // 2. Jika gagal, coba login peserta.
        if (Auth::guard('peserta')->attempt(
            $credentials,
            $remember
        )) {
            $request->session()->regenerate();

            return redirect()->intended(
                route('peserta.dashboard')
            );
        }

        // 3. Jika keduanya gagal.
        throw ValidationException::withMessages([
            'username' => 'Username atau password salah.',
        ]);
    }

    /**
     * Logout.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('peserta')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
