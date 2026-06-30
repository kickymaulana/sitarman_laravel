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
use App\Models\TinggiFormer;
use App\Models\JamKeluarOven;

class ThermalShockController extends Controller
{
    public function index(Request $request)
    {
        $thermalshocks = ThermalShock::query()
            // customer di-eager load untuk menampilkan gabungan data barunya
            ->with(['thermalOven', 'thermalPintu', 'user', 'customer'])
            ->when($request->search, function ($query, $search) {
                $query->where('hari_tgl', 'like', "%{$search}%")
                      ->orWhere('suhu_testing', 'like', "%{$search}%")
                      ->orWhereHas('thermalOven', function($q) use ($search) {
                          $q->where('thermal_oven', 'like', "%{$search}%");
                      })
                      ->orWhereHas('thermalPintu', function($q) use ($search) {
                          $q->where('thermal_pintu', 'like', "%{$search}%");
                      })
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%");
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
            'thermalOvens'   => ThermalOven::select('id', 'thermal_oven')->orderBy('thermal_oven')->get(),
            'thermalPintus'  => ThermalPintu::select('id', 'thermal_pintu')->orderBy('thermal_pintu')->get(),
            'ovens'          => Oven::select('id', 'oven')->orderBy('oven')->get(),

            // Ambil data customer lengkap dengan kolom barunya
            'customers'      => Customer::select('id', 'customer', 'model', 'spesifikasi', 'size')->orderBy('customer')->get(),

            'tinggiFormers'  => TinggiFormer::select('id', 'tinggi_former')->orderBy('tinggi_former')->get(),
            'jamKeluarOvens' => JamKeluarOven::select('id', 'jam_keluar_oven')->orderBy('jam_keluar_oven')->get(),
        ]);
    }

    public function store(Request $request)
    {
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
            'thermalOvens'   => ThermalOven::select('id', 'thermal_oven')->orderBy('thermal_oven')->get(),
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
            'target_suhu'  => 'required|in:180,200'
        ]);

        foreach ($request->ids as $id) {
            $thermalshock = ThermalShock::find($id);
            if ($thermalshock) {
                $newRecord = $thermalshock->replicate();
                $newRecord->suhu_testing = $request->target_suhu;
                $newRecord->hasil_test_180 = 'Belum Tes';
                $newRecord->hasil_test_200 = 'Belum Tes';
                $newRecord->keterangan     = '-';
                $newRecord->user_id = auth()->id();
                $newRecord->save();
            }
        }

        return redirect()->route('thermalshock.index')->with('message', count($request->ids) . " data Thermal Shock berhasil di-copy.");
    }

    public function bulkEdit(Request $request)
    {
        $ids = $request->has('ids') ? explode(',', $request->ids) : [];

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

        $records = ThermalShock::with(['thermalOven', 'thermalPintu', 'user', 'oven', 'customer', 'tinggiFormer', 'jamKeluarOven'])
            ->whereBetween('hari_tgl', [$request->start_date, $request->end_date])
            ->orderBy('hari_tgl', 'asc')
            ->orderBy('posisi_former', 'asc')
            ->get();

        return response()->json($records);
    }
}
