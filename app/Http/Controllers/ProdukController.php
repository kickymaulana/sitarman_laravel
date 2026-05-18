<?php

// app/Http/Controllers/ProdukController.php
namespace App\Http\Controllers;

use App\Models\Oven;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Spesifikasi;
use App\Models\ThermalShock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProdukController extends Controller
{
    public function index(Request $request, ThermalShock $thermalshock)
    {
        $produk = Produk::query()
            ->where('thermal_shock_id', $thermalshock->id)
            ->with(['oven', 'customer', 'modelSize', 'spesifikasi'])
            ->when($request->search, function ($query, $search) {
                $query->where('kode_tanah', 'like', "%{$search}%")
                      ->orWhere('sampel', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Produk/Index', [
            'thermalshock' => $thermalshock,
            'produk' => $produk,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create(ThermalShock $thermalshock)
    {
        return Inertia::render('Produk/Create', [
            'thermalshock' => $thermalshock,
            'ovens' => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'customers' => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes' => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis' => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
        ]);
    }

    public function store(Request $request, ThermalShock $thermalshock)
    {
        $validated = $request->validate([
            'kode_tanah'          => 'required|string|max:255',
            'oven_id'             => 'required|exists:oven,id',
            'customer_id'         => 'required|exists:customer,id',
            'modelsize_id'        => 'required|exists:modelsize,id',
            'spesifikasi_id'      => 'required|exists:spesifikasi,id',
            'sampel'              => 'nullable|string|max:255',
            'berat_former'        => 'required|integer',
            'tanggal_keluar_oven' => 'required|date',
            'tgl_produksi'        => 'required|date',
            'posisi_former'       => 'required|integer',
            'hasil_test'          => 'required|in:OK,NG',
            'suhu_actual'         => 'required|integer',
            'keterangan'          => 'nullable|string',
        ]);

        $validated['thermal_shock_id'] = $thermalshock->id;
        if(empty($validated['sampel'])) $validated['sampel'] = '-';

        Produk::create($validated);

        return redirect()->route('produk.index', $thermalshock->id)
            ->with('message', 'Data Produk Thermal Shock berhasil ditambahkan.');
    }

    public function edit(ThermalShock $thermalshock, Produk $produk)
    {
        return Inertia::render('Produk/Edit', [
            'thermalshock' => $thermalshock,
            'produk' => $produk,
            'ovens' => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'customers' => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes' => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis' => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
        ]);
    }

    public function update(Request $request, ThermalShock $thermalshock, Produk $produk)
    {
        $validated = $request->validate([
            'kode_tanah'          => 'required|string|max:255',
            'oven_id'             => 'required|exists:oven,id',
            'customer_id'         => 'required|exists:customer,id',
            'modelsize_id'        => 'required|exists:modelsize,id',
            'spesifikasi_id'      => 'required|exists:spesifikasi,id',
            'sampel'              => 'nullable|string|max:255',
            'berat_former'        => 'required|integer',
            'tanggal_keluar_oven' => 'required|date',
            'tgl_produksi'        => 'required|date',
            'posisi_former'       => 'required|integer',
            'hasil_test'          => 'required|in:OK,NG',
            'suhu_actual'         => 'required|integer',
            'keterangan'          => 'nullable|string',
        ]);

        $produk->update($validated);

        return redirect()->route('produk.index', $thermalshock->id)
            ->with('message', 'Data Produk Thermal Shock berhasil diperbarui.');
    }

    public function destroy(ThermalShock $thermalshock, Produk $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index', $thermalshock->id)
            ->with('message', 'Data Produk Thermal Shock berhasil dihapus.');
    }
}

