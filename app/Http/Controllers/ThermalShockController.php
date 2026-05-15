<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ThermalShock;
use App\Models\Customer;
use App\Models\ModelSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $thermalshocks = ThermalShock::query()
            ->when($request->search, function ($query, $search) {
                $query->where('hari_tgl', 'like', "%{$search}%");
            })
            ->withCount('details') // Menghitung jumlah item dalam satu pengetesan
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
            'customers' => Customer::select('id', 'customer')->get(),
            'modelsizes' => ModelSize::select('id', 'modelsize')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Validasi Header dan Detail sekaligus
        $validated = $request->validate([
            // Header
            'hari_tgl' => 'required|date',
            'suhu_testing' => 'required|integer',
            'suhu_motor' => 'nullable|string',
            'suhu_display' => 'required|integer',
            'suhu_actual' => 'required|integer',
            'jam_awal_proses' => 'required',
            'jam_capai_suhu' => 'required',
            'suhu_awal' => 'required|integer',
            'suhu_air' => 'required|string',
            'jam_mulai_tembak' => 'required',
            'jam_selesai_tembak' => 'required',

            // Detail (Array)
            'items' => 'required|array|min:1',
            'items.*.customer_id' => 'required|exists:customer,id',
            'items.*.modelsize_id' => 'required|exists:modelsize,id',
            'items.*.oven_kode_tanah' => 'required|string',
            'items.*.spesifikasi' => 'required|string',
            'items.*.berat_former' => 'required|integer',
            'items.*.tanggal_keluar_oven' => 'required|date',
            'items.*.tgl_produksi' => 'required|date',
            'items.*.posisi_former' => 'required|integer',
            'items.*.hasil_test' => 'required|in:OK,NG',
            'items.*.keterangan' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Simpan ke tabel thermal_shock (Parent)
                $parent = ThermalShock::create([
                    'hari_tgl' => $validated['hari_tgl'],
                    'suhu_testing' => $validated['suhu_testing'],
                    'suhu_motor' => $validated['suhu_motor'],
                    'suhu_display' => $validated['suhu_display'],
                    'suhu_actual' => $validated['suhu_actual'],
                    'jam_awal_proses' => $validated['jam_awal_proses'],
                    'jam_capai_suhu' => $validated['jam_capai_suhu'],
                    'suhu_awal' => $validated['suhu_awal'],
                    'suhu_air' => $validated['suhu_air'],
                    'jam_mulai_tembak' => $validated['jam_mulai_tembak'],
                    'jam_selesai_tembak' => $validated['jam_selesai_tembak'],
                ]);

                // Simpan semua item ke tabel thermal_shock_detail (Child)
                $parent->details()->createMany($validated['items']);
            });

            return redirect()->route('thermalshock.index')->with('message', 'Laporan Thermal Shock berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function show(ThermalShock $thermalshock)
    {
        // Load relasi agar nama customer dan modelsize muncul, bukan cuma ID nya
        $thermalshock->load(['details.customer', 'details.modelSize']);

        return Inertia::render('ThermalShock/Show', [
            'thermalshock' => $thermalshock
        ]);
    }
}
