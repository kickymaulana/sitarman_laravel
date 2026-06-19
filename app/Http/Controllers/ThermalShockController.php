<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ThermalShock;
use App\Models\ThermalOven;
use App\Models\ThermalPintu;
use App\Models\Produks;
use Carbon\Carbon;

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $thermalshocks = ThermalShock::query()
            ->with(['thermalOven', 'thermalPintu', 'produks', 'user'])
            ->when($request->search, function ($query, $search) {
                // Mencari berdasarkan tanggal proses atau nama di tabel relasi
                $query->where('hari_tgl', 'like', "%{$search}%")
                      ->orWhereHas('thermalOven', function($q) use ($search) {
                          $q->where('thermal_oven', 'like', "%{$search}%");
                      })
                      ->orWhereHas('thermalPintu', function($q) use ($search) {
                          $q->where('thermal_pintu', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ThermalShock/Index', [
            'thermalshocks' => $thermalshocks,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('ThermalShock/Create', [
            'ovens' => ThermalOven::select('id', 'thermal_oven')->orderBy('thermal_oven')->get(),
            'pintus' => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'thermal_oven_id'     => 'required|exists:thermal_oven,id',
            'thermal_pintu_id'    => 'required|exists:thermal_pintu,id',
            'hari_tgl'            => 'required|date',
            'suhu_testing'        => 'required|integer',
            'suhu_display'        => 'required|integer',
            'suhu_actual'         => 'required|integer',
            'jam_awal_proses'     => 'required',
            'jam_capai_suhu'      => 'required',
            'suhu_awal'           => 'required|integer',
            'suhu_air'            => 'required|string|max:255',
            'jam_mulai_tembak'    => 'nullable',
            'jam_selesai_tembak'  => 'nullable',
        ], [
            'thermal_oven_id.required'  => 'Oven wajib dipilih.',
            'thermal_pintu_id.required' => 'Pintu wajib dipilih.',
            'hari_tgl.required'         => 'Hari/Tanggal wajib diisi.',
            // ... Kamu bisa menambahkan custom message lainnya di sini
        ]);

        // Ambil semua data request, lalu sisipkan user_id di dalamnya
        $data = $request->all();
        $data['user_id'] = auth()->id();

        // JIKA kosong/null, isi dengan '00:00:00' sebelum disimpan ke database
        $data['jam_mulai_tembak'] = $request->jam_mulai_tembak ?? '00:00:00';
        $data['jam_selesai_tembak'] = $request->jam_selesai_tembak ?? '00:00:00';

        ThermalShock::create($data);

        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil ditambahkan.');
    }

    public function edit(ThermalShock $thermalshock)
    {
        return Inertia::render('ThermalShock/Edit', [
            'thermalshock' => $thermalshock,
            'ovens'        => ThermalOven::select('id', 'thermal_oven')->orderBy('thermal_oven')->get(),
            'pintus'       => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get()
        ]);
    }

    public function update(Request $request, ThermalShock $thermalshock)
    {
        $request->validate([
            'thermal_oven_id'     => 'required|exists:thermal_oven,id',
            'thermal_pintu_id'    => 'required|exists:thermal_pintu,id',
            'hari_tgl'            => 'required|date',
            'suhu_testing'        => 'required|integer',
            'suhu_motor'          => 'nullable|string|max:255',
            'suhu_display'        => 'required|integer',
            'suhu_actual'         => 'required|integer',
            'jam_awal_proses'     => 'required',
            'jam_capai_suhu'      => 'required',
            'suhu_awal'           => 'required|integer',
            'suhu_air'            => 'required|string|max:255',
            'jam_mulai_tembak'    => 'required',
            'jam_selesai_tembak'  => 'nullable',
        ]);

        $thermalshock->update($request->all());

        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil diperbarui.');
    }

    public function show(ThermalShock $thermalshock)
    {
        // Load relasi jika belum ter-load otomatis
        $thermalshock->load(['thermalOven', 'thermalPintu', 'user']);


        // Ambil daftar thermal shock lain (misal 30 log terakhir) untuk opsi target copy
        // Kita format tampilannya agar operator mudah mengenali log target
        $thermalShockOptions = ThermalShock::with(['thermalOven', 'thermalPintu'])
            ->where('id', '!=', $thermalshock->id)
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => "Tanggal: " . date('d-m-Y', strtotime($item->hari_tgl)) .
                               " | Suhu: " . ($item->suhu_testing ?? '-') .
                               " | Pintu: " . ($item->thermalPintu->thermal_pintu ?? '-')
                ];
            });

        return Inertia::render('ThermalShock/Show', [
            'thermalshock' => $thermalshock,
            'thermalShockOptions' => $thermalShockOptions
        ]);
    }

    public function copyProduk(Request $request, ThermalShock $thermalshock)
    {
        $request->validate([
            'target_thermal_shock_id' => 'required|exists:thermal_shock,id',
        ], [
            'target_thermal_shock_id.required' => 'Target Thermal Shock tujuan wajib dipilih.'
        ]);

        $targetId = $request->target_thermal_shock_id;

        // Ambil semua produk dari thermal shock asal
        $produks = $thermalshock->produks;

        if ($produks->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada produk di log ini yang bisa disalin.');
        }

        // Lakukan clone data menggunakan DB Transaction agar aman
        \DB::transaction(function () use ($produks, $targetId) {
            foreach ($produks as $produk) {
                // Replika data produk
                $newProduk = $produk->replicate();

                // Set ke ID Thermal Shock target tujuan
                $newProduk->thermal_shock_id = $targetId;

                // Reset parameter hasil test ke default awal
                $newProduk->hasil_test = 'Belum Tes';
                $newProduk->suhu_actual = null;
                $newProduk->keterangan = null;

                $newProduk->save();
            }
        });

        return redirect()->route('thermalshock.show', $targetId)
            ->with('message', 'Berhasil menyalin ' . $produks->count() . ' produk ke log Thermal Shock ini.');
    }

    public function destroy(ThermalShock $thermalshock)
    {
        $thermalshock->delete();
        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil dihapus.');
    }

    public function exportAllRekap(Request $request)
    {
        set_time_limit(0);

        $fileName = 'rekap_total_thermal_shock_' . now()->format('Ymd_His') . '.csv';

        // 1. Ambil semua data produk beserta induk thermal_shock dan relasi master datanya
        // Kita filter juga berdasarkan keyword search jika user sedang melakukan pencarian di tabel
        $search = $request->input('search');

        $produks = \App\Models\Produk::query()
            ->with(['thermalShock', 'oven', 'customer', 'modelSize', 'spesifikasi', 'tinggiFormer', 'jamKeluarOven'])
            ->when($search, function ($query, $search) {
                $query->where('kode_tanah', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%");
                      })
                      ->orWhereHas('modelSize', function($q) use ($search) {
                          $q->where('modelsize', 'like', "%{$search}%");
                      });
            })
            ->get();

        // 2. PROSES PENGELOMPOKAN (GROUPING) AGAR JADI SATU BARIS KE SAMPING
        $groupedData = [];

        foreach ($produks as $item) {
            // Buat unique key berdasarkan kombinasi fisik sampel produk agar bisa mendeteksi kesamaan data
            $uniqueKey = implode('_', [
                $item->tanggal_keluar_oven,
                $item->oven_id,
                $item->customer_id,
                $item->modelsize_id,
                $item->tinggi_former_id,
                $item->spesifikasi_id,
                $item->kode_tanah,
                $item->berat_former
            ]);

            // Ambil parameter suhu testing dari tabel induknya
            $suhuTesting = $item->thermalShock?->suhu_testing ?? null;

            // Jika kelompok data fisik ini belum ada di array rekap, daftarkan info dasarnya terlebih dahulu
            if (!isset($groupedData[$uniqueKey])) {
                $groupedData[$uniqueKey] = [
                    'tanggal_keluar_oven' => $item->tanggal_keluar_oven ? Carbon::parse($item->tanggal_keluar_oven)->format('d-M-Y') : '-',
                    'jam_keluar'          => $item->jamKeluarOven?->jam_keluar_oven ?? '-',
                    'oven'                => $item->oven?->oven ?? '-',
                    'customer'            => $item->customer?->customer ?? 'Manual Input',
                    'model_size'          => $item->modelSize?->modelsize ?? '-',
                    'tinggi_former'       => $item->tinggiFormer?->tinggi_former ?? '-',
                    'spesifikasi'         => $item->spesifikasi?->spesifikasi ?? '-',
                    'kode_tanah'          => $item->kode_tanah,
                    'kode_bakar'          => $item->kode_bakar,
                    'sampel'              => $item->sampel,
                    'berat_former'        => $item->berat_former,
                    'tgl_production'      => $item->tgl_production ? Carbon::parse($item->tgl_production)->format('d-M-Y') : '-',
                    'keterangan'          => $item->keterangan ?? '-',

                    // Siapkan wadah kolom kanan-kiri default kosong
                    'suhu_180'            => '-',
                    'hasil_180'           => '-',
                    'suhu_200'            => '-',
                    'hasil_200'           => '-',
                ];
            }

            // Masukkan data hasil uji ke kolom kanan atau kiri secara dinamis jika kuncinya sama
            if ($suhuTesting == 200) {
                $groupedData[$uniqueKey]['suhu_200']  = $item->suhu_actual !== null ? $item->suhu_actual . '°C' : '0°C';
                $groupedData[$uniqueKey]['hasil_200'] = $item->hasil_test;
            } else {
                // Dianggap masuk ke uji 180°C (Kiri)
                $groupedData[$uniqueKey]['suhu_180']  = $item->suhu_actual !== null ? $item->suhu_actual . '°C' : '0°C';
                $groupedData[$uniqueKey]['hasil_180'] = $item->hasil_test;
            }
        }

        // 3. PROSES STREAM DOWNLOAD CSV
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Tanggal Keluar Oven', 'Jam Keluar Oven', 'Oven', 'Customer', 'Model Size',
            'Tinggi Former', 'Spesifikasi', 'Kode Tanah', 'Kode Bakar', 'Sampel',
            'Berat Former (g)', 'Tanggal Produksi', 'Keterangan',
            // Kolom Kiri (180)
            'Suhu Aktual 180°C', 'Hasil Test 180°C',
            // Kolom Kanan (200)
            'Suhu Aktual 200°C', 'Hasil Test 200°C'
        ];

        $callback = function() use($groupedData, $columns) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // Tambahkan BOM UTF-8

            fputcsv($file, $columns, ';');

            foreach ($groupedData as $row) {
                fputcsv($file, [
                    $row['tanggal_keluar_oven'],
                    $row['jam_keluar'],
                    $row['oven'],
                    $row['customer'],
                    $row['model_size'],
                    $row['tinggi_former'],
                    $row['spesifikasi'],
                    $row['kode_tanah'],
                    $row['kode_bakar'],
                    $row['sampel'],
                    $row['berat_former'],
                    $row['tgl_production'],
                    $row['keterangan'],
                    // Kolom Hasil 180 (Kiri)
                    $row['suhu_180'],
                    $row['hasil_180'],
                    // Kolom Hasil 200 (Kanan)
                    $row['suhu_200'],
                    $row['hasil_200'],
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
