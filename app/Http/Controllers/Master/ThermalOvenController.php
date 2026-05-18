<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ThermalOven;

class ThermalOvenController extends Controller
{
    public function index(Request $request)
    {
        $thermalovens = ThermalOven::query()
            ->when($request->search, function ($query, $search) {
                $query->where('thermal_oven', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/ThermalOven/Index', [
            'thermalovens' => $thermalovens,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/ThermalOven/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'thermal_oven' => 'required|string|max:255|unique:thermal_oven,thermal_oven',
        ], [
            'thermal_oven.required' => 'Thermal Oven wajib diisi.',
            'thermal_oven.unique' => 'Thermal Oven ini sudah terdaftar.',
        ]);

        ThermalOven::create($request->only('thermal_oven'));

        return redirect()->route('thermaloven.index')->with('message', 'Data thermal oven berhasil ditambahkan.');
    }

    public function edit(ThermalOven $thermaloven)
    {
        return Inertia::render('Master/ThermalOven/Edit', [
            'thermaloven' => $thermaloven
        ]);
    }

    public function update(Request $request, ThermalOven $thermaloven)
    {
        $request->validate([
            'thermal_oven' => 'required|string|max:255|unique:thermal_oven,thermal_oven,' . $thermaloven->id,
        ], [
            'thermal_oven.required' => 'Thermal Oven wajib diisi.',
            'thermal_oven.unique' => 'Thermal Oven ini sudah terdaftar.',
        ]);

        $thermaloven->update($request->only('thermal_oven'));

        return redirect()->route('thermaloven.index')->with('message', 'Data thermal oven berhasil diperbarui.');
    }

    public function destroy(ThermalOven $thermaloven)
    {
        $thermaloven->delete();
        return redirect()->route('thermaloven.index')->with('message', 'Data thermal oven berhasil dihapus.');
    }
}
