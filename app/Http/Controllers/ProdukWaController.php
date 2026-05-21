<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Spesifikasi;
use App\Models\WaterAbsorption;
use App\Models\ProdukWa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProdukWaController extends Controller
{
    public function index(Request $request, WaterAbsorption $waterabsorption)
    {
        $produkwa = ProdukWa::query()
            ->where('water_absorption_id', $waterabsorption->id)
            ->with(['customer', 'modelSize', 'spesifikasi'])
            ->when($request->search, function ($query, $search) {
                $query->where('sampel', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%");
                      });
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ProdukWa/Index', [
            'waterabsorption' => $waterabsorption,
            'produkwa' => $produkwa,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create(WaterAbsorption $waterabsorption)
    {
        $lastProduk = ProdukWa::where('water_absorption_id', $waterabsorption->id)
            ->latest()
            ->first();

        return Inertia::render('ProdukWa/Create', [
            'waterabsorption' => $waterabsorption,
            'lastProduk' => $lastProduk,
            'customers' => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes' => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis' => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
        ]);
    }

    public function store(Request $request, WaterAbsorption $waterabsorption)
    {
        $validated = $request->validate([
            'tgl_produksi'   => 'required|date',
            'customer_id'    => 'required|exists:customer,id',
            'modelsize_id'   => 'required|exists:modelsize,id',
            'spesifikasi_id' => 'required|exists:spesifikasi,id',
            'sampel'         => 'nullable|string|max:255',
            'temp'           => 'required|integer',
            'palm_wo'        => 'required|numeric',
            'palm_wa'        => 'required|numeric',
            'mc_wo'          => 'required|numeric',
            'mc_wa'          => 'required|numeric',
            'sl_wo'          => 'required|numeric',
            'sl_wa'          => 'required|numeric',
        ]);

        $validated['water_absorption_id'] = $waterabsorption->id;
        if(empty($validated['sampel'])) $validated['sampel'] = '-';

        // Hitung nilai _water di sisi Backend
        $validated['palm_water'] = $validated['palm_wa'] - $validated['palm_wo'];
        $validated['mc_water']   = $validated['mc_wa'] - $validated['mc_wo'];
        $validated['sl_water']   = $validated['sl_wa'] - $validated['sl_wo'];

        ProdukWa::create($validated);

        return redirect()->route('produkwa.index', $waterabsorption->id)
            ->with('message', 'Item produk water absorption berhasil ditambahkan.');
    }

    public function edit(WaterAbsorption $waterabsorption, ProdukWa $produkwa)
    {
        if ($produkwa->water_absorption_id !== $waterabsorption->id) {
            abort(404);
        }

        return Inertia::render('ProdukWa/Edit', [
            'waterabsorption' => $waterabsorption,
            'produkwa'        => $produkwa,
            'customers'       => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'      => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis'    => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
        ]);
    }

    public function update(Request $request, WaterAbsorption $waterabsorption, ProdukWa $produkwa)
    {
        if ($produkwa->water_absorption_id !== $waterabsorption->id) {
            abort(404);
        }

        $validated = $request->validate([
            'tgl_produksi'   => 'required|date',
            'customer_id'    => 'required|exists:customer,id',
            'modelsize_id'   => 'required|exists:modelsize,id',
            'spesifikasi_id' => 'required|exists:spesifikasi,id',
            'sampel'         => 'nullable|string|max:255',
            'temp'           => 'required|integer',
            'palm_wo'        => 'required|numeric',
            'palm_wa'        => 'required|numeric',
            'mc_wo'          => 'required|numeric',
            'mc_wa'          => 'required|numeric',
            'sl_wo'          => 'required|numeric',
            'sl_wa'          => 'required|numeric',
        ]);

        if(empty($validated['sampel'])) $validated['sampel'] = '-';

        // Hitung nilai _water di sisi Backend
        $validated['palm_water'] = $validated['palm_wa'] - $validated['palm_wo'];
        $validated['mc_water']   = $validated['mc_wa'] - $validated['mc_wo'];
        $validated['sl_water']   = $validated['sl_wa'] - $validated['sl_wo'];

        $produkwa->update($validated);

        return redirect()->route('produkwa.index', $waterabsorption->id)
            ->with('message', 'Data item produk water absorption berhasil diperbarui.');
    }

    public function destroy(WaterAbsorption $waterabsorption, ProdukWa $produkwa)
    {
        if ($produkwa->water_absorption_id !== $waterabsorption->id) {
            abort(404);
        }

        $produkwa->delete();
        return redirect()->route('produkwa.index', $waterabsorption->id)
            ->with('message', 'Item produk water absorption berhasil dihapus.');
    }

    // Ganti Pengerjaan Manual / Cepat khusus pengisian parameter Lab
    public function pengerjaan(WaterAbsorption $waterabsorption, ProdukWa $produkwa)
    {
        if ($produkwa->water_absorption_id !== $waterabsorption->id) {
            abort(404);
        }

        return Inertia::render('ProdukWa/Pengerjaan', [
            'waterabsorption' => $waterabsorption,
            'produkwa'        => $produkwa,
            'customers'       => Customer::select('id', 'customer')->get(),
            'modelsizes'      => ModelSize::select('id', 'customer_id', 'modelsize')->get(),
            'spesifikasis'    => Spesifikasi::select('id', 'spesifikasi')->get(),
        ]);
    }

    public function simpanPengerjaan(Request $request, WaterAbsorption $waterabsorption, ProdukWa $produkwa)
    {
        if ($produkwa->water_absorption_id !== $waterabsorption->id) {
            abort(404);
        }

        // 1. Validasi field laboratorium lab air
        $validated = $request->validate([
            'temp'    => 'required|integer',
            'palm_wo' => 'required|numeric',
            'palm_wa' => 'required|numeric',
            'mc_wo'   => 'required|numeric',
            'mc_wa'   => 'required|numeric',
            'sl_wo'   => 'required|numeric',
            'sl_wa'   => 'required|numeric',
        ]);

        // 2. Hitung nilai _water di backend secara presisi sebelum update
        $validated['palm_water'] = $validated['palm_wa'] - $validated['palm_wo'];
        $validated['mc_water']   = $validated['mc_wa'] - $validated['mc_wo'];
        $validated['sl_water']   = $validated['sl_wa'] - $validated['sl_wo'];

        // Update data sampel saat ini
        $produkwa->update($validated);

        // 3. Cari sampel berikutnya berdasarkan waktu input data (created_at) yang lebih baru
        $nextProduk = ProdukWa::where('water_absorption_id', $waterabsorption->id)
            ->where('created_at', '>', $produkwa->created_at)
            ->orderBy('created_at', 'asc')
            ->first();

        // Jikalau created_at milidetiknya sama atau kembar, kita pakai ID sebagai cadangan sorting
        if (!$nextProduk) {
            $nextProduk = ProdukWa::where('water_absorption_id', $waterabsorption->id)
                ->where('id', '>', $produkwa->id)
                ->orderBy('id', 'asc')
                ->first();
        }

        if ($nextProduk) {
            return redirect()->route('produkwa.pengerjaan', [$waterabsorption->id, $nextProduk->id])
                ->with('message', "Data sampel \"{$produkwa->sampel}\" berhasil disimpan. Lanjut ke sampel \"{$nextProduk->sampel}\".");
        } else {
            return redirect()->route('produkwa.index', $waterabsorption->id)
                ->with('message', 'Semua sampel pengujian air telah selesai dikerjakan.');
        }
    }
}
