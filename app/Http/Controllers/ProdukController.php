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
use App\Models\HasilThermalShock;

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
            ->paginate(15)
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

    public function masukan_ke_hasil_thermal_shock(ThermalShock $thermalshock)
    {
        // 1. Ambil semua data produk yang aktif di dalam sesi thermal shock ini
        $allProduk = Produk::where('thermal_shock_id', $thermalshock->id)->get();

        // Proteksi jika ternyata user menekan tombol saat data produk masih kosong
        if ($allProduk->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal memproses. Tidak ada data produk pada batch ini.');
        }

        // 2. Susun wadah array kosong untuk bulk insert
        $dataRekap = [];

        foreach ($allProduk as $item) {
            // Set nilai default sesuai dengan blueprint enum migration Anda
            $suhu180 = 'Belum Tes';
            $suhu200 = 'Belum Tes';

            // Ambil suhu testing dari relasi thermalShock
            $suhuTesting = $item->thermalShock->suhu_testing ?? null;

            // Tentukan kolom mana yang harus diisi berdasarkan suhu_testing
            if ($suhuTesting == '180') {
                $suhu180 = $item->hasil_test;
            } elseif ($suhuTesting == '200') {
                $suhu200 = $item->hasil_test;
            }

            $dataRekap[] = [
                // Pemetaan field identitas yang disalin langsung dari tabel produk
                'tanggal_keluar_oven' => $item->tanggal_keluar_oven,
                'oven_id'             => $item->oven_id,
                'jam_keluar_oven_id'  => $item->jam_keluar_oven_id,
                'customer_id'         => $item->customer_id,
                'modelsize_id'        => $item->modelsize_id,
                'tinggi_former_id'    => $item->tinggi_former_id,
                'spesifikasi_id'      => $item->spesifikasi_id,
                'kode_tanah'          => $item->kode_tanah,
                'berat_former'        => $item->berat_former,

                // Logika Mapping Khusus Hasil Uji:
                'suhu'                => $item->suhu_actual ?? 0,

                // Sekarang kolom ini dinamis dan tidak akan terisi dua-duanya sekaligus
                'suhu_180'            => $suhu180,
                'suhu_200'            => $suhu200,

                // Nilai fisik & parameter lab di-set default aman (nullable)
                'ketebalan'           => 0.00,
                'metode_biasa'        => 0.00,
                'wa_palm'             => 0.000,
                'wa_mc'               => 0.000,
                'wa_sli'              => 0.000,
                'density'             => 0.00,
                'hasil_akhir'           => 0.00,
                'visual'              => 0,

                // Timestamp wajib diisi manual jika menggunakan raw bulk insert
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }

        // 3. Eksekusi penyimpanan massal ke database dalam satu query tunggal
        HasilThermalShock::insert($dataRekap);

        return redirect()->route('hasilthermalshock.index')
            ->with('message', count($dataRekap) . ' data produk berhasil dipindahkan massal ke Hasil Thermal Shock.');
    }

    public function exportExcel(ThermalShock $thermalshock)
    {
        // 1. Matikan pembatasan waktu eksekusi jika datanya banyak
        set_time_limit(0);

        // Penamaan file dinamis
        $fileName = 'produk_thermal_shock_' . $thermalshock->hari_tgl . '_' . now()->format('His') . '.csv';

        // 2. Gunakan relasi dengan nama camelCase yang tepat sesuai definisi di Model ThermalShock
        // Di Model kamu: public function produks()
        $results = \App\Models\Produk::query()
            ->where('thermal_shock_id', $thermalshock->id)
            ->with(['oven', 'customer', 'modelSize', 'spesifikasi', 'tinggiFormer', 'jamKeluarOven'])
            ->orderBy('posisi_former', 'asc')
            ->get();

        // Header HTTP
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'No / Posisi Former',
            'Tanggal Keluar Oven',
            'Jam Keluar Oven',
            'Oven',
            'Customer',
            'Model Size',
            'Tinggi Former',
            'Spesifikasi',
            'Kode Tanah',
            'Kode Bakar',
            'Sampel',
            'Berat Former (g)',
            'Tanggal Produksi',
            'Suhu Aktual (°C)',
            'Hasil Test',
            'Keterangan'
        ];

        $callback = function() use($results, $columns) {
            // 🔥 KUNCI UTAMA: Bersihkan semua buffer output yang menggantung di Laravel
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            $file = fopen('php://output', 'w');

            // Tambahkan BOM agar Excel tidak berantakan membaca karakter khusus
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Tulis header kolom
            fputcsv($file, $columns, ';');

            // Tulis baris data
            foreach ($results as $row) {
                fputcsv($file, [
                    $row->posisi_former,
                    $row->tanggal_keluar_oven ? \Carbon\Carbon::parse($row->tanggal_keluar_oven)->format('d-M-Y') : '-',
                    // Gunakan operator ?-> untuk menghindari error jika relasi bernilai null
                    $row->jamKeluarOven?->jam_keluar_oven ?? '-',
                    $row->oven?->oven ?? '-',
                    $row->customer?->customer ?? 'Manual Input',
                    $row->modelSize?->modelsize ?? '-',
                    $row->tinggiFormer?->tinggi_former ?? '-',
                    $row->spesifikasi?->spesifikasi ?? '-',
                    $row->kode_tanah,
                    $row->kode_bakar,
                    $row->sampel,
                    $row->berat_former,
                    $row->tgl_produksi ? \Carbon\Carbon::parse($row->tgl_produksi)->format('d-M-Y') : '-',
                    $row->suhu_actual ?? 0,
                    $row->hasil_test,
                    $row->keterangan ?? '-',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


}

