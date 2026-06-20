<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ThermalShock;
use App\Models\ThermalOven;
use App\Models\ThermalPintu;
use App\Models\Oven;
use App\Models\Customer;
use App\Models\ModelSize;
use App\Models\Spesifikasi;
use App\Models\TinggiFormer;
use App\Models\JamKeluarOven;

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $thermalshocks = ThermalShock::query()
            // Eager loading relasi yang tersisa (termasuk relasi pindahan dari produk jika dibutuhkan nanti)
            ->with(['thermalOven', 'thermalPintu', 'user'])
            ->when($request->search, function ($query, $search) {
                $query->where('hari_tgl', 'like', "%{$search}%")
                      ->orWhere('suhu_testing', 'like', "%{$search}%")
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
            // Master Utama Thermal Shock
            'thermalOvens'   => ThermalOven::select('id', 'thermal_oven')->orderBy('thermal_oven')->get(),
            'thermalPintus'  => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get(),

            // Master Tambahan hasil gabungan
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'customers'      => Customer::select('id', 'customer')->orderBy('customer')->get(), // Sesuai kolom database: customer
            'modelSizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(), // Sertakan customer_id untuk filter dependent
            'spesifikasis'   => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
            'tinggiFormers'  => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
            'jamKeluarOvens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // ... kode validasi & store tetap sama seperti sebelumnya ...
        $request->validate([
            'thermal_oven_id'      => 'required|exists:thermal_oven,id',
            'thermal_pintu_id'     => 'required|exists:thermal_pintu,id',
            'hari_tgl'             => 'required|date',
            'suhu_testing'         => 'required|in:180,200',
            'suhu_display'         => 'required|integer',
            'suhu_actual'          => 'required|integer',
            'jam_awal_proses'      => 'required',
            'jam_capai_suhu'       => 'required',
            'suhu_awal'            => 'required|integer',
            'suhu_air'             => 'required|string|max:255',
            'jam_mulai_tembak'     => 'nullable',
            'jam_selesai_tembak'   => 'nullable',

            'kode_bakar'           => 'nullable|integer',
            'kode_tanah'           => 'nullable|string|max:255',
            'oven_id'              => 'required|exists:oven,id',
            'customer_id'          => 'required|exists:customer,id',
            'modelsize_id'         => 'required|exists:modelsize,id',
            'spesifikasi_id'       => 'required|exists:spesifikasi,id',
            'tinggi_former_id'     => 'required|exists:tinggi_former,id',
            'jam_keluar_oven_id'   => 'required|exists:jam_keluar_oven,id',
            'sampel'               => 'nullable|string|max:255',
            'berat_former'         => 'required|integer',
            'tanggal_keluar_oven'  => 'required|date',
            'tgl_produksi'         => 'required|date',
            'posisi_former'        => 'required|integer',
            'hasil_test_180'       => 'required|in:OK,NG,Belum Tes',
            'hasil_test_200'       => 'required|in:OK,NG,Belum Tes',
            'keterangan'           => 'nullable|string',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['jam_mulai_tembak'] = $request->jam_mulai_tembak ?? '00:00:00';
        $data['jam_selesai_tembak'] = $request->jam_selesai_tembak ?? '00:00:00';

        ThermalShock::create($data);

        return redirect()->route('thermalshock.index')->with('message', 'Data Thermal Shock berhasil disimpan.');
    }

    public function edit(ThermalShock $thermalshock)
    {
        return Inertia::render('ThermalShock/Edit', [
            'thermalshock'   => $thermalshock,

            // Master Utama Thermal Shock
            'thermalOvens'   => ThermalOven::select('id', 'thermal_oven')->orderBy('thermal_oven')->get(),
            'thermalPintus'  => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get(),

            // Master Tambahan hasil gabungan produk lama
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),
            'customers'      => Customer::select('id', 'customer')->orderBy('customer')->get(),
            'modelSizes'     => ModelSize::select('id', 'customer_id', 'modelsize')->orderBy('modelsize')->get(),
            'spesifikasis'   => Spesifikasi::select('id', 'spesifikasi')->orderBy('spesifikasi')->get(),
            'tinggiFormers'  => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
            'jamKeluarOvens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function update(Request $request, ThermalShock $thermalshock)
    {
        $request->validate([
            // Validasi Utama
            'thermal_oven_id'      => 'required|exists:thermal_oven,id',
            'thermal_pintu_id'     => 'required|exists:thermal_pintu,id',
            'hari_tgl'             => 'required|date',
            'suhu_testing'         => 'required|in:180,200',
            'suhu_display'         => 'required|integer',
            'suhu_actual'          => 'required|integer',
            'jam_awal_proses'      => 'required',
            'jam_capai_suhu'       => 'required',
            'suhu_awal'            => 'required|integer',
            'suhu_air'             => 'required|string|max:255',
            'jam_mulai_tembak'     => 'nullable',
            'jam_selesai_tembak'   => 'nullable',

            // Validasi Gabungan Produk
            'kode_bakar'           => 'nullable|integer',
            'kode_tanah'           => 'nullable|string|max:255',
            'oven_id'              => 'required|exists:oven,id',
            'customer_id'          => 'required|exists:customer,id',
            'modelsize_id'         => 'required|exists:modelsize,id',
            'spesifikasi_id'       => 'required|exists:spesifikasi,id',
            'tinggi_former_id'     => 'required|exists:tinggi_former,id',
            'jam_keluar_oven_id'   => 'required|exists:jam_keluar_oven,id',
            'sampel'               => 'nullable|string|max:255',
            'berat_former'         => 'required|integer',
            'tanggal_keluar_oven'  => 'required|date',
            'tgl_produksi'         => 'required|date',
            'posisi_former'        => 'required|integer',
            'hasil_test_180'       => 'required|in:OK,NG,Belum Tes',
            'hasil_test_200'       => 'required|in:OK,NG,Belum Tes',
            'keterangan'           => 'nullable|string',
        ]);

        $data = $request->all();
        $data['jam_mulai_tembak'] = $request->jam_mulai_tembak ?? '00:00:00';
        $data['jam_selesai_tembak'] = $request->jam_selesai_tembak ?? '00:00:00';

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
            'ids'          => 'required|array',
            'ids.*'        => 'exists:thermal_shock,id',
            'target_suhu'  => 'required|in:180,200' // Validasi target suhu dari user
        ]);

        $ids = $request->ids;
        $targetSuhu = $request->target_suhu;

        foreach ($ids as $id) {
            $thermalshock = ThermalShock::find($id);

            if ($thermalshock) {
                $newRecord = $thermalshock->replicate();

                // Set suhu_testing sesuai dengan pilihan user di AlertDialog
                $newRecord->suhu_testing = $targetSuhu;

                // Reset field hasil test dan keterangan ke default
                $newRecord->hasil_test_180 = 'Belum Tes';
                $newRecord->hasil_test_200 = 'Belum Tes';
                $newRecord->keterangan     = '-';

                // Set operator ke user yang sedang login saat ini
                $newRecord->user_id = auth()->id();

                $newRecord->save();
            }
        }

        return redirect()->route('thermalshock.index')->with('message', count($ids) . " data Thermal Shock berhasil di-copy ke target suhu {$targetSuhu}°C.");
    }

    public function bulkEdit(Request $request)
    {
        // Ambil string ID dari query parameter (contoh: ?ids=1,2,3)
        $ids = $request->has('ids') ? explode(',', $request->ids) : [];

        // Ambil datanya dan urutkan berurutan mulai dari posisi_former terkecil
        $thermalshocks = ThermalShock::with(['customer', 'modelSize'])
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
        // UBAH: Ganti validasi dari 'data' menjadi 'records'
        $request->validate([
            'records'                 => 'required|array',
            'records.*.id'            => 'required|exists:thermal_shock,id',
            'records.*.hasil_test_180' => 'required|in:OK,NG,Belum Tes',
            'records.*.hasil_test_200' => 'required|in:OK,NG,Belum Tes',
            'records.*.keterangan'     => 'nullable|string',
        ]);

        // UBAH: Loop data $request->records
        foreach ($request->records as $row) {
            ThermalShock::where('id', $row['id'])->update([
                'hasil_test_180' => $row['hasil_test_180'],
                'hasil_test_200' => $row['hasil_test_200'],
                'keterangan'     => $row['keterangan'] ?? '-',
                'user_id'        => auth()->id(),
            ]);
        }

        return redirect()->route('thermalshock.index')->with('message', count($request->records) . ' hasil test Thermal Shock berhasil diperbarui sekaligus.');
    }

    public function getExportData(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        // Tarik semua data di antara tanggal yang dipilih operator beserta relasinya
        $records = ThermalShock::with(['thermalOven', 'thermalPintu', 'user', 'oven', 'customer', 'modelSize', 'spesifikasi', 'tinggiFormer', 'jamKeluarOven'])
            ->whereBetween('hari_tgl', [$request->start_date, $request->end_date])
            ->orderBy('hari_tgl', 'asc')
            ->orderBy('posisi_former', 'asc')
            ->get();

        return response()->json($records);
    }

}
