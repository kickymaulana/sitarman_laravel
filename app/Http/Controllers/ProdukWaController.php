<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Spesifikasi;
use App\Models\WaterAbsorption;
use App\Models\ProduWa;
use App\Models\ProdukDwa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\DensityWaterAbsorption;
use App\Models\HasilThermalShock;

class ProdukWaController extends Controller
{

    public function index(Request $request, DensityWaterAbsorption $waterabsorption)
    {
        $produkwa = ProdukDwa::query()
            ->where('density_water_absorption_id', $waterabsorption->id) // Filter relasi induk baru
            ->with(['customer', 'modelSize', 'spesifikasi'])
            ->when($request->search, function ($query, $search) {
                // Pencarian diselaraskan ke nama kolom baru 'sample'
                $query->where('sample', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%");
                      });
            })
            ->orderBy('no', 'asc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('ProdukWa/Index', [
            'waterabsorption' => $waterabsorption,
            'produkwa'        => $produkwa,
            'filters'         => $request->only(['search'])
        ]);
    }

    public function create(DensityWaterAbsorption $waterabsorption)
    {
        // Mengambil log item terakhir yang satu induk untuk auto-fill data identitas
        $lastProduk = ProdukDwa::where('density_water_absorption_id', $waterabsorption->id)
            ->latest()
            ->first();

        return Inertia::render('ProdukWa/Create', [
            'waterabsorption' => $waterabsorption,
            'lastProduk'      => $lastProduk,
            'customers'       => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'      => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis'    => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
        ]);
    }

    public function store(Request $request, DensityWaterAbsorption $waterabsorption)
    {
        $validated = $request->validate([
            'no'             => 'required|numeric',
            'tgl_produksi'   => 'required|date',
            'customer_id'    => 'required|exists:customer,id',
            'modelsize_id'   => 'required|exists:modelsize,id',
            'spesifikasi_id' => 'required|exists:spesifikasi,id',
            'sampel'         => 'nullable|string|max:255', // Di mapping ke kolom 'sample' di database
            'temp'           => 'required|integer',
            'palm_wo'        => 'required|numeric',
            'palm_wa'        => 'required|numeric',
            'mc_wo'          => 'required|numeric',
            'mc_wa'          => 'required|numeric',
            'sl_wo'          => 'required|numeric',
            'sl_wa'          => 'required|numeric',
        ]);

        // Menyusun payload data baru
        $data = [
            'no'                          => $validated['no'],
            'density_water_absorption_id' => $waterabsorption->id,
            'customer_id'                 => $validated['customer_id'],
            'modelsize_id'                => $validated['modelsize_id'],
            'spesifikasi_id'              => $validated['spesifikasi_id'],
            'tgl_produksi'                => $validated['tgl_produksi'],
            'sample'                      => $validated['sampel'] ?? '-',
            'temp'                        => $validated['temp'],

            // Kolom parameter Water Absorption
            'palm_wo'                     => $validated['palm_wo'],
            'palm_wa'                     => $validated['palm_wa'],
            'mc_wo'                       => $validated['mc_wo'],
            'mc_wa'                       => $validated['mc_wa'],
            'sl_wo'                       => $validated['sl_wo'],
            'sl_wa'                       => $validated['sl_wa'],

            // Memberikan nilai default aman untuk parameter bawaan density karena tidak diisi di form ini
            'oven_id'                     => 1,
            'jam_keluar_oven_id'          => 1,
            'ketebalan'                   => 0,
            'berat_awal'                  => 0,
            'berat_akhir'                 => 0,
            'volume'                      => 0,
            'density'                     => 0,
        ];

        // Kalkulasi nilai _water di sisi Backend ((wa - wo) / wa) * 100
        $data['palm_water'] = $validated['palm_wa'] ? (($validated['palm_wa'] - $validated['palm_wo']) / $validated['palm_wa']) * 100 : 0;
        $data['mc_water']   = $validated['mc_wa'] ? (($validated['mc_wa'] - $validated['mc_wo']) / $validated['mc_wa']) * 100 : 0;
        $data['sl_water']   = $validated['sl_wa'] ? (($validated['sl_wa'] - $validated['sl_wo']) / $validated['sl_wa']) * 100 : 0;

        // Simpan ke tabel produk_dwa melalui Eloquent Model
        ProdukDwa::create($data);

        return redirect()->route('produkwa.index', $waterabsorption->id)
            ->with('message', 'Item produk water absorption berhasil ditambahkan.');
    }

    public function edit(DensityWaterAbsorption $waterabsorption, ProdukDwa $produkwa)
    {
        // Proteksi validasi pengaman data induk-anak
        if ($produkwa->density_water_absorption_id !== $waterabsorption->id) {
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

    public function update(Request $request, DensityWaterAbsorption $waterabsorption, ProdukDwa $produkwa)
    {
        if ($produkwa->density_water_absorption_id !== $waterabsorption->id) {
            abort(404);
        }

        $validated = $request->validate([
            'no'             => 'required|numeric',
            'tgl_produksi'   => 'required|date',
            'customer_id'    => 'required|exists:customer,id',
            'modelsize_id'   => 'required|exists:modelsize,id',
            'spesifikasi_id' => 'required|exists:spesifikasi,id',
            'sampel'         => 'nullable|string|max:255', // Di mapping ke 'sample'
            'temp'           => 'required|integer',
            'palm_wo'        => 'required|numeric',
            'palm_wa'        => 'required|numeric',
            'mc_wo'          => 'required|numeric',
            'mc_wa'          => 'required|numeric',
            'sl_wo'          => 'required|numeric',
            'sl_wa'          => 'required|numeric',
        ]);

        // Nyatakan susunan data pembaharuan
        $data = [
            'no'             => $validated['no'],
            'customer_id'    => $validated['customer_id'],
            'modelsize_id'   => $validated['modelsize_id'],
            'spesifikasi_id' => $validated['spesifikasi_id'],
            'tgl_produksi'   => $validated['tgl_produksi'],
            'sample'         => $validated['sampel'] ?? '-',
            'temp'           => $validated['temp'],
            'palm_wo'        => $validated['palm_wo'],
            'palm_wa'        => $validated['palm_wa'],
            'mc_wo'          => $validated['mc_wo'],
            'mc_wa'          => $validated['mc_wa'],
            'sl_wo'          => $validated['sl_wo'],
            'sl_wa'          => $validated['sl_wa'],
        ];

        // Hitung ulang nilai _water di sisi Backend ((wa - wo) / wa) * 100
        $data['palm_water'] = $validated['palm_wa'] ? (($validated['palm_wa'] - $validated['palm_wo']) / $validated['palm_wa']) * 100 : 0;
        $data['mc_water']   = $validated['mc_wa'] ? (($validated['mc_wa'] - $validated['mc_wo']) / $validated['mc_wa']) * 100 : 0;
        $data['sl_water']   = $validated['sl_wa'] ? (($validated['sl_wa'] - $validated['sl_wo']) / $validated['sl_wa']) * 100 : 0;

        $produkwa->update($data);

        return redirect()->route('produkwa.index', $waterabsorption->id)
            ->with('message', 'Data item produk water absorption berhasil diperbarui.');
    }

    public function destroy(DensityWaterAbsorption $waterabsorption, ProdukDwa $produkwa)
    {
        if ($produkwa->density_water_absorption_id !== $waterabsorption->id) {
            abort(404);
        }

        $produkwa->delete();

        return redirect()->route('produkwa.index', $waterabsorption->id)
            ->with('with', 'Item produk water absorption berhasil dihapus.');
    }

    public function masukan_ke_hasil_thermal_shock(DensityWaterAbsorption $waterabsorption)
    {
        // 1. Ambil semua data produk DWA yang terikat pada sesi ID induk ini
        $allProdukDwa = ProdukDwa::where('density_water_absorption_id', $waterabsorption->id)->get();

        if ($allProdukDwa->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal memproses. Tidak ada data produk DWA pada batch ini.');
        }

        // 2. Susun array rekap data massal untuk bulk insert
        $dataRekap = [];

        foreach ($allProdukDwa as $item) {
            $dataRekap[] = [
                // Pemetaan identitas dasar material & oven
                'tanggal_keluar_oven' => $waterabsorption->tgl, // Diambil dari tanggal sesi log induk DWA
                'oven_id'             => $item->oven_id,
                'jam_keluar_oven_id'  => $item->jam_keluar_oven_id,
                'customer_id'         => $item->customer_id,
                'modelsize_id'        => $item->modelsize_id,
                'spesifikasi_id'      => $item->spesifikasi_id,
                'tinggi_former_id'    => null, // Di-set null karena data DWA tidak merekam tinggi_former
                'kode_tanah'          => '-',

                // Pemetaan parameter fisik & hitungan laboratorium hasil tes DWA rill
                'thickness'           => $item->ketebalan,
                'density'             => $item->density,
                'wa_palm'             => $item->palm_water,
                'wa_mc'               => $item->mc_water,
                'wa_sli'              => $item->sl_water,

                // Set parameter default pengujian thermal shock (siap di-update user nanti)
                'suhu_180'            => 'Belum Tes',
                'suhu_200'            => 'Belum Tes',
                'suhu'                => 0,
                'berat_former'        => 0,
                'chemical'            => 0.00,
                'luas_area'           => 0.00,
                'visual'              => 0,

                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }

        // 3. Eksekusi bulk insert sekaligus ke database
        HasilThermalShock::insert($dataRekap);

        return redirect()->route('hasilthermalshock.index')
            ->with('message', count($dataRekap) . ' data produk DWA berhasil dikirim massal ke Hasil Thermal Shock.');
    }
}
