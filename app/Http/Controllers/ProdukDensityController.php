<?php

namespace App\Http\Controllers;

use App\Models\Density;
use App\Models\ProdukDensity;
use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Oven;
use App\Models\JamKeluarOven;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProdukDensityController extends Controller
{
    public function index(Request $request, Density $density)
    {
        $produkdensity = ProdukDensity::query()
            ->where('density_id', $density->id)
            ->with(['customer', 'modelSize', 'oven', 'jamKeluarOven'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('customer', function($q) use ($search) {
                    $q->where('customer', 'like', "%{$search}%");
                });
            })
            ->orderBy('no', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ProdukDensity/Index', [
            'density' => $density,
            'produkdensity' => $produkdensity,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create(Density $density)
    {
        $lastProduk = ProdukDensity::where('density_id', $density->id)
            ->latest()
            ->first();

        return Inertia::render('ProdukDensity/Create', [
            'density' => $density,
            'lastProduk' => $lastProduk,
            'customers' => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes' => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'ovens' => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }


    public function store(Request $request, Density $density)
    {
        $validated = $request->validate([
            'no'                 => 'required|numeric',
            'tgl_produksi'       => 'required|date',
            'customer_id'        => 'required|exists:customer,id',
            'modelsize_id'       => 'required|exists:modelsize,id',
            'oven_id'            => 'required|exists:oven,id',
            'jam_keluar_oven_id' => 'required|exists:jam_keluar_oven,id',
            'ketebalan'          => 'required|numeric',
            'berat_awal'         => 'required|numeric',
            'berat_akhir'        => 'required|numeric',
            'volume'             => 'required|numeric',
            'density'            => 'required|numeric', // Menerima langsung hasil hitungan Create.vue
        ]);

        $validated['density_id'] = $density->id;

        // Langsung simpan ke database tanpa menimpa nilainya lagi
        ProdukDensity::create($validated);

        return redirect()->route('produkdensity.index', $density->id)
            ->with('message', 'Item produk density berhasil ditambahkan.');
    }

    public function edit(Density $density, ProdukDensity $produkdensity)
    {
        if ($produkdensity->density_id !== $density->id) {
            abort(404);
        }

        return Inertia::render('ProdukDensity/Edit', [
            'density'        => $density,
            'produkdensity'  => $produkdensity,
            'customers'      => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function update(Request $request, Density $density, ProdukDensity $produkdensity)
    {
        $validated = $request->validate([
            'no'                 => 'required|numeric',
            'tgl_produksi'       => 'required|date',
            'customer_id'        => 'required|exists:customer,id',
            'modelsize_id'       => 'required|exists:modelsize,id',
            'oven_id'            => 'required|exists:oven,id',
            'jam_keluar_oven_id' => 'required|exists:jam_keluar_oven,id',
            'ketebalan'          => 'required|numeric',
            'berat_awal'         => 'required|numeric',
            'berat_akhir'        => 'required|numeric',
            'volume'             => 'required|numeric',
            'density'            => 'required|numeric',
        ]);

        $produkdensity->update($validated);

        return redirect()->route('produkdensity.index', $density->id)
            ->with('message', 'Item produk density berhasil diperbarui.');
    }

    public function destroy(Density $density, ProdukDensity $produkdensity)
    {
        if ($produkdensity->density_id !== $density->id) {
            abort(404);
        }

        $produkdensity->delete();
        return redirect()->route('produkdensity.index', $density->id)
            ->with('message', 'Item produk density berhasil dihapus.');
    }
}
