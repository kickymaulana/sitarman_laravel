<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Spesifikasi;

class SpesifikasiController extends Controller
{
    public function index(Request $request)
    {
        $spesifikasis = Spesifikasi::query()
            ->when($request->search, function ($query, $search) {
                $query->where('spesifikasi', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Spesifikasi/Index', [
            'spesifikasis' => $spesifikasis,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Spesifikasi/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'spesifikasi' => 'required|string|max:255|unique:spesifikasi,spesifikasi',
        ], [
            'spesifikasi.required' => 'Spesifikasi wajib diisi.',
            'spesifikasi.unique' => 'Spesifikasi ini sudah terdaftar.',
        ]);

        Spesifikasi::create($request->only('spesifikasi'));

        return redirect()->route('spesifikasi.index')->with('message', 'Data spesifikasi berhasil ditambahkan.');
    }

    public function edit(Spesifikasi $spesifikasi)
    {
        return Inertia::render('Master/Spesifikasi/Edit', [
            'spesifikasi' => $spesifikasi
        ]);
    }

    public function update(Request $request, Spesifikasi $spesifikasi)
    {
        $request->validate([
            'spesifikasi' => 'required|string|max:255|unique:spesifikasi,spesifikasi,' . $spesifikasi->id,
        ], [
            'spesifikasi.required' => 'Spesifikasi wajib diisi.',
            'spesifikasi.unique' => 'Spesifikasi ini sudah terdaftar.',
        ]);

        $spesifikasi->update($request->only('spesifikasi'));

        return redirect()->route('spesifikasi.index')->with('message', 'Data spesifikasi berhasil diperbarui.');
    }

    public function destroy(Spesifikasi $spesifikasi)
    {
        $spesifikasi->delete();
        return redirect()->route('spesifikasi.index')->with('message', 'Data spesifikasi berhasil dihapus.');
    }
}
