<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::withTrashed()->paginate(10);
        $pending = User::pending()->count();
        return view('users.index', compact('users', 'pending'));
    }

    /**
     * Show user detail.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Approve pending user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'is_active' => 'boolean',
        ]);

        $user->update($request->only(['is_active', 'role']));

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate!');
    }

    public function approve(User $user)
    {
        if ($user->is_active) {
            return back()->with('error', 'User sudah aktif.');
        }

        $user->update([
            'is_active' => true,
            'pending_status' => 'approved',
        ]);

        return back()->with('success', 'User berhasil disetujui!');
    }

    /**
     * Reject pending user.
     */
    public function reject(User $user)
    {
        if (!$user->is_active) {
            $user->update([
                'pending_status' => 'rejected',
                'is_active' => false,
            ]);
            return back()->with('info', 'User ditolak.');
        }

        return back()->with('error', 'Hanya user pending yang bisa ditolak.');
    }
}

