<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Density;

class DensityController extends Controller
{
    public function index(Request $request)
    {
        $densities = Density::query()
            ->with(['produk_density', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where('tgl', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Density/Index', [
            // Menggunakan huruf kecil 'densities' agar konsisten dengan route & prop Vue
            'densities' => $densities,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Density/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl' => 'required|date',
        ], [
            'tgl.required' => 'Tanggal wajib diisi.',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        Density::create($data);

        return redirect()->route('density.index')->with('message', 'Data Density berhasil ditambahkan.');
    }

    public function show(Density $density)
    {
        $density->load(['produk_density', 'user']);

        return Inertia::render('Density/Show', [
            'density' => $density
        ]);
    }

    public function edit(Density $density)
    {
        return Inertia::render('Density/Edit', [
            'density' => $density
        ]);
    }

    public function update(Request $request, Density $density)
    {
        $request->validate([
            'tgl' => 'required|date',
        ], [
            'tgl.required' => 'Tanggal wajib diisi.',
        ]);

        $data = $request->all();
        $density->update($data);

        return redirect()->route('density.index')->with('message', 'Data Density berhasil diperbarui.');
    }

    public function destroy(Density $density)
    {
        $density->delete();
        return redirect()->route('density.index')->with('message', 'Data Density berhasil dihapus.');
    }
}
