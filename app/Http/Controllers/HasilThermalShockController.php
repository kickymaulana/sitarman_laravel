<?php

namespace App\Http\Controllers;

use App\Models\Oven;
use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Spesifikasi;
use App\Models\TinggiFormer;
use App\Models\JamKeluarOven;
use App\Models\HasilThermalShock;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HasilThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $hasil = HasilThermalShock::query()
            ->with(['oven', 'jamKeluarOven', 'customer', 'modelSize', 'spesifikasi'])
            ->when($request->search, function ($query, $search) {
                $query->where('kode_tanah', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%");
                      })
                      ->orWhereHas('modelSize', function($q) use ($search) {
                          $q->where('modelsize', 'like', "%{$search}%");
                      });
            })
            ->orderBy('jam_keluar_oven_id', 'asc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('HasilThermalShock/Index', [
            'hasil'   => $hasil,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        // Mengambil log item rekap terakhir sebagai acuan auto-fill identitas dasar
        $lastRekap = HasilThermalShock::latest()->first();

        return Inertia::render('HasilThermalShock/Create', [
            'lastRekap'       => $lastRekap,
            'ovens'           => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamKeluarOvens'  => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
            'customers'       => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'      => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis'    => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
            'tinggiFormers'   => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_keluar_oven' => 'nullable|date',
            'oven_id'             => 'nullable|exists:oven,id',
            'jam_keluar_oven_id'  => 'nullable|exists:jam_keluar_oven,id',
            'customer_id'         => 'nullable|exists:customer,id',
            'modelsize_id'        => 'nullable|exists:modelsize,id',
            'tinggi_former_id'    => 'nullable|exists:tinggi_former,id',
            'spesifikasi_id'      => 'nullable|exists:spesifikasi,id',
            'kode_tanah'          => 'nullable|string|max:255',
            'suhu_180'            => 'required|in:OK,NG,Belum Tes',
            'suhu_200'            => 'required|in:OK,NG,Belum Tes',
            'suhu'                => 'required|integer',
            'berat_former'        => 'required|integer',
            'ketebalan'           => 'nullable|numeric',
            'metode_biasa'            => 'nullable|numeric',
            'wa_palm'             => 'nullable|numeric',
            'wa_mc'               => 'nullable|numeric',
            'wa_sli'              => 'nullable|numeric',
            'density'             => 'nullable|numeric',
            'hasil_akhir'           => 'required|numeric',
            'visual'              => 'required|integer',
        ]);

        // Menyusun payload data dengan fallback nilai kosong/default yang aman
        $data = [
            'tanggal_keluar_oven' => $validated['tanggal_keluar_oven'],
            'oven_id'             => $validated['oven_id'],
            'jam_keluar_oven_id'  => $validated['jam_keluar_oven_id'],
            'customer_id'         => $validated['customer_id'],
            'modelsize_id'        => $validated['modelsize_id'],
            'tinggi_former_id'    => $validated['tinggi_former_id'],
            'spesifikasi_id'      => $validated['spesifikasi_id'],
            'kode_tanah'          => $validated['kode_tanah'] ?? '-',
            'suhu_180'            => $validated['suhu_180'],
            'suhu_200'            => $validated['suhu_200'],
            'suhu'                => $validated['suhu'],
            'berat_former'        => $validated['berat_former'],
            'ketebalan'           => $validated['thickness'] ?? 0,
            'metode_biasa'            => $validated['metode_biasa'] ?? 0,
            'wa_palm'             => $validated['wa_palm'] ?? 0,
            'wa_mc'               => $validated['wa_mc'] ?? 0,
            'wa_sli'              => $validated['wa_sli'] ?? 0,
            'density'             => $validated['density'] ?? 0,
            'hasil_akhir'           => $validated['hasil_akhir'],
            'visual'              => $validated['visual'],
        ];

        HasilThermalShock::create($data);

        return redirect()->route('hasilthermalshock.index')
            ->with('message', 'Data rekapitulasi hasil thermal shock berhasil disimpan.');
    }

    public function edit(HasilThermalShock $hasilthermalshock)
    {
        return Inertia::render('HasilThermalShock/Edit', [
            'hasil'          => $hasilthermalshock,
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'jamKeluarOvens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
            'customers'      => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelsizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis'   => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
            'tinggiFormers'  => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
        ]);
    }

    public function update(Request $request, HasilThermalShock $hasilthermalshock)
    {
        $validated = $request->validate([
            'tanggal_keluar_oven' => 'nullable|date',
            'oven_id'             => 'nullable|exists:oven,id',
            'jam_keluar_oven_id'  => 'nullable|exists:jam_keluar_oven,id',
            'customer_id'         => 'nullable|exists:customer,id',
            'modelsize_id'        => 'nullable|exists:modelsize,id',
            'tinggi_former_id'    => 'nullable|exists:tinggi_former,id',
            'spesifikasi_id'      => 'nullable|exists:spesifikasi,id',
            'kode_tanah'          => 'nullable|string|max:255',
            'suhu_180'            => 'required|in:OK,NG,Belum Tes',
            'suhu_200'            => 'required|in:OK,NG,Belum Tes',
            'suhu'                => 'required|integer',
            'berat_former'        => 'required|integer',
            'ketebalan'           => 'nullable|numeric',
            'motode_biasa'            => 'nullable|numeric',
            'wa_palm'             => 'nullable|numeric',
            'wa_mc'               => 'nullable|numeric',
            'wa_sli'              => 'nullable|numeric',
            'density'             => 'nullable|numeric',
            'hasil_akhir'           => 'required|numeric',
            'visual'              => 'required|integer',
        ]);

        $hasilthermalshock->update($validated);

        return redirect()->route('hasilthermalshock.index')
            ->with('message', 'Data rekapitulasi hasil thermal shock berhasil diperbarui.');
    }

    public function destroy(HasilThermalShock $hasilthermalshock)
    {
        $hasilthermalshock->delete();

        return redirect()->route('hasilthermalshock.index')
            ->with('message', 'Data rekapitulasi hasil thermal shock berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $fileName = 'rekap_thermal_shock_' . now()->format('Ymd_His') . '.csv';

        // Query data sesuai filter
        $results = \App\Models\HasilThermalShock::query()
            ->with(['oven', 'jamKeluarOven', 'customer', 'modelSize'])
            ->when($search, function ($query, $search) {
                $query->where('kode_tanah', 'like', "%{$search}%")
                    ->orWhereHas('customer', function($q) use ($search) {
                        $q->where('customer', 'like', "%{$search}%");
                    })
                    ->orWhereHas('modelSize', function($q) use ($search) {
                        $q->where('modelsize', 'like', "%{$search}%");
                    });
            })
            ->orderBy('tanggal_keluar_oven', 'desc')
            ->get();

        // Set Header HTTP agar dibaca sebagai file unduhan oleh browser
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Struktur Kolom Header File
        $columns = [
            'ID', 'Tanggal Keluar Oven', 'Customer', 'Model Size', 'Oven', 'Jam Keluar',
            'Kode Tanah', 'Suhu 180°C', 'Suhu 200°C', 'Suhu (°C)', 'Berat Former (g)',
            'Ketebalan', 'Metode Biasa', 'Density', 'WA Palm', 'WA MC', 'WA Sli', 'Hasil Akhir', 'Visual'
        ];

        $callback = function() use($results, $columns) {
            $file = fopen('php://output', 'w');

            // Tambahkan BOM (Byte Order Mark) agar karakter khusus seperti °C terbaca rapi di Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Tulis header kolom
            fputcsv($file, $columns, ';'); // Menggunakan titik koma (;) agar otomatis rapi jadi kolom di Excel regional Indonesia

            // Tulis data baris demi baris
            foreach ($results as $row) {
                fputcsv($file, [
                    $row->id,
                    $row->tanggal_keluar_oven ? \Carbon\Carbon::parse($row->tanggal_keluar_oven)->format('d-M-Y') : '-',
                    $row->customer?->customer ?? 'Manual Input',
                    $row->modelSize?->modelsize ?? '-',
                    $row->oven?->oven ?? '-',
                    $row->jamKeluarOven?->jam_keluar_oven ?? '-',
                    $row->kode_tanah,
                    $row->suhu_180,
                    $row->suhu_200,
                    $row->suhu,
                    $row->berat_former,
                    $row->ketebalan,
                    $row->metode_biasa,
                    $row->density,
                    $row->wa_palm . '%',
                    $row->wa_mc . '%',
                    $row->wa_sli . '%',
                    $row->hasil_akhir,
                    $row->visual,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
