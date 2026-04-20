<?php

namespace App\Http\Controllers;

use App\Models\PenanggungJawab;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenanggungJawabController extends Controller
{
    public function index()
    {
        $penanggungJawabs = PenanggungJawab::orderBy('nama')->get();
        return view('penanggung_jawab.index', compact('penanggungJawabs'));
    }

    public function create()
    {
        return view('penanggung_jawab.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:50|unique:penanggung_jawabs,nip',
            'jabatan' => 'required|string|max:100',
            'no_hp' => ['required', 'regex:/^[0-9]+$/', 'min:9', 'max:20'],
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        PenanggungJawab::create($validated);

        return redirect()->route('penanggung-jawab.index')
            ->with('success', 'Penanggung jawab berhasil ditambahkan.');
    }

    public function edit(PenanggungJawab $penanggungJawab)
    {
        return view('penanggung_jawab.edit', compact('penanggungJawab'));
    }

    public function update(Request $request, PenanggungJawab $penanggungJawab)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('penanggung_jawabs', 'nip')->ignore($penanggungJawab->id),
            ],
            'jabatan' => 'required|string|max:100',
            'no_hp' => ['required', 'regex:/^[0-9]+$/', 'min:9', 'max:20'],
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $penanggungJawab->update($validated);

        return redirect()->route('penanggung-jawab.index')
            ->with('success', 'Penanggung jawab berhasil diperbarui.');
    }

    public function destroy(PenanggungJawab $penanggungJawab)
    {
        if ($penanggungJawab->loans()->exists()) {
            return redirect()->route('penanggung-jawab.index')
                ->with('error', 'Penanggung jawab tidak dapat dihapus karena telah digunakan pada peminjaman.');
        }

        $penanggungJawab->delete();

        return redirect()->route('penanggung-jawab.index')
            ->with('success', 'Penanggung jawab berhasil dihapus.');
    }
}
