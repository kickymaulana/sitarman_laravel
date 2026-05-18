<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ThermalPintu;

class ThermalPintuController extends Controller
{
    public function index(Request $request)
    {
        $thermalpintus = ThermalPintu::query()
            ->when($request->search, function ($query, $search) {
                $query->where('thermal_pintu', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/ThermalPintu/Index', [
            'thermalpintus' => $thermalpintus,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/ThermalPintu/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'thermal_pintu' => 'required|string|max:255|unique:thermal_pintu,thermal_pintu',
        ], [
            'thermal_pintu.required' => 'Thermal Pintu wajib diisi.',
            'thermal_pintu.unique' => 'Thermal Pintu ini sudah terdaftar.',
        ]);

        ThermalPintu::create($request->only('thermal_pintu'));

        return redirect()->route('thermalpintu.index')->with('message', 'Data thermal pintu berhasil ditambahkan.');
    }

    public function edit(ThermalPintu $thermalpintu)
    {
        return Inertia::render('Master/ThermalPintu/Edit', [
            'thermalpintu' => $thermalpintu
        ]);
    }

    public function update(Request $request, ThermalPintu $thermalpintu)
    {
        $request->validate([
            'thermal_pintu' => 'required|string|max:255|unique:thermal_pintu,thermal_pintu,' . $thermalpintu->id,
        ], [
            'thermal_pintu.required' => 'Thermal Pintu wajib diisi.',
            'thermal_pintu.unique' => 'Thermal Pintu ini sudah terdaftar.',
        ]);

        $thermalpintu->update($request->only('thermal_pintu'));

        return redirect()->route('thermalpintu.index')->with('message', 'Data thermal pintu berhasil diperbarui.');
    }

    public function destroy(ThermalPintu $thermalpintu)
    {
        $thermalpintu->delete();
        return redirect()->route('thermalpintu.index')->with('message', 'Data thermal pintu berhasil dihapus.');
    }
}
