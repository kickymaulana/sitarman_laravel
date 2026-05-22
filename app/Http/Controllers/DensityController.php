<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Density;
use App\Models\DensityWaterAbsorption;
use App\Models\User;

class DensityController extends Controller
{
    public function index(Request $request)
    {
        $densities = DensityWaterAbsorption::query()
            ->with(['produk_dwa', 'density_user', 'water_absoription_user']) // Mengikuti relasi model baru
            ->when($request->search, function ($query, $search) {
                // Pencarian berdasarkan tanggal
                $query->where('tgl', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Density/Index', [
            'densities' => $densities,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        // Mengambil data user untuk pilihan dropdown Operator
        $users = User::select('id', 'name')->orderBy('name', 'asc')->get();

        return Inertia::render('Density/Create', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl'                       => 'required|date',
            'density_user_id'           => 'required|exists:users,id',
            'water_absoription_user_id' => 'required|exists:users,id',
            'spec'                      => 'nullable|string|max:255',
            'mulai_proses'              => 'required|date_format:H:i:s',
            'selesai_proses'            => 'required|date_format:H:i:s',
            'temp_air'                  => 'required|integer|min:0',
        ], [
            'tgl.required'                       => 'Tanggal wajib diisi.',
            'density_user_id.required'           => 'Operator Density wajib dipilih.',
            'water_absoription_user_id.required' => 'Operator Water Absorption wajib dipilih.',
            'mulai_proses.required'              => 'Jam mulai proses wajib diisi.',
            'selesai_proses.required'            => 'Jam selesai proses wajib diisi.',
            'temp_air.required'                  => 'Temperatur air wajib diisi.',
        ]);

        // Default value jika spec kosong
        $data = $request->all();
        if (empty($data['spec'])) {
            $data['spec'] = '-';
        }

        DensityWaterAbsorption::create($data);

        return redirect()->route('density.index')
            ->with('message', 'Data Density & Water Absorption berhasil ditambahkan.');
    }

    public function show(DensityWaterAbsorption $density)
    {
        // Muat relasi baru sesuai arsitektur table
        $density->load(['produk_dwa', 'density_user', 'water_absoription_user']);

        return Inertia::render('Density/Show', [
            'density' => $density
        ]);
    }

    public function edit(DensityWaterAbsorption $density)
    {
        // Mengambil daftar nama user untuk opsi dropdown select
        $users = User::select('id', 'name')->orderBy('name', 'asc')->get();

        return Inertia::render('Density/Edit', [
            'density' => $density,
            'users'   => $users
        ]);
    }

    public function update(Request $request, DensityWaterAbsorption $density)
    {
        $request->validate([
            'tgl'                       => 'required|date',
            'density_user_id'           => 'required|exists:users,id',
            'water_absoription_user_id' => 'required|exists:users,id',
            'spec'                      => 'nullable|string|max:255',
            'mulai_proses'              => 'required|date_format:H:i:s',
            'selesai_proses'            => 'required|date_format:H:i:s',
            'temp_air'                  => 'required|integer|min:0',
        ], [
            'tgl.required'                       => 'Tanggal wajib diisi.',
            'density_user_id.required'           => 'Operator Density wajib dipilih.',
            'water_absoription_user_id.required' => 'Operator Water Absorption wajib dipilih.',
            'mulai_proses.required'              => 'Jam mulai proses wajib diisi.',
            'selesai_proses.required'            => 'Jam selesai proses wajib diisi.',
            'temp_air.required'                  => 'Temperatur air wajib diisi.',
        ]);

        $data = $request->all();
        if (empty($data['spec'])) {
            $data['spec'] = '-';
        }

        $density->update($data);

        return redirect()->route('density.index')
            ->with('message', 'Data Density & Water Absorption berhasil diperbarui.');
    }

    public function destroy(DensityWaterAbsorption $density)
    {
        $density->delete();

        return redirect()->route('density.index')
            ->with('message', 'Data Density & Water Absorption berhasil dihapus.');
    }
}
