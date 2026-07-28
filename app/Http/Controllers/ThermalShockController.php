<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ThermalShock;
use App\Models\ThermalPintu;
use App\Models\Oven;
use App\Models\Customer;
use App\Models\TinggiFormer;
use App\Models\JamKeluarOven;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $thermalshocks = ThermalShock::query()
            // PERBAIKAN: Eager load seluruh relasi secara lengkap agar terbaca oleh Vue
            ->with(['thermalPintu', 'user', 'customer', 'oven', 'tinggiFormer', 'jamKeluarOven'])
            ->when($request->search, function ($query, $search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('hari_tgl', 'like', "%{$search}%")
                      ->orWhere('sampel', 'like', "%{$search}%")
                      ->orWhere('kode_bakar', 'like', "%{$search}%")
                      ->orWhere('kode_tanah', 'like', "%{$search}%")
                      ->orWhere('berat_former', 'like', "%{$search}%")
                      ->orWhere('posisi_former', 'like', "%{$search}%")
                      ->orWhere('suhu_display_180', 'like', "%{$search}%")
                      ->orWhere('suhu_actual_180', 'like', "%{$search}%")
                      ->orWhere('suhu_awal_180', 'like', "%{$search}%")
                      ->orWhere('suhu_air_180', 'like', "%{$search}%")
                      ->orWhere('hasil_180', 'like', "%{$search}%")
                      ->orWhere('suhu_display_200', 'like', "%{$search}%")
                      ->orWhere('suhu_actual_200', 'like', "%{$search}%")
                      ->orWhere('suhu_awal_200', 'like', "%{$search}%")
                      ->orWhere('suhu_air_200', 'like', "%{$search}%")
                      ->orWhere('hasil_200', 'like', "%{$search}%")
                      ->orWhere('hasil_test_180', 'like', "%{$search}%")
                      ->orWhere('hasil_test_200', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%")

                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('thermalPintu', function($q) use ($search) {
                          $q->where('thermal_pintu', 'like', "%{$search}%");
                      })
                      ->orWhereHas('oven', function($q) use ($search) {
                          $q->where('oven', 'like', "%{$search}%");
                      })
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('size', 'like', "%{$search}%")
                            ->orWhere('spesifikasi', 'like', "%{$search}%");
                      })
                      ->orWhereHas('tinggiFormer', function($q) use ($search) {
                          $q->where('tinggi_former', 'like', "%{$search}%");
                      })
                      ->orWhereHas('jamKeluarOven', function($q) use ($search) {
                          $q->where('jam_keluar_oven', 'like', "%{$search}%");
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
        $lastRecord = ThermalShock::where('user_id', auth()->id())
            ->latest()
            ->first();

        return Inertia::render('ThermalShock/Create', [
            'lastRecord'     => $lastRecord,
            'thermalPintus'  => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get(),
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'customers'      => Customer::select('id', 'customer', 'model', 'spesifikasi', 'size')->orderBy('customer')->get(),
            'tinggiFormers'  => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
            'jamKeluarOvens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            // Metadata Utama
            'thermal_pintu_id'       => 'required|exists:thermal_pintu,id',
            'hari_tgl'               => 'required|date',

            // Parameter Pengujian 180°C (Wajib)
            'suhu_awal_180'          => 'required|integer',
            'suhu_display_180'       => 'required|integer',
            'suhu_actual_180'        => 'required|integer',
            'suhu_air_180'           => 'required|string|max:255',
            'jam_awal_proses_180'    => 'required|string',
            'jam_capai_suhu_180'     => 'required|string',
            'jam_mulai_tembak_180'   => 'nullable|string',
            'jam_selesai_tembak_180' => 'nullable|string',

            // Parameter Pengujian 200°C (Boleh Kosong / Nullable)
            'suhu_awal_200'          => 'nullable|integer',
            'suhu_display_200'       => 'nullable|integer',
            'suhu_actual_200'        => 'nullable|integer',
            'suhu_air_200'           => 'nullable|string|max:255',
            'jam_awal_proses_200'    => 'nullable|string',
            'jam_capai_suhu_200'     => 'nullable|string',
            'jam_mulai_tembak_200'   => 'nullable|string',
            'jam_selesai_tembak_200' => 'nullable|string',

            // Data Manufaktur Produk
            'kode_bakar'             => 'nullable|integer',
            'kode_tanah'             => 'nullable|string|max:255',
            'sampel'                 => 'nullable|string|max:255',
            'oven_id'                => 'required|exists:oven,id',
            'customer_id'            => 'required|exists:customer,id',
            'tinggi_former_id'       => 'required|exists:tinggi_former,id',
            'jam_keluar_oven_id'     => 'required|exists:jam_keluar_oven,id',
            'tanggal_keluar_oven'    => 'required|date',
            'tgl_produksi'           => 'required|date',
            'berat_former'           => 'required|integer',
            'posisi_former'          => 'required|integer',
            'hasil_test_180'         => 'required|in:OK,NG,Belum Tes,Tidak Test',
            'hasil_test_200'         => 'required|in:OK,NG,Belum Tes,Tidak Test',
            'keterangan'             => 'nullable|string',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        // 1. Normalisasi Fallback nilai Angka/Integer & String untuk Parameter 200°C
        $data['suhu_awal_200']    = $request->suhu_awal_200 ?? 0;
        $data['suhu_display_200'] = $request->suhu_display_200 ?? 0;
        $data['suhu_actual_200']  = $request->suhu_actual_200 ?? 0;
        $data['suhu_air_200']     = $request->suhu_air_200 ?: '-';

        // 2. Normalisasi Fallback nilai Jam (Time) parameter 180 & 200
        $data['jam_mulai_tembak_180']   = $request->jam_mulai_tembak_180 ?: '00:00:00';
        $data['jam_selesai_tembak_180'] = $request->jam_selesai_tembak_180 ?: '00:00:00';

        $data['jam_awal_proses_200']    = $request->jam_awal_proses_200 ?: '00:00:00';
        $data['jam_capai_suhu_200']     = $request->jam_capai_suhu_200 ?: '00:00:00';
        $data['jam_mulai_tembak_200']   = $request->jam_mulai_tembak_200 ?: '00:00:00';
        $data['jam_selesai_tembak_200'] = $request->jam_selesai_tembak_200 ?: '00:00:00';

        // 3. Menambahkan format detik (:00) jika string hanya berisi HH:mm
        $timeFields = [
            'jam_awal_proses_180', 'jam_capai_suhu_180', 'jam_mulai_tembak_180', 'jam_selesai_tembak_180',
            'jam_awal_proses_200', 'jam_capai_suhu_200', 'jam_mulai_tembak_200', 'jam_selesai_tembak_200'
        ];

        foreach ($timeFields as $field) {
            if (!empty($data[$field]) && strlen($data[$field]) === 5) {
                $data[$field] .= ':00';
            }
        }

        ThermalShock::create($data);

        return redirect()->route('thermalshock.index')
            ->with('message', 'Data Thermal Shock Gabungan berhasil disimpan.');
    }


    public function edit(ThermalShock $thermalshock)
    {
        return Inertia::render('ThermalShock/Edit', [
            'thermalshock'   => $thermalshock,
            // KELUARKAN: 'thermalOvens' dihapus karena sudah tidak digunakan di form terpadu Anda
            'thermalPintus'  => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get(),
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'customers'      => Customer::select('id', 'customer', 'model', 'spesifikasi', 'size')->orderBy('customer')->get(),
            'tinggiFormers'  => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
            'jamKeluarOvens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function update(Request $request, ThermalShock $thermalshock)
    {
        $request->validate([
            // Metadata Utama
            'thermal_pintu_id'       => 'required|exists:thermal_pintu,id',
            'hari_tgl'               => 'required|date',

            // Parameter Pengujian 180°C
            'suhu_awal_180'          => 'required|integer',
            'suhu_display_180'        => 'required|integer',
            'suhu_actual_180'         => 'required|integer',
            'suhu_air_180'           => 'required|string|max:255',
            'jam_awal_proses_180'    => 'required|string',
            'jam_capai_suhu_180'     => 'required|string',
            'jam_mulai_tembak_180'   => 'nullable|string',
            'jam_selesai_tembak_180' => 'nullable|string',

            // Parameter Pengujian 200°C
            'suhu_awal_200'          => 'required|integer',
            'suhu_display_200'        => 'required|integer',
            'suhu_actual_200'         => 'required|integer',
            'suhu_air_200'           => 'required|string|max:255',
            'jam_awal_proses_200'    => 'required|string',
            'jam_capai_suhu_200'     => 'required|string',
            'jam_mulai_tembak_200'   => 'nullable|string',
            'jam_selesai_tembak_200' => 'nullable|string',

            // Data Manufaktur Produk
            'kode_bakar'             => 'nullable|integer',
            'kode_tanah'             => 'nullable|string|max:255',
            'sampel'                 => 'nullable|string|max:255',
            'oven_id'                => 'required|exists:oven,id',
            'customer_id'            => 'required|exists:customer,id',
            'tinggi_former_id'       => 'required|exists:tinggi_former,id',
            'jam_keluar_oven_id'     => 'required|exists:jam_keluar_oven,id',
            'tanggal_keluar_oven'    => 'required|date',
            'tgl_produksi'           => 'required|date',
            'berat_former'           => 'required|integer',
            'posisi_former'          => 'required|integer',
            'hasil_test_180'         => 'required|in:OK,NG,Belum Tes,Tidak Test',
            'hasil_test_200'         => 'required|in:OK,NG,Belum Tes,Tidak Test',
            'keterangan'             => 'nullable|string',
        ]);

        $data = $request->all();

        // Normalisasi fallback nilai jam jika kosong
        $data['jam_mulai_tembak_180']   = $request->jam_mulai_tembak_180 ?: '00:00:00';
        $data['jam_selesai_tembak_180'] = $request->jam_selesai_tembak_180 ?: '00:00:00';
        $data['jam_mulai_tembak_200']   = $request->jam_mulai_tembak_200 ?: '00:00:00';
        $data['jam_selesai_tembak_200'] = $request->jam_selesai_tembak_200 ?: '00:00:00';

        // Tambahkan detik (:00) jika frontend mengirim format HH:mm agar lolos seleksi tipe data database
        $timeFields = [
            'jam_awal_proses_180', 'jam_capai_suhu_180', 'jam_mulai_tembak_180', 'jam_selesai_tembak_180',
            'jam_awal_proses_200', 'jam_capai_suhu_200', 'jam_mulai_tembak_200', 'jam_selesai_tembak_200'
        ];

        foreach ($timeFields as $field) {
            if (!empty($data[$field]) && strlen($data[$field]) === 5) {
                $data[$field] .= ':00';
            }
        }

        $thermalshock->update($data);

        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil diperbarui.');
    }

    public function destroy(ThermalShock $thermalshock)
    {
        $thermalshock->delete();
        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil dihapus.');
    }


    public function bulkReplicate(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:thermal_shock,id',
        ]);

        foreach ($request->ids as $id) {
            $thermalshock = ThermalShock::find($id);
            if ($thermalshock) {
                $newRecord = $thermalshock->replicate();
                // Reset hasil pengujian ke kondisi awal untuk pencatatan baru
                $newRecord->hasil_test_180 = 'Belum Tes';
                $newRecord->hasil_test_200 = 'Belum Tes';
                $newRecord->keterangan     = '-';
                $newRecord->user_id        = auth()->id();
                $newRecord->save();
            }
        }

        return redirect()->route('thermalshock.index')
            ->with('message', count($request->ids) . " data Record Thermal Shock berhasil diduplikasi.");
    }

    public function bulkEdit(Request $request)
    {
        $ids = $request->has('ids') ? explode(',', $request->ids) : [];

        // PERBAIKAN: Masukkan eager loading customer di sini juga jika dibutuhkan rincian produk saat bulk edit hasil
        $thermalshocks = ThermalShock::with(['customer', 'oven', 'tinggiFormer', 'jamKeluarOven'])
            ->whereIn('id', $ids)
            ->orderBy('posisi_former', 'asc')
            ->get();

        return Inertia::render('ThermalShock/BulkEditHasil', [
            'thermalshocks' => $thermalshocks,
            'selectedIds' => $ids
        ]);
    }


    public function bulkUpdate(Request $request)
    {
        $request->validate([
            // Master Parameter Suhu 180°C (Nullable jika tidak diubah)
            'suhu_awal_180'          => 'nullable|integer',
            'suhu_display_180'       => 'nullable|integer',
            'suhu_actual_180'        => 'nullable|integer',
            'suhu_air_180'           => 'nullable|string',
            'jam_awal_proses_180'    => 'nullable|string',
            'jam_capai_suhu_180'     => 'nullable|string',
            'jam_mulai_tembak_180'   => 'nullable|string',
            'jam_selesai_tembak_180' => 'nullable|string',

            // Master Parameter Suhu 200°C (Nullable jika tidak diubah)
            'suhu_awal_200'          => 'nullable|integer',
            'suhu_display_200'       => 'nullable|integer',
            'suhu_actual_200'        => 'nullable|integer',
            'suhu_air_200'           => 'nullable|string',
            'jam_awal_proses_200'    => 'nullable|string',
            'jam_capai_suhu_200'     => 'nullable|string',
            'jam_mulai_tembak_200'   => 'nullable|string',
            'jam_selesai_tembak_200' => 'nullable|string',

            // Data Array Hasil Test Per Produk
            'records'                  => 'required|array',
            'records.*.id'             => 'required|exists:thermal_shock,id',
            'records.*.hasil_test_180' => 'required|in:OK,NG,Belum Tes,Tidak Test',
            'records.*.hasil_180'      => 'nullable|integer|min:0',
            'records.*.hasil_test_200' => 'required|in:OK,NG,Belum Tes,Tidak Test,Pecah 180',
            'records.*.hasil_200'      => 'nullable|integer|min:0',
            'records.*.keterangan'     => 'nullable|string',
        ]);

        // 1. Kumpulkan data parameter global yang diisi di header (jika ada)
        $globalHeaderData = [];

        // Kelompok field jam yang butuh akhiran :00 jika diinput HH:mm
        $timeFields180 = ['jam_awal_proses_180', 'jam_capai_suhu_180', 'jam_mulai_tembak_180', 'jam_selesai_tembak_180'];
        $timeFields200 = ['jam_awal_proses_200', 'jam_capai_suhu_200', 'jam_mulai_tembak_200', 'jam_selesai_tembak_200'];

        // Map nilai 180°C
        if ($request->filled('suhu_awal_180')) $globalHeaderData['suhu_awal_180'] = $request->suhu_awal_180;
        if ($request->filled('suhu_display_180')) $globalHeaderData['suhu_display_180'] = $request->suhu_display_180;
        if ($request->filled('suhu_actual_180')) $globalHeaderData['suhu_actual_180'] = $request->suhu_actual_180;
        if ($request->filled('suhu_air_180')) $globalHeaderData['suhu_air_180'] = $request->suhu_air_180;

        foreach ($timeFields180 as $f) {
            if ($request->filled($f)) {
                $val = $request->$f;
                $globalHeaderData[$f] = (strlen($val) === 5) ? $val . ':00' : $val;
            }
        }

        // Map nilai 200°C
        if ($request->filled('suhu_awal_200')) $globalHeaderData['suhu_awal_200'] = $request->suhu_awal_200;
        if ($request->filled('suhu_display_200')) $globalHeaderData['suhu_display_200'] = $request->suhu_display_200;
        if ($request->filled('suhu_actual_200')) $globalHeaderData['suhu_actual_200'] = $request->suhu_actual_200;
        if ($request->filled('suhu_air_200')) $globalHeaderData['suhu_air_200'] = $request->suhu_air_200;

        foreach ($timeFields200 as $f) {
            if ($request->filled($f)) {
                $val = $request->$f;
                $globalHeaderData[$f] = (strlen($val) === 5) ? $val . ':00' : $val;
            }
        }

        // 2. Loop update data detail per produk
        foreach ($request->records as $row) {
            $updateData = array_merge($globalHeaderData, [
                'hasil_test_180' => $row['hasil_test_180'],
                'hasil_180'      => $row['hasil_180'] ?? 0,
                'hasil_test_200' => $row['hasil_test_200'],
                'hasil_200'      => $row['hasil_200'] ?? 0,
                'keterangan'     => $row['keterangan'] ?? '-',
                'user_id'        => auth()->id(),
            ]);

            ThermalShock::where('id', $row['id'])->update($updateData);
        }

        return redirect()->route('thermalshock.index')->with('message', count($request->records) . ' data Thermal Shock berhasil diperbarui.');
    }

    public function getExportData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        // 1. Ambil data mentah dari database beserta relasinya
        $records = ThermalShock::with(['thermalPintu', 'user', 'oven', 'customer', 'tinggiFormer', 'jamKeluarOven'])
            ->whereBetween('hari_tgl', [$request->start_date, $request->end_date])
            ->orderBy('hari_tgl', 'asc')
            ->orderBy('posisi_former', 'asc')
            ->get();

        // 2. Format ulang struktur object JSON-nya sebelum dikirim ke frontend
        $formattedRecords = $records->map(function ($record) {
            return [
                'id' => $record->id,
                'hari_tgl' => $record->hari_tgl,

                // Relasi Pintu & User
                'thermal_pintu' => $record->thermalPintu ? [
                    'thermal_pintu' => $record->thermalPintu->thermal_pintu
                ] : null,
                'user' => $record->user ? [
                    'name' => $record->user->name
                ] : null,

                // Parameter 180
                'hasil_test_180' => $record->hasil_test_180,
                'hasil_180'      => $record->hasil_180,
                'suhu_awal_180' => $record->suhu_awal_180,
                'suhu_display_180' => $record->suhu_display_180,
                'suhu_actual_180' => $record->suhu_actual_180,
                'suhu_air_180' => $record->suhu_air_180,
                'jam_awal_proses_180' => $record->jam_awal_proses_180,
                'jam_capai_suhu_180' => $record->jam_capai_suhu_180,
                'jam_mulai_tembak_180' => $record->jam_mulai_tembak_180,
                'jam_selesai_tembak_180' => $record->jam_selesai_tembak_180,

                // Parameter 200
                'hasil_test_200' => $record->hasil_test_200,
                'hasil_200'      => $record->hasil_200,
                'suhu_awal_200' => $record->suhu_awal_200,
                'suhu_display_200' => $record->suhu_display_200,
                'suhu_actual_200' => $record->suhu_actual_200,
                'suhu_air_200' => $record->suhu_air_200,
                'jam_awal_proses_200' => $record->jam_awal_proses_200,
                'jam_capai_suhu_200' => $record->jam_capai_suhu_200,
                'jam_mulai_tembak_200' => $record->jam_mulai_tembak_200,
                'jam_selesai_tembak_200' => $record->jam_selesai_tembak_200,

                // Data Produk Manufaktur
                'kode_bakar' => $record->kode_bakar,
                'kode_tanah' => $record->kode_tanah,
                'oven' => $record->oven ? [
                    'oven' => $record->oven->oven // Di frontend Anda panggil item.oven?.oven
                ] : null,
                'customer' => $record->customer ? [
                    'customer' => $record->customer->customer,
                    'model' => $record->customer->model,
                    'size' => $record->customer->size,
                    'spesifikasi' => $record->customer->spesifikasi,
                ] : null,

                // Di frontend: item.tinggi_former || item.tinggiFormer
                'tinggi_former' => $record->tinggiFormer ? [
                    'tinggi_former' => $record->tinggiFormer->tinggi_former
                ] : null,

                // Di frontend: item.jam_keluar_oven || item.jamKeluarOven
                'jam_keluar_oven' => $record->jamKeluarOven ? [
                    'jam_keluar_oven' => $record->jamKeluarOven->jam_keluar_oven
                ] : null,

                'sampel' => $record->sampel,
                'berat_former' => $record->berat_former,
                'tanggal_keluar_oven' => $record->tanggal_keluar_oven,
                'tgl_produksi' => $record->tgl_produksi,
                'posisi_former' => $record->posisi_former,
                'keterangan' => $record->keterangan,
            ];
        });

        // 3. Kembalikan dalam bentuk JSON response biasa seperti kemauan frontend
        return response()->json($formattedRecords);
    }


    public function bulkEdit200(Request $request)
    {
        $ids = $request->has('ids') ? explode(',', $request->ids) : [];

        // Mengambil data seminimal mungkin hanya untuk verifikasi list di halaman edit massal
        $thermalshocks = ThermalShock::with(['customer', 'thermalPintu'])
            ->whereIn('id', $ids)
            ->orderBy('posisi_former', 'asc')
            ->get();

        return Inertia::render('ThermalShock/BulkEditSuhu200', [
            'thermalshocks' => $thermalshocks,
            'selectedIds' => $ids
        ]);
    }

    public function bulkUpdate200(Request $request)
    {
        $request->validate([
            'ids'                    => 'required|array',
            'ids.*'                  => 'exists:thermal_shock,id',
            'suhu_awal_200'          => 'required|integer',
            'suhu_display_200'       => 'required|integer',
            'suhu_actual_200'        => 'required|integer',
            'suhu_air_200'           => 'required|string',
            'jam_awal_proses_200'    => 'nullable|string',
            'jam_capai_suhu_200'     => 'nullable|string',
            'jam_mulai_tembak_200'   => 'nullable|string',
            'jam_selesai_tembak_200' => 'nullable|string',
        ]);

        $data = [
            'suhu_awal_200'          => $request->suhu_awal_200,
            'suhu_display_200'       => $request->suhu_display_200,
            'suhu_actual_200'        => $request->suhu_actual_200,
            'suhu_air_200'           => $request->suhu_air_200 ?: '-',
            'jam_awal_proses_200'    => $request->jam_awal_proses_200 ?: '00:00:00',
            'jam_capai_suhu_200'     => $request->jam_capai_suhu_200 ?: '00:00:00',
            'jam_mulai_tembak_200'   => $request->jam_mulai_tembak_200 ?: '00:00:00',
            'jam_selesai_tembak_200' => $request->jam_selesai_tembak_200 ?: '00:00:00',
            'user_id'                => auth()->id(),
        ];

        // Tambahkan detik (:00) jika format frontend HH:mm agar lolos tipe data TIME database
        $timeFields = ['jam_awal_proses_200', 'jam_capai_suhu_200', 'jam_mulai_tembak_200', 'jam_selesai_tembak_200'];
        foreach ($timeFields as $field) {
            if (!empty($data[$field]) && strlen($data[$field]) === 5) {
                $data[$field] .= ':00';
            }
        }

        // Jalankan update massal sekaligus
        ThermalShock::whereIn('id', $request->ids)->update($data);

        return redirect()->route('thermalshock.index')
            ->with('message', count($request->ids) . ' data Parameter & Waktu Suhu 200°C berhasil diperbarui sekaligus.');
    }

    public function bulkEdit180(Request $request)
    {
        $ids = $request->has('ids') ? explode(',', $request->ids) : [];

        // Mengambil data seminimal mungkin untuk verifikasi list di halaman edit massal
        $thermalshocks = ThermalShock::with(['customer', 'thermalPintu'])
            ->whereIn('id', $ids)
            ->orderBy('posisi_former', 'asc')
            ->get();

        return Inertia::render('ThermalShock/BulkEditSuhu180', [
            'thermalshocks' => $thermalshocks,
            'selectedIds' => $ids
        ]);
    }

    public function bulkUpdate180(Request $request)
    {
        $request->validate([
            'ids'                    => 'required|array',
            'ids.*'                  => 'exists:thermal_shock,id',
            'suhu_awal_180'          => 'required|integer',
            'suhu_display_180'       => 'required|integer',
            'suhu_actual_180'        => 'required|integer',
            'suhu_air_180'           => 'required|string',
            'jam_awal_proses_180'    => 'nullable|string',
            'jam_capai_suhu_180'     => 'nullable|string',
            'jam_mulai_tembak_180'   => 'nullable|string',
            'jam_selesai_tembak_180' => 'nullable|string',
        ]);

        $data = [
            'suhu_awal_180'          => $request->suhu_awal_180,
            'suhu_display_180'       => $request->suhu_display_180,
            'suhu_actual_180'        => $request->suhu_actual_180,
            'suhu_air_180'           => $request->suhu_air_180 ?: '-',
            'jam_awal_proses_180'    => $request->jam_awal_proses_180 ?: '00:00:00',
            'jam_capai_suhu_180'     => $request->jam_capai_suhu_180 ?: '00:00:00',
            'jam_mulai_tembak_180'   => $request->jam_mulai_tembak_180 ?: '00:00:00',
            'jam_selesai_tembak_180' => $request->jam_selesai_tembak_180 ?: '00:00:00',
            'user_id'                => auth()->id(),
        ];

        // Tambahkan detik (:00) jika format frontend HH:mm agar sesuai tipe data TIME database
        $timeFields = ['jam_awal_proses_180', 'jam_capai_suhu_180', 'jam_mulai_tembak_180', 'jam_selesai_tembak_180'];
        foreach ($timeFields as $field) {
            if (!empty($data[$field]) && strlen($data[$field]) === 5) {
                $data[$field] .= ':00';
            }
        }

        // Jalankan update massal sekaligus
        ThermalShock::whereIn('id', $request->ids)->update($data);

        return redirect()->route('thermalshock.index')
            ->with('message', count($request->ids) . ' data Parameter & Waktu Suhu 180°C berhasil diperbarui sekaligus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:thermal_shock,id',
        ]);

        // Eksekusi penghapusan massal
        ThermalShock::whereIn('id', $request->ids)->delete();

        return redirect()->route('thermalshock.index')
            ->with('message', count($request->ids) . ' data Thermal Shock berhasil dihapus sekaligus.');
    }


    public function menuTembak()
    {
        $pintus = ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get();

        $antreanPintu = $pintus->map(function ($pintu) {
            // Ambil daftar sesi yang masih punya antrean
            $sesiList = ThermalShock::where('thermal_pintu_id', $pintu->id)
                ->where(function($q) {
                    $q->where('hasil_test_180', 'Belum Tes')
                      ->orWhere('hasil_test_200', 'Belum Tes');
                })
                ->selectRaw('COALESCE(sesi, "Sesi Default") as sesi')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('sesi')
                ->orderByRaw('MIN(created_at) asc')
                ->get()
                ->toArray();

            return [
                'id' => $pintu->id,
                'thermal_pintu' => $pintu->thermal_pintu,
                'sesi_list' => $sesiList,
                'total_antrean' => collect($sesiList)->sum('total'),
            ];
        });

        return Inertia::render('ThermalShock/MenuTembak', [
            'antreanPintu' => $antreanPintu
        ]);
    }

    public function pintuAntrean($pintu_id, $sesi = null)
    {
        $query = ThermalShock::where('thermal_pintu_id', $pintu_id)
            ->where(function($q) {
                $q->where('hasil_test_180', 'Belum Tes')
                  ->orWhere('hasil_test_200', 'Belum Tes');
            });

        if ($sesi && $sesi !== 'Sesi Default') {
            $query->where('sesi', $sesi);
        } elseif ($sesi === 'Sesi Default') {
            $query->whereNull('sesi');
        }

        $ids = $query->orderBy('posisi_former', 'asc')
            ->pluck('id')
            ->toArray();

        if (empty($ids)) {
            return redirect()->route('thermalshock.menuTembak')
                ->with('message', 'Tidak ada antrean di sesi ini.');
        }

        return redirect()->route('thermalshock.bulkEdit', ['ids' => implode(',', $ids)]);
    }


    public function strukFilter()
    {
        $ovens = \App\Models\Oven::select('id', 'oven')->orderBy('oven')->get();
        return Inertia::render('ThermalShock/StrukFilter', [
            'ovens' => $ovens,
        ]);
    }

    public function strukFilterProcess(Request $request)
    {
        $query = ThermalShock::query();
        if ($request->filled('tanggal_keluar_oven')) $query->where('tanggal_keluar_oven', $request->tanggal_keluar_oven);
        if ($request->filled('oven_id')) $query->where('oven_id', $request->oven_id);
        if ($request->filled('kode_bakar')) $query->where('kode_bakar', $request->kode_bakar);
        if ($request->filled('sampel')) $query->where('sampel', 'like', "%{$request->sampel}%");
        $ids = $query->orderBy('posisi_former')->pluck('id')->toArray();
        if (empty($ids)) return redirect()->route('thermalshock.strukFilter')->with('message', 'Tidak ada data');
        return redirect()->route('thermalshock.strukRingkasan', ['ids' => implode(',', $ids)]);
    }

    public function strukRingkasan(Request $request)
    {
        $ids = $request->has('ids') ? explode(',', $request->ids) : [];

        $records = ThermalShock::with(['customer', 'thermalPintu', 'oven', 'jamKeluarOven'])
            ->whereIn('id', $ids)
            ->orderBy('posisi_former', 'asc')
            ->get();

        return Inertia::render('ThermalShock/StrukRingkasan', [
            'records' => $records,
            'tanggal' => now()->translatedFormat('d F Y H:i'),
        ]);
    }



}
