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

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $thermalshocks = ThermalShock::query()
            // PERBAIKAN: Eager load seluruh relasi secara lengkap agar terbaca oleh Vue
            ->with(['thermalPintu', 'user', 'customer', 'oven', 'tinggiFormer', 'jamKeluarOven'])
            ->when($request->search, function ($query, $search) {
                $query->where('hari_tgl', 'like', "%{$search}%")
                      ->orWhere('hasil_test_180', 'like', "%{$search}%")
                      ->orWhere('hasil_test_200', 'like', "%{$search}%")

                      ->orWhereHas('thermalPintu', function($q) use ($search) {
                          $q->where('thermal_pintu', 'like', "%{$search}%");
                      })
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('size', 'like', "%{$search}%")
                            ->orWhere('spesifikasi', 'like', "%{$search}%");
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
            'hasil_test_180'         => 'required|in:OK,NG,Belum Tes',
            'hasil_test_200'         => 'required|in:OK,NG,Belum Tes',
            'keterangan'             => 'nullable|string',
        ]);

        // Ambil data yang sudah lolos validasi
        $data = $request->all();

        // Tambahkan user pendata
        $data['user_id'] = auth()->id();

        // Pastikan format jam kosong dikembalikan ke default database (00:00:00)
        $data['jam_mulai_tembak_180']   = $request->jam_mulai_tembak_180 ?: '00:00:00';
        $data['jam_selesai_tembak_180'] = $request->jam_selesai_tembak_180 ?: '00:00:00';
        $data['jam_mulai_tembak_200']   = $request->jam_mulai_tembak_200 ?: '00:00:00';
        $data['jam_selesai_tembak_200'] = $request->jam_selesai_tembak_200 ?: '00:00:00';

        // Kondisional opsional: Jika format jam hanya H:i dari frontend, tambahkan ':00' agar pas dengan tipe data TIME
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
            'hasil_test_180'         => 'required|in:OK,NG,Belum Tes',
            'hasil_test_200'         => 'required|in:OK,NG,Belum Tes',
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
        $thermalshocks = ThermalShock::with(['customer'])
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
            'records'                  => 'required|array',
            'records.*.id'             => 'required|exists:thermal_shock,id',
            'records.*.hasil_test_180' => 'required|in:OK,NG,Belum Tes',
            'records.*.hasil_test_200' => 'required|in:OK,NG,Belum Tes',
            'records.*.keterangan'     => 'nullable|string',
        ]);

        foreach ($request->records as $row) {
            ThermalShock::where('id', $row['id'])->update([
                'hasil_test_180' => $row['hasil_test_180'],
                'hasil_test_200' => $row['hasil_test_200'],
                'keterangan'     => $row['keterangan'] ?? '-',
                'user_id'        => auth()->id(),
            ]);
        }

        return redirect()->route('thermalshock.index')->with('message', count($request->records) . ' hasil test berhasil diperbarui.');
    }

    public function getExportData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        // PERBAIKAN: Hapus 'thermalOven' karena relasi/tabelnya sudah tidak ada
        $records = ThermalShock::with(['thermalPintu', 'user', 'oven', 'customer', 'tinggiFormer', 'jamKeluarOven'])
            ->whereBetween('hari_tgl', [$request->start_date, $request->end_date])
            ->orderBy('hari_tgl', 'asc')
            ->orderBy('posisi_former', 'asc')
            ->get();

        return response()->json($records);
    }

}
