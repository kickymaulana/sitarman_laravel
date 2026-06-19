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
            ->with(['thermalOven', 'thermalPintu', 'produks', 'user'])
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
            'jam_mulai_tembak'    => 'nullable',
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

        // JIKA kosong/null, isi dengan '00:00:00' sebelum disimpan ke database
        $data['jam_mulai_tembak'] = $request->jam_mulai_tembak ?? '00:00:00';
        $data['jam_selesai_tembak'] = $request->jam_selesai_tembak ?? '00:00:00';

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
        $thermalshock->load(['thermalOven', 'thermalPintu', 'user']);


        // Ambil daftar thermal shock lain (misal 30 log terakhir) untuk opsi target copy
        // Kita format tampilannya agar operator mudah mengenali log target
        $thermalShockOptions = ThermalShock::with(['thermalOven', 'thermalPintu'])
            ->where('id', '!=', $thermalshock->id)
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => "Tanggal: " . date('d-m-Y', strtotime($item->hari_tgl)) .
                               " | Suhu: " . ($item->suhu_testing ?? '-') .
                               " | Pintu: " . ($item->thermalPintu->thermal_pintu ?? '-')
                ];
            });

        return Inertia::render('ThermalShock/Show', [
            'thermalshock' => $thermalshock,
            'thermalShockOptions' => $thermalShockOptions
        ]);
    }

    public function copyProduk(Request $request, ThermalShock $thermalshock)
    {
        $request->validate([
            'target_thermal_shock_id' => 'required|exists:thermal_shock,id',
        ], [
            'target_thermal_shock_id.required' => 'Target Thermal Shock tujuan wajib dipilih.'
        ]);

        $targetId = $request->target_thermal_shock_id;

        // Ambil semua produk dari thermal shock asal
        $produks = $thermalshock->produks;

        if ($produks->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada produk di log ini yang bisa disalin.');
        }

        // Lakukan clone data menggunakan DB Transaction agar aman
        \DB::transaction(function () use ($produks, $targetId) {
            foreach ($produks as $produk) {
                // Replika data produk
                $newProduk = $produk->replicate();

                // Set ke ID Thermal Shock target tujuan
                $newProduk->thermal_shock_id = $targetId;

                // Reset parameter hasil test ke default awal
                $newProduk->hasil_test = 'Belum Tes';
                $newProduk->suhu_actual = null;
                $newProduk->keterangan = null;

                $newProduk->save();
            }
        });

        return redirect()->route('thermalshock.show', $targetId)
            ->with('message', 'Berhasil menyalin ' . $produks->count() . ' produk ke log Thermal Shock ini.');
    }

    public function destroy(ThermalShock $thermalshock)
    {
        $thermalshock->delete();
        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil dihapus.');
    }
}
