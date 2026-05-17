<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThermalShock;
use App\Models\Customer;
use App\Models\ModelSize;
use App\Http\Resources\ThermalShockResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $thermalshocks = ThermalShock::query()
            ->when($search, function ($query, $search) {
                $query->where('hari_tgl', 'like', "%{$search}%");
            })
            ->withCount('details') // Menghitung jumlah item dalam satu pengetesan
            ->latest()
            ->paginate(10);

        return ThermalShockResource::collection($thermalshocks);
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
        ]);

        try {
            $parent = null;
            DB::transaction(function () use ($validated, &$parent) {
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

            });

            // Load relasi agar respons lebih lengkap
            $parent->load('details.customer', 'details.modelSize');

            return response()->json([
                'message' => 'Laporan Thermal Shock berhasil disimpan.',
                'data' => new ThermalShockResource($parent)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(ThermalShock $thermal_shock)
    {
        // Load relasi agar nama customer dan modelsize muncul
        $thermal_shock->load(['details.customer', 'details.modelSize']);

        return new ThermalShockResource($thermal_shock);
    }

    public function update(Request $request, ThermalShock $thermal_shock)
    {
        // 1. Validasi Data
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
        ]);

        try {
            DB::transaction(function () use ($validated, $thermal_shock) {
                // Update data Parent (Header)
                $thermal_shock->update([
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

            });

            // Load relasi terbaru
            $thermal_shock->load('details.customer', 'details.modelSize');

            return response()->json([
                'message' => 'Laporan Thermal Shock berhasil diperbarui.',
                'data' => new ThermalShockResource($thermal_shock)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(ThermalShock $thermal_shock)
    {
        try {
            // Karena relasi biasanya di set cascade, atau kita hapus manual detailsnya dulu
            DB::transaction(function () use ($thermal_shock) {
                $thermal_shock->details()->delete();
                $thermal_shock->delete();
            });

            return response()->json(['message' => 'Data Thermal Shock berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function references()
    {
        return response()->json([
            'customers' => Customer::select('id', 'customer')->get(),
            'modelsizes' => ModelSize::select('id', 'modelsize')->get(),
        ]);
    }

    // --- ENDPOINT BARU UNTUK UX MASTER-DETAIL ---

    // Menambah 1 Detail ke Header yang sudah ada
    public function storeDetail(Request $request, ThermalShock $thermal_shock)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'modelsize_id' => 'required|exists:modelsize,id',
            'oven_kode_tanah' => 'required|string',
            'spesifikasi' => 'required|string',
            'berat_former' => 'required|integer',
            'tanggal_keluar_oven' => 'required|date',
            'tgl_produksi' => 'required|date',
            'posisi_former' => 'required|integer',
            'hasil_test' => 'required|in:OK,NG',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $detail = $thermal_shock->details()->create($validated);
            $detail->load(['customer', 'modelSize']);

            return response()->json([
                'message' => 'Detail berhasil ditambahkan.',
                'data' => $detail
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan detail.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Menghapus 1 Detail dari Header
    public function destroyDetail(ThermalShock $thermal_shock, $detailId)
    {
        try {
            $detail = $thermal_shock->details()->findOrFail($detailId);
            $detail->delete();

            return response()->json(['message' => 'Detail berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus detail.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
