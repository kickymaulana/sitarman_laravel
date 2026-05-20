<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ThermalShock;
use App\Models\ThermalOven;
use App\Models\ThermalPintu;

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $thermalshocks = ThermalShock::query()
            ->with(['thermalOven', 'thermalPintu', 'produks'])
            ->when($request->search, function ($query, $search) {
                // Mencari berdasarkan tanggal proses atau nama di tabel relasi
                $query->where('hari_tgl', 'like', "%{$search}%")
                      ->orWhereHas('thermalOven', function($q) use ($search) {
                          $q->where('thermal_oven', 'like', "%{$search}%");
                      })
                      ->orWhereHas('thermalPintu', function($q) use ($search) {
                          $q->where('thermal_pintu', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ThermalShock/Index', [
            'thermalshocks' => $thermalshocks,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('ThermalShock/Create', [
            'ovens' => ThermalOven::select('id', 'thermal_oven')->orderBy('thermal_oven')->get(),
            'pintus' => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'thermal_oven_id'     => 'required|exists:thermal_oven,id',
            'thermal_pintu_id'    => 'required|exists:thermal_pintu,id',
            'hari_tgl'            => 'required|date',
            'suhu_testing'        => 'required|integer',
            'suhu_display'        => 'required|integer',
            'suhu_actual'         => 'required|integer',
            'jam_awal_proses'     => 'required',
            'jam_capai_suhu'      => 'required',
            'suhu_awal'           => 'required|integer',
            'suhu_air'            => 'required|string|max:255',
            'jam_mulai_tembak'    => 'required',
            'jam_selesai_tembak'  => 'nullable',
        ], [
            'thermal_oven_id.required'  => 'Oven wajib dipilih.',
            'thermal_pintu_id.required' => 'Pintu wajib dipilih.',
            'hari_tgl.required'         => 'Hari/Tanggal wajib diisi.',
            // ... Kamu bisa menambahkan custom message lainnya di sini
        ]);

        // Ambil semua data request, lalu sisipkan user_id di dalamnya
        $data = $request->all();
        $data['user_id'] = auth()->id();

        ThermalShock::create($data);

        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil ditambahkan.');
    }

    public function edit(ThermalShock $thermalshock)
    {
        return Inertia::render('ThermalShock/Edit', [
            'thermalshock' => $thermalshock,
            'ovens'        => ThermalOven::select('id', 'thermal_oven')->orderBy('thermal_oven')->get(),
            'pintus'       => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get()
        ]);
    }

    public function update(Request $request, ThermalShock $thermalshock)
    {
        $request->validate([
            'thermal_oven_id'     => 'required|exists:thermal_oven,id',
            'thermal_pintu_id'    => 'required|exists:thermal_pintu,id',
            'hari_tgl'            => 'required|date',
            'suhu_testing'        => 'required|integer',
            'suhu_motor'          => 'nullable|string|max:255',
            'suhu_display'        => 'required|integer',
            'suhu_actual'         => 'required|integer',
            'jam_awal_proses'     => 'required',
            'jam_capai_suhu'      => 'required',
            'suhu_awal'           => 'required|integer',
            'suhu_air'            => 'required|string|max:255',
            'jam_mulai_tembak'    => 'required',
            'jam_selesai_tembak'  => 'nullable',
        ]);

        $thermalshock->update($request->all());

        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil diperbarui.');
    }

    public function show(ThermalShock $thermalshock)
    {
        // Load relasi jika belum ter-load otomatis
        $thermalshock->load(['thermalOven', 'thermalPintu']);

        return Inertia::render('ThermalShock/Show', [
            'thermalshock' => $thermalshock
        ]);
    }

    public function destroy(ThermalShock $thermalshock)
    {
        $thermalshock->delete();
        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil dihapus.');
    }
}
