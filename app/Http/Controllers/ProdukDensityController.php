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
use App\Models\DensityWaterAbsorption; // Import master model baru
use App\Models\ProdukDwa;
use App\Models\Spesifikasi;

class ProdukDensityController extends Controller
{
    public function index(Request $request, DensityWaterAbsorption $densityWaterAbsorption)
    {
        $produkdensity = ProdukDwa::query()
            ->where('density_water_absorption_id', $densityWaterAbsorption->id)
            ->with(['oven', 'jamKeluarOven','customer', 'modelSize', 'spesifikasi']) // Memuat relasi model baru
            ->when($request->search, function ($query, $search) {
                $query->whereHas('customer', function($q) use ($search) {
                    $q->where('customer', 'like', "%{$search}%");
                });
            })
            ->orderBy('no', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ProdukDensity/Index', [
            // Mengirim data induk menggunakan key lama 'density'
            // agar meminimalkan perubahan properti di sisi Vue component
            'density' => $densityWaterAbsorption,
            'produkdensity' => $produkdensity,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create(DensityWaterAbsorption $densityWaterAbsorption)
    {
        // Mengambil record terakhir untuk memicu auto-fill di form input berikutnya
        $lastProduk = ProdukDwa::where('density_water_absorption_id', $densityWaterAbsorption->id)
            ->latest()
            ->first();

        return Inertia::render('ProdukDensity/Create', [
            'density'        => $densityWaterAbsorption,
            'lastProduk'     => $lastProduk,
            'customers'      => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis'   => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function store(Request $request, DensityWaterAbsorption $densityWaterAbsorption)
    {
        $validated = $request->validate([
            'no'                 => 'required|numeric',
            'tgl_produksi'       => 'required|date',
            'sample'             => 'nullable|string',
            'customer_id'        => 'required|exists:customer,id',
            'modelsize_id'       => 'required|exists:modelsize,id',
            'spesifikasi_id'     => 'required|exists:spesifikasi,id', // Validasi spesifikasi baru
            'oven_id'            => 'required|exists:oven,id',
            'tanggal_keluar_oven'=> 'required|date',
            'jam_keluar_oven_id' => 'required|exists:jam_keluar_oven,id',
            'ketebalan'          => 'required|numeric',
            'berat_awal'         => 'required|numeric',
            'berat_akhir'        => 'required|numeric',
            'volume'             => 'required|numeric',
            'density'            => 'required|numeric', // Menerima hasil hitungan Create.vue
        ]);

        // Pasangkan ke ID Master DWA
        $validated['density_water_absorption_id'] = $densityWaterAbsorption->id;

        ProdukDwa::create($validated);

        return redirect()->route('produkdensity.index', $densityWaterAbsorption->id)
            ->with('message', 'Item produk density berhasil ditambahkan.');
    }

    public function edit(DensityWaterAbsorption $densityWaterAbsorption, ProdukDwa $produkDwa)
    {
        // Proteksi pengaman data: pastikan item ini benar milik data induknya
        if ($produkDwa->density_water_absorption_id !== $densityWaterAbsorption->id) {
            abort(404);
        }

        return Inertia::render('ProdukDensity/Edit', [
            'density'        => $densityWaterAbsorption,
            'produkdensity'  => $produkDwa,
            'customers'      => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis'   => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function update(Request $request, DensityWaterAbsorption $densityWaterAbsorption, ProdukDwa $produkDwa)
    {
        $validated = $request->validate([
            'no'                 => 'required|numeric',
            'tgl_produksi'       => 'required|date',
            'sample'             => 'nullable|string',
            'customer_id'        => 'required|exists:customer,id',
            'modelsize_id'       => 'required|exists:modelsize,id',
            'spesifikasi_id'     => 'required|exists:spesifikasi,id', // Tambahan validasi baru
            'oven_id'            => 'required|exists:oven,id',
            'tanggal_keluar_oven'=> 'required|date',
            'jam_keluar_oven_id' => 'required|exists:jam_keluar_oven,id',
            'ketebalan'          => 'required|numeric',
            'berat_awal'         => 'required|numeric',
            'berat_akhir'        => 'required|numeric',
            'volume'             => 'required|numeric',
            'density'            => 'required|numeric',
        ]);

        $produkDwa->update($validated);

        return redirect()->route('produkdensity.index', $densityWaterAbsorption->id)
            ->with('message', 'Item produk density berhasil diperbarui.');
    }

    public function destroy(DensityWaterAbsorption $densityWaterAbsorption, ProdukDwa $produkDwa)
    {
        if ($produkDwa->density_water_absorption_id !== $densityWaterAbsorption->id) {
            abort(404);
        }

        $produkDwa->delete();

        return redirect()->route('produkdensity.index', $densityWaterAbsorption->id)
            ->with('message', 'Item produk density berhasil dihapus.');
    }
}
