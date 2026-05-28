<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chemical;
use App\Models\ProdukChemical;
use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Oven;
use App\Models\JamKeluarOven;

class ProdukChemicalController extends Controller
{
    public function index(Request $request, Chemical $chemical)
    {
        $produkchemical = ProdukChemical::query()
            ->where('chemical_id', $chemical->id)
            ->with(['oven', 'jamKeluarOven', 'customer', 'modelSize'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('customer', 'like', "%{$search}%");
                })->orWhere('sample', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ProdukChemical/Index', [
            'chemical'       => $chemical,
            'produkchemical' => $produkchemical,
            'filters'        => $request->only(['search'])
        ]);
    }

    public function create(Chemical $chemical)
    {
        $lastProduk = ProdukChemical::where('chemical_id', $chemical->id)->latest()->first();

        return Inertia::render('ProdukChemical/Create', [
            'chemical'       => $chemical,
            'lastProduk'     => $lastProduk,
            'customers'      => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function store(Request $request, Chemical $chemical)
    {
        $validated = $request->validate([
            'tgl_produksi'        => 'required|date',
            'customer_id'         => 'required|exists:customer,id',
            'modelsize_id'        => 'required|exists:modelsize,id',
            'oven_id'             => 'required|exists:oven,id',
            'tanggal_keluar_oven' => 'required|date',
            'jam_keluar_oven_id'  => 'required|exists:jam_keluar_oven,id',
            'sample'              => 'nullable|string|max:255',

            // Input User
            'ketebalan_mm'        => 'required|numeric|min:0',
            'berat_awal'          => 'required|numeric|min:0',
            'berat_akhir'         => 'required|numeric|min:0',
            'density'             => 'required|numeric|min:0',

            // Hasil Kalkulasi yang dikirim dari Front-End
            'berat_hilang'        => 'required|numeric',
            'metode_biasa'        => 'required|numeric',
            'volume'              => 'required|numeric',
            'ketebalan_dm'        => 'required|numeric',
            'luas_permukaan'      => 'required|numeric',
            'hasil_akhir'         => 'required|numeric',
        ]);

        if (empty($validated['sample'])) {
            $validated['sample'] = '-';
        }

        $validated['chemical_id'] = $chemical->id;

        // Langsung buat data tanpa perlu hitung ulang di sisi server
        ProdukChemical::create($validated);

        return redirect()->route('produkchemical.index', $chemical->id)
            ->with('message', 'Item produk chemical berhasil ditambahkan.');
    }

    public function edit(Chemical $chemical, ProdukChemical $produkchemical)
    {
        if ($produkchemical->chemical_id !== $chemical->id) {
            abort(404);
        }

        return Inertia::render('ProdukChemical/Edit', [
            'chemical'       => $chemical,
            'produkchemical' => $produkchemical,
            'customers'      => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function update(Request $request, Chemical $chemical, ProdukChemical $produkchemical)
    {
        if ($produkchemical->chemical_id !== $chemical->id) {
            abort(404);
        }

        $validated = $request->validate([
            'tgl_produksi'        => 'required|date',
            'customer_id'         => 'required|exists:customer,id',
            'modelsize_id'        => 'required|exists:modelsize,id',
            'oven_id'             => 'required|exists:oven,id',
            'tanggal_keluar_oven' => 'required|date',
            'jam_keluar_oven_id'  => 'required|exists:jam_keluar_oven,id',
            'sample'              => 'nullable|string|max:255',

            'ketebalan_mm'        => 'required|numeric|min:0',
            'berat_awal'          => 'required|numeric|min:0',
            'berat_akhir'         => 'required|numeric|min:0',
            'density'             => 'required|numeric|min:0',

            'berat_hilang'        => 'required|numeric',
            'metode_biasa'        => 'required|numeric',
            'volume'              => 'required|numeric',
            'ketebalan_dm'        => 'required|numeric',
            'luas_permukaan'      => 'required|numeric',
            'hasil_akhir'         => 'required|numeric',
        ]);

        if (empty($validated['sample'])) {
            $validated['sample'] = '-';
        }

        $produkchemical->update($validated);

        return redirect()->route('produkchemical.index', $chemical->id)
            ->with('message', 'Item produk chemical berhasil diperbarui.');
    }

    public function destroy(Chemical $chemical, ProdukChemical $produkchemical)
    {
        if ($produkchemical->chemical_id !== $chemical->id) {
            abort(404);
        }

        $produkchemical->delete();

        return redirect()->route('produkchemical.index', $chemical->id)
            ->with('message', 'Item produk chemical berhasil dihapus.');
    }
}
