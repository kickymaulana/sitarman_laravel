<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WaterAbsorption;

class WaterAbsorptionController extends Controller
{
    public function index(Request $request)
    {
        $waterabsorptions = WaterAbsorption::query()
            ->with(['produk_wa','user'])
            ->when($request->search, function ($query, $search) {
                $query->where('tgl_test', 'like', "%{$search}%")
                      ->orWhere('spec', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('WaterAbsorption/Index', [
            'waterabsorptions' => $waterabsorptions,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('WaterAbsorption/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_test'       => 'required|date',
            'spec'           => 'nullable|string|max:255',
            'mulai_proses'   => 'nullable',
            'selesai_proses' => 'nullable',
            'temp_air'       => 'nullable|integer',
        ], [
            'tgl_test.required'       => 'Tanggal test wajib diisi.',
            'mulai_proses.required'   => 'Jam mulai proses wajib diisi.',
            'selesai_proses.required' => 'Jam selesai proses wajib diisi.',
            'temp_air.required'       => 'Temperatur air wajib diisi.',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['spec'] = $request->spec ?? '-';
        $data['mulai_proses'] = $request->mulai_proses ?? '00:00:00';
        $data['selesai_proses'] = $request->selesai_proses ?? '00:00:00';
        $data['temp_air'] = $request->temp_air ?? 0;

        WaterAbsorption::create($data);

        return redirect()->route('waterabsorption.index')->with('message', 'Data Water Absorption berhasil ditambahkan.');
    }

    public function show(WaterAbsorption $waterabsorption)
    {
        $waterabsorption->load(['produk_wa', 'user']);

        return Inertia::render('WaterAbsorption/Show', [
            'waterabsorption' => $waterabsorption
        ]);
    }

    public function edit(WaterAbsorption $waterabsorption)
    {
        return Inertia::render('WaterAbsorption/Edit', [
            'waterabsorption' => $waterabsorption
        ]);
    }

    public function update(Request $request, WaterAbsorption $waterabsorption)
    {
        $request->validate([
            'tgl_test'       => 'required|date',
            'spec'           => 'nullable|string|max:255',
            'mulai_proses'   => 'required',
            'selesai_proses' => 'required',
            'temp_air'       => 'required|integer',
        ]);

        $data = $request->all();
        $data['spec'] = $request->spec ?? '-';

        $waterabsorption->update($data);

        return redirect()->route('waterabsorption.index')->with('message', 'Data Water Absorption berhasil diperbarui.');
    }

    public function destroy(WaterAbsorption $waterabsorption)
    {
        $waterabsorption->delete();
        return redirect()->route('waterabsorption.index')->with('message', 'Data Water Absorption berhasil dihapus.');
    }
}
