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
            ->orderBy('posisi_former', 'asc')
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
        // Ambil data produk terakhir yang diinput untuk thermal_shock ini
        $lastProduk = Produk::where('thermal_shock_id', $thermalshock->id)
            ->latest()
            ->first();

        return Inertia::render('Produk/Create', [
            'thermalshock' => $thermalshock,
            'lastProduk' => $lastProduk, // Kirim data terakhir ke frontend
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
            'suhu_actual'         => 'nullable|integer',
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
            'suhu_actual'         => 'nullable|integer',
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


    public function pengerjaan(ThermalShock $thermalshock, Produk $produk)
    {
        return Inertia::render('Produk/Pengerjaan', [
            'thermalshock' => $thermalshock,
            'produk' => $produk,
            'ovens' => Oven::select('id', 'oven')->get(),
            'customers' => Customer::select('id', 'customer')->get(),
            'modelsizes' => ModelSize::select('id', 'customer_id', 'modelsize')->get(),
            'spesifikasis' => Spesifikasi::select('id', 'spesifikasi')->get(),
        ]);
    }

    public function simpanPengerjaan(Request $request, ThermalShock $thermalshock, Produk $produk)
    {
        // 1. Validasi hanya 3 field yang boleh diisi
        $validated = $request->validate([
            'suhu_actual' => 'nullable|integer',
            'hasil_test'  => 'required|in:OK,NG',
            'keterangan'  => 'nullable|string',
        ]);

        // 2. Update data produk saat ini
        $produk->update($validated);

        // 3. Cari apakah ada produk dengan posisi_former berikutnya (misal saat ini posisi 1, cari posisi 2)
        //    di dalam batch Thermal Shock yang sama
        $nextProduk = Produk::where('thermal_shock_id', $thermalshock->id)
            ->where('posisi_former', '>', $produk->posisi_former)
            ->orderBy('posisi_former', 'asc')
            ->first();

        if ($nextProduk) {
            // Jika posisi berikutnya ada (misal posisi 2), redirect ke halaman pengerjaan posisi 2
            return redirect()->route('produk.pengerjaan', [$thermalshock->id, $nextProduk->id])
                ->with('message', "Posisi {$produk->posisi_former} disimpan. Lanjut ke posisi {$nextProduk->posisi_former}.");
        } else {
            // Jika sudah sampai posisi terakhir (misal posisi 10), kembali ke index
            return redirect()->route('produk.index', $thermalshock->id)
                ->with('message', 'Semua posisi produk telah selesai dikerjakan.');
        }
    }
}

