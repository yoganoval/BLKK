<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PesertaLoginController extends Controller
{
    public function create()
    {
        return inertia('Auth/PesertaLogin');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('peserta')->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'is_active' => true,
        ])) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah, atau akun tidak aktif.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('peserta.dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::guard('peserta')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('peserta.login');
    }
}
