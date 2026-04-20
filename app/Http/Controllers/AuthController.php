<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationPending;

class AuthController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
$credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Akun Anda masih menunggu persetujuan admin.');
            }

            return redirect()->route('dashboard'); 
        }

        $request->session()->regenerateToken();
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home'); 
    }

    /**
     * Tampilkan form register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:users',
            'email' => 'required|email|unique:users',
            'address' => 'required|string|max:1000',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        $nisValid = $this->validateNIS($validated['nis']);

        $user = User::create([
            'name' => $validated['name'],
            'nis' => $validated['nis'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'is_active' => $nisValid ? 1 : 0,
            'pending_status' => $nisValid ? 'approved' : 'pending',
            'role' => 'user',
        ]);

        // Auto create member from registration
        \App\Models\Member::firstOrCreate(
            ['nis' => $user->nis],
            [
                'name' => $user->name,
                'address' => $user->address,
                'phone' => $user->phone
            ]
        );

        $message = $nisValid 
            ? 'Registrasi berhasil! Silakan login untuk mengakses dashboard.' 
            : 'Registrasi berhasil! Menunggu persetujuan admin.';
        session()->flash('success', $message);
        return redirect()->route('login');
    }

    /**
     * Validasi NIS vs DB sekolah
     */
    private function validateNIS($nis)
    {
        return in_array($nis, ['12345678', '87654321']); // Demo
    }
}
