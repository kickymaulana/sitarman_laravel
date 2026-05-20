<?php

// app/Http/Controllers/ProdukController.php
namespace App\Http\Controllers;

use App\Models\Oven;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Spesifikasi;
use App\Models\ThermalShock;
use App\Models\TinggiFormer;
use App\Models\JamKeluarOven;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProdukController extends Controller
{
    public function index(Request $request, ThermalShock $thermalshock)
    {
        $produk = Produk::query()
            ->where('thermal_shock_id', $thermalshock->id)
            ->with(['oven', 'customer', 'modelSize', 'spesifikasi', 'jamKeluarOven'])
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
            // --- TAMBAHAN MASTER DATA BARU ---
            'tinggiformers' => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function store(Request $request, ThermalShock $thermalshock)
    {
        $validated = $request->validate([
            'kode_bakar'          => 'nullable|integer',
            'kode_tanah'          => 'nullable|string|max:255',
            'oven_id'             => 'required|exists:oven,id',
            'customer_id'         => 'required|exists:customer,id',
            'modelsize_id'        => 'required|exists:modelsize,id',
            'spesifikasi_id'      => 'required|exists:spesifikasi,id',
            'tinggi_former_id'    => 'required|exists:tinggi_former,id',
            'jam_keluar_oven_id'   => 'required|exists:jam_keluar_oven,id',
            'sampel'              => 'nullable|string|max:255',
            'berat_former'        => 'required|integer',
            'tanggal_keluar_oven' => 'required|date',
            'tgl_produksi'        => 'required|date',
            // 'posisi_former'    => 'required|integer', // 👈 Hapus atau komentari dari validasi request
            'hasil_test'          => 'required|in:OK,NG,Belum Tes',
            'suhu_actual'         => 'nullable|integer',
            'keterangan'          => 'nullable|string',
        ]);

        // Hubungkan ke ID Thermal Shock induk
        $validated['thermal_shock_id'] = $thermalshock->id;

        // --- PROSES AUTO-INCREMENT POSISI FORMER ---
        // Cari nilai posisi_former tertinggi pada thermal_shock_id ini
        $maxPosisi = Produk::where('thermal_shock_id', $thermalshock->id)->max('posisi_former');

        // Jika belum ada data (null), mulai dari 1. Jika sudah ada, tambah 1.
        $validated['posisi_former'] = $maxPosisi ? $maxPosisi + 1 : 1;
        // --------------------------------------------

        if(empty($validated['sampel'])) $validated['sampel'] = '-';
        if(empty($validated['kode_bakar'])) $validated['kode_bakar'] = 0;

        Produk::create($validated);

        return redirect()->route('produk.index', $thermalshock->id)
            ->with('message', 'Data Produk Thermal Shock berhasil ditambahkan dengan Posisi ' . $validated['posisi_former'] . '.');
    }

    public function edit(ThermalShock $thermalshock, Produk $produk)
    {
        if ($produk->thermal_shock_id !== $thermalshock->id) {
            abort(404);
        }

        return Inertia::render('Produk/Edit', [
            'thermalshock' => $thermalshock,
            'produk'       => $produk, // Kirim data produk yang akan diedit
            'ovens'        => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'customers'    => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'   => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis' => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
            'tinggiformers' => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function update(Request $request, ThermalShock $thermalshock, Produk $produk)
    {
        if ($produk->thermal_shock_id !== $thermalshock->id) {
            abort(404);
        }

        $validated = $request->validate([
            'kode_bakar'          => 'nullable|integer',
            'kode_tanah'          => 'nullable|string|max:255',
            'oven_id'             => 'required|exists:oven,id',
            'customer_id'         => 'required|exists:customer,id',
            'modelsize_id'        => 'required|exists:modelsize,id',
            'spesifikasi_id'      => 'required|exists:spesifikasi,id',
            'tinggi_former_id'    => 'required|exists:tinggi_former,id',
            'jam_keluar_oven_id'   => 'required|exists:jam_keluar_oven,id',
            'sampel'              => 'nullable|string|max:255',
            'berat_former'        => 'required|integer',
            'tanggal_keluar_oven' => 'required|date',
            'tgl_produksi'        => 'required|date',
            'posisi_former'       => 'required|integer',
            'hasil_test'          => 'required|in:OK,NG,Belum Tes',
            'suhu_actual'         => 'nullable|integer',
            'keterangan'          => 'nullable|string',
        ]);

        if(empty($validated['sampel'])) $validated['sampel'] = '-';

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
        // Pastikan produk milik thermal_shock yang benar
        if ($produk->thermal_shock_id !== $thermalshock->id) {
            abort(404);
        }

        return Inertia::render('Produk/Pengerjaan', [
            'thermalshock'   => $thermalshock,
            'produk'         => $produk,
            'ovens'          => Oven::select('id', 'oven')->get(),
            'customers'      => Customer::select('id', 'customer')->get(),
            'modelsizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->get(),
            'spesifikasis'   => Spesifikasi::select('id', 'spesifikasi')->get(),
            // --- TAMBAHAN DATA AGAR TAMPIL DI READ-ONLY INFO ---
            'tinggiformers'  => TinggiFormer::select('id', 'tinggi_former')->get(),
            'jamkeluarovens' => JamKeluarOven::select('id', 'jam_keluar_oven')->get(),
        ]);
    }

    public function simpanPengerjaan(Request $request, ThermalShock $thermalshock, Produk $produk)
    {
        if ($produk->thermal_shock_id !== $thermalshock->id) {
            abort(404);
        }

        // 1. Validasi field pengerjaan
        $validated = $request->validate([
            'suhu_actual' => 'nullable|integer',
            'hasil_test'  => 'required|in:OK,NG,Belum Tes',
            'keterangan'  => 'nullable|string',
        ]);

        // 2. Update data produk saat ini
        $produk->update($validated);

        // 3. Cari apakah ada produk dengan posisi_former berikutnya di batch yang sama
        $nextProduk = Produk::where('thermal_shock_id', $thermalshock->id)
            ->where('posisi_former', '>', $produk->posisi_former)
            ->orderBy('posisi_former', 'asc')
            ->first();

        if ($nextProduk) {
            return redirect()->route('produk.pengerjaan', [$thermalshock->id, $nextProduk->id])
                ->with('message', "Posisi {$produk->posisi_former} disimpan. Lanjut ke posisi {$nextProduk->posisi_former}.");
        } else {
            return redirect()->route('produk.index', $thermalshock->id)
                ->with('message', 'Semua posisi produk telah selesai dikerjakan.');
        }
    }

}

