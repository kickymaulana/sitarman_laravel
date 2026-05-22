<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WaterAbsorption;
use App\Models\DensityWaterAbsorption;
use App\Models\User;

class WaterAbsorptionController extends Controller
{

    public function index(Request $request)
    {
        $waterabsorptions = DensityWaterAbsorption::query()
            ->with(['produk_dwa', 'density_user', 'water_absoription_user']) // Eager loading relasi baru
            ->when($request->search, function ($query, $search) {
                // Pencarian disesuaikan ke kolom 'tgl' dan 'spec'
                $query->where('tgl', 'like', "%{$search}%")
                      ->orWhere('spec', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('WaterAbsorption/Index', [
            'waterabsorptions' => $waterabsorptions,
            'filters' => $request->only(['search'])
        ]);
    }

public function create()
    {
        // Mengambil daftar user untuk opsi pilihan dropdown operator
        $users = User::select('id', 'name')->orderBy('name', 'asc')->get();

        return Inertia::render('WaterAbsorption/Create', [
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl'                       => 'required|date',
            'spec'                      => 'nullable|string|max:255',
            'density_user_id'           => 'required|exists:users,id',
            'water_absoription_user_id' => 'required|exists:users,id',
            'mulai_proses'              => 'required',
            'selesai_proses'            => 'required',
            'temp_air'                  => 'required|integer|min:0',
        ], [
            'tgl.required'                       => 'Tanggal test wajib diisi.',
            'density_user_id.required'           => 'Operator Density wajib dipilih.',
            'water_absoription_user_id.required' => 'Operator Water Absorption wajib dipilih.',
            'mulai_proses.required'              => 'Jam mulai proses wajib diisi.',
            'selesai_proses.required'            => 'Jam selesai proses wajib diisi.',
            'temp_air.required'                  => 'Temperatur air wajib diisi.',
        ]);

        $data = $request->all();
        $data['spec'] = $request->spec ?? '-';

        // Menambahkan detik (:00) agar kompatibel dengan format tipe data 'time' di DB
        if (!empty($data['mulai_proses']) && strlen($data['mulai_proses']) === 5) {
            $data['mulai_proses'] .= ':00';
        }
        if (!empty($data['selesai_proses']) && strlen($data['selesai_proses']) === 5) {
            $data['selesai_proses'] .= ':00';
        }

        DensityWaterAbsorption::create($data);

        return redirect()->route('waterabsorption.index')
            ->with('message', 'Data Water Absorption berhasil ditambahkan.');
    }

    public function show(DensityWaterAbsorption $waterabsorption)
    {
        // Muat relasi baru: item produk pengetesan serta data kedua operator terkait
        $waterabsorption->load(['produk_dwa', 'density_user', 'water_absoription_user']);

        return Inertia::render('WaterAbsorption/Show', [
            'waterabsorption' => $waterabsorption
        ]);
    }

    public function edit(DensityWaterAbsorption $waterabsorption)
    {
        // Mengambil daftar user untuk opsi pilihan dropdown operator laboratorium
        $users = User::select('id', 'name')->orderBy('name', 'asc')->get();

        return Inertia::render('WaterAbsorption/Edit', [
            'waterabsorption' => $waterabsorption,
            'users'           => $users
        ]);
    }

    public function update(Request $request, DensityWaterAbsorption $waterabsorption)
    {
        $request->validate([
            'tgl'                       => 'required|date',
            'spec'                      => 'nullable|string|max:255',
            'density_user_id'           => 'required|exists:users,id',
            'water_absoription_user_id' => 'required|exists:users,id',
            'mulai_proses'              => 'required',
            'selesai_proses'            => 'required',
            'temp_air'                  => 'required|integer|min:0',
        ], [
            'tgl.required'                       => 'Tanggal pengujian wajib diisi.',
            'density_user_id.required'           => 'Operator Density wajib dipilih.',
            'water_absoription_user_id.required' => 'Operator Water Absorption wajib dipilih.',
            'mulai_proses.required'              => 'Jam mulai proses wajib diisi.',
            'selesai_proses.required'            => 'Jam selesai proses wajib diisi.',
        ]);

        $data = $request->all();
        $data['spec'] = $request->spec ?? '-';

        // Konversi format H:i dari UI ke H:i:s sebelum disimpan ke database
        if (strlen($data['mulai_proses']) === 5) {
            $data['mulai_proses'] .= ':00';
        }
        if (strlen($data['selesai_proses']) === 5) {
            $data['selesai_proses'] .= ':00';
        }

        $waterabsorption->update($data);

        return redirect()->route('waterabsorption.index')
            ->with('message', 'Data Water Absorption berhasil diperbarui.');
    }

    public function destroy(DensityWaterAbsorption $waterabsorption)
    {
        $waterabsorption->delete();

        return redirect()->route('waterabsorption.index')
            ->with('message', 'Data Water Absorption berhasil dihapus.');
    }
}
