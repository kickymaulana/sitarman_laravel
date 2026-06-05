<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chemical;
use App\Models\ProdukChemical;
use App\Models\HasilThermalShock;
use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Oven;
use App\Models\JamKeluarOven;
use Illuminate\Support\Facades\Storage;

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
            'gambar'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // <-- Validasi file gambar
        ]);

        if (empty($validated['sample'])) {
            $validated['sample'] = '-';
        }

        $validated['chemical_id'] = $chemical->id;

        // Proses upload ke MinIO (S3 Disk) jika file diupload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $path = $file->store('produk-chemical', 's3'); // Masuk ke folder produk-chemical di bucket sitarman
            $validated['gambar'] = $path;
        } else {
            $validated['gambar'] = null;
        }
        // Langsung buat data tanpa perlu hitung ulang di sisi server
        ProdukChemical::create($validated);

        return redirect()->route('produkchemical.index', $chemical->id)
            ->with('message', 'Item produk chemical berhasil ditambahkan.');
    }

    public function edit(Request $request, Chemical $chemical, ProdukChemical $produkchemical)
    {
        if ($produkchemical->chemical_id !== $chemical->id) {
            abort(404);
        }

        // Ambil tanggal filter dari query string, jika tidak ada gunakan default dari record
        $targetDate = $request->query('tanggal_filter', $produkchemical->tanggal_keluar_oven);

        // Ambil kandidat thermal shock berdasarkan tanggal keluar oven
        $thermalShockCandidates = HasilThermalShock::query()
            ->where('tanggal_keluar_oven', $targetDate)
            ->with(['oven', 'jamKeluarOven', 'customer', 'modelSize'])
            ->select([
                'id',
                'tanggal_keluar_oven',
                'kode_tanah',
                'suhu',
                'oven_id',
                'jam_keluar_oven_id',
                'customer_id',
                'modelsize_id'
            ])
            ->orderBy('jam_keluar_oven_id')
            ->get();

        return Inertia::render('ProdukChemical/Edit', [
            'chemical'               => $chemical,
            'produkchemical'         => $produkchemical,
            'customers'              => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'             => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'ovens'                  => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamkeluarovens'         => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
            'thermalShockCandidates' => $thermalShockCandidates,
            'selectedFilterDate'     => $targetDate,
        ]);
    }

    public function update(Request $request, Chemical $chemical, ProdukChemical $produkchemical)
    {
        if ($produkchemical->chemical_id !== $chemical->id) {
            abort(404);
        }

        $validated = $request->validate([
            'tgl_produksi'          => 'required|date',
            'customer_id'           => 'required|exists:customer,id',
            'modelsize_id'          => 'required|exists:modelsize,id',
            'oven_id'               => 'required|exists:oven,id',
            'tanggal_keluar_oven'   => 'required|date',
            'jam_keluar_oven_id'    => 'required|exists:jam_keluar_oven,id',
            'sample'                => 'nullable|string|max:255',

            'ketebalan_mm'          => 'required|numeric|min:0',
            'berat_awal'            => 'required|numeric|min:0',
            'berat_akhir'           => 'required|numeric|min:0',
            'density'               => 'required|numeric|min:0',

            'berat_hilang'          => 'required|numeric',
            'metode_biasa'          => 'required|numeric',
            'volume'                => 'required|numeric',
            'ketebalan_dm'          => 'required|numeric',
            'luas_permukaan'        => 'required|numeric',
            'hasil_akhir'           => 'required|numeric',
            'hasil_thermalshock_id' => 'nullable', // Validasi field sinkronisasi
            'gambar'                => 'nullable',
        ]);

        if (empty($validated['sample'])) {
            $validated['sample'] = '-';
        }

        // Jalankan pengecekan file upload secara spesifik
        if ($request->hasFile('gambar')) {
            $request->validate(['gambar' => 'image|mimes:jpeg,png,jpg,webp|max:2048']);

            // Hapus gambar lama dari MinIO jika ada
            if ($produkchemical->gambar && Storage::disk('s3')->exists($produkchemical->gambar)) {
                Storage::disk('s3')->delete($produkchemical->gambar);
            }

            $file = $request->file('gambar');
            $path = $file->store('produk-chemical', 's3');
            $validated['gambar'] = $path;
        } else {
            // Jika tidak upload gambar baru, pertahankan path lama
            unset($validated['gambar']);
        }

        // 1. Update data internal produk chemical
        $produkchemical->update($validated);
        $produkchemical->refresh();

        // 2. Integrasi ke tabel Hasil Thermal Shock
        $syncId = $request->input('hasil_thermalshock_id');

        if ($syncId === 'NEW') {
            HasilThermalShock::create([
                'tanggal_keluar_oven' => $produkchemical->tanggal_keluar_oven,
                'oven_id'             => $produkchemical->oven_id,
                'jam_keluar_oven_id'  => $produkchemical->jam_keluar_oven_id,
                'customer_id'         => $validated['customer_id'],
                'modelsize_id'        => $validated['modelsize_id'],
                'kode_tanah'          => '-',
                'suhu_180'            => 'Belum Tes',
                'suhu_200'            => 'Belum Tes',
                'suhu'                => 0,
                'berat_former'        => 0,
                'ketebalan'           => $validated['ketebalan_mm'],
                'metode_biasa'            => $validated['metode_biasa'],
                'hasil_akhir'            => $validated['hasil_akhir'],
                'density'             => $validated['density'],
                'visual'              => 0,
            ]);
        } elseif (!empty($syncId) && is_numeric($syncId)) {
            $thermalShock = HasilThermalShock::find($syncId);
            if ($thermalShock) {
                $thermalShock->update([
                    'ketebalan' => $validated['ketebalan_mm'],
                    'metode_biasa'  => $validated['metode_biasa'],
                    'hasil_akhir'  => $validated['hasil_akhir'],
                    'density'   => $validated['density'],
                ]);
            }
        }

        return redirect()->route('produkchemical.index', $chemical->id)
            ->with('message', 'Item produk chemical berhasil diperbarui.');
    }

    public function destroy(Chemical $chemical, ProdukChemical $produkchemical)
    {
        if ($produkchemical->chemical_id !== $chemical->id) {
            abort(404);
        }

        // Hapus file gambar dari MinIO sebelum menghapus record database
        if ($produkchemical->gambar && Storage::disk('s3')->exists($produkchemical->gambar)) {
            Storage::disk('s3')->delete($produkchemical->gambar);
        }

        $produkchemical->delete();

        return redirect()->route('produkchemical.index', $chemical->id)
            ->with('message', 'Item produk chemical berhasil dihapus.');
    }
}
