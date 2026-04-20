<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna yang login
     */
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    /**
     * Menampilkan form edit profil
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update data profil pengguna
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Validasi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'current_password' => 'nullable|required_if:new_password,!=null',
            'new_password' => 'nullable|min:8|confirmed',
            'nis' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar di sistem',
            'current_password.required_if' => 'Password saat ini wajib diisi jika ingin mengubah password',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        // Validasi password saat ini
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai'])->withInput();
            }
        }

        // Update data pengguna
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update password jika diisi
        if ($request->filled('new_password')) {
            $user->password = Hash::make($validated['new_password']);
        }

        // Update data member jika role adalah anggota
        if ($user->role === 'anggota') {
            $user->nis = $validated['nis'] ?? $user->nis;
            $user->address = $validated['address'] ?? $user->address;
            $user->phone = $validated['phone'] ?? $user->phone;
        }

        $user->save();

        return redirect()->route('profile.show')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}
