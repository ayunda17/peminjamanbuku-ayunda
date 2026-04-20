<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\PaginationService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Menampilkan daftar semua anggota dengan paginasi
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        
        // Query anggota dengan filter pencarian
        $query = Member::query();
        
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }
        
        // Hitung total data
        $totalMembers = $query->count();
        
        // Setup pagination
        $pagination = new PaginationService($page, 10);
        $pagination->setTotal($totalMembers);
        
        // Get data dengan limit dan offset
        $members = $query->offset($pagination->getOffset())
                         ->limit($pagination->getPerPage())
                         ->get();
        
        return view('members.index', [
            'members' => $members,
            'pagination' => $pagination->toArray(),
            'search' => $search,
        ]);
    }

    /**
     * Menampilkan form tambah anggota
     */
    public function create()
    {
        return view('members.create');
    }

    /**
     * Menyimpan anggota baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail anggota
     */
    public function show(Member $member)
    {
        $loans = $member->loans()->with('book')->get();
        return view('members.show', compact('member', 'loans'));
    }

    /**
     * Menampilkan form edit anggota
     */
    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update data anggota
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
        ]);

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui!');
    }

    /**
     * Hapus anggota
     */
    public function destroy(Member $member)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('members.index')->with('error', 'Akses ditolak. Hanya admin yang dapat menghapus anggota.');
        }

        // Cek apakah anggota memiliki peminjaman aktif
        if ($member->loans()->whereNull('return_date')->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus anggota yang memiliki peminjaman aktif!');
        }

        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Anggota berhasil dihapus!');
    }
}
