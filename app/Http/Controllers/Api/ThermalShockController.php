<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ThermalShock;
use App\Http\Resources\ThermalShockResource;
use Illuminate\Http\Request;

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $thermalshocks = ThermalShock::query()
            ->when($search, function ($query, $search) {
                $query->where('hari_tgl', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return ThermalShockResource::collection($thermalshocks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
            $parent = ThermalShock::create($validated);

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
        return new ThermalShockResource($thermal_shock);
    }

    public function update(Request $request, ThermalShock $thermal_shock)
    {
        $validated = $request->validate([
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
            $thermal_shock->update($validated);

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
            $thermal_shock->delete();
            return response()->json(['message' => 'Data Thermal Shock berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
