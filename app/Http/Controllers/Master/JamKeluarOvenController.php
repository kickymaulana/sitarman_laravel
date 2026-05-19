<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JamKeluarOven;

class JamKeluarOvenController extends Controller
{
    public function index(Request $request)
    {
        $jamkeluarovens = JamKeluarOven::query()
            ->when($request->search, function ($query, $search) {
                $query->where('jam_keluar_oven', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/JamKeluarOven/Index', [
            'jamkeluarovens' => $jamkeluarovens,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/JamKeluarOven/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jam_keluar_oven' => 'required|date_format:H:i|unique:jam_keluar_oven,jam_keluar_oven',
        ], [
            'jam_keluar_oven.required'    => 'Jam Keluar Oven wajib diisi.',
            'jam_keluar_oven.date_format' => 'Format jam harus HH:mm.',
            'jam_keluar_oven.unique'      => 'Jam Keluar Oven ini sudah terdaftar.',
        ]);

        JamKeluarOven::create($request->only('jam_keluar_oven'));

        return redirect()->route('jamkeluaroven.index')->with('message', 'Data jam keluar oven berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jamkeluaroven = JamKeluarOven::findOrFail($id);

        // Memastikan format yang dikirim ke Vue hanya HH:mm (tanpa detik dari DB)
        $jamkeluaroven->jam_keluar_oven = date('H:i', strtotime($jamkeluaroven->jam_keluar_oven));

        return Inertia::render('Master/JamKeluarOven/Edit', [
            'jamkeluaroven' => $jamkeluaroven
        ]);
    }

    public function update(Request $request, $id)
    {
        $jamkeluaroven = JamKeluarOven::findOrFail($id);

        $request->validate([
            'jam_keluar_oven' => 'required|date_format:H:i|unique:jam_keluar_oven,jam_keluar_oven,' . $jamkeluaroven->id,
        ], [
            'jam_keluar_oven.required'    => 'Jam Keluar Oven wajib diisi.',
            'jam_keluar_oven.date_format' => 'Format jam harus HH:mm.',
            'jam_keluar_oven.unique'      => 'Jam Keluar Oven ini sudah terdaftar.',
        ]);

        $jamkeluaroven->update($request->only('jam_keluar_oven'));

        return redirect()->route('jamkeluaroven.index')->with('message', 'Data jam keluar oven berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jamkeluaroven = JamKeluarOven::findOrFail($id);
        $jamkeluaroven->delete();

        return redirect()->route('jamkeluaroven.index')->with('message', 'Data jam keluar oven berhasil dihapus.');
    }
}
