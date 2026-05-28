<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Chemical;

class ChemicalController extends Controller
{
    public function index(Request $request)
    {
        $chemicals = Chemical::query()
            ->with(['produk_chemical'])
            ->when($request->search, function ($query, $search) {
                $query->where('tgl_test', 'like', "%{$search}%")
                      ->orWhere('kode_alkali', 'like', "%{$search}%")
                      ->orWhere('kode_acid', 'like', "%{$search}%");
            })
            ->latest('tgl_test')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Chemical/Index', [
            'chemicals' => $chemicals,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Chemical/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_test'             => 'required|date',
            'kode_alkali'          => 'nullable|string|max:255',
            'alkali_jam_mulai'     => 'nullable',
            'alkali_jam_selesai'   => 'nullable',
            'kode_acid'            => 'nullable|string|max:255',
            'acid_jam_mulai'       => 'nullable',
            'acid_jam_selesai'     => 'nullable',
        ], [
            'tgl_test.required'           => 'Tanggal test wajib diisi.',
            'alkali_jam_mulai.required'   => 'Jam mulai alkali wajib diisi.',
            'alkali_jam_selesai.required' => 'Jam selesai alkali wajib diisi.',
            'acid_jam_mulai.required'     => 'Jam mulai acid wajib diisi.',
            'acid_jam_selesai.required'   => 'Jam selesai acid wajib diisi.',
        ]);

        $data = $request->all();
        if (empty($data['kode_alkali'])) { $data['kode_alkali'] = '-'; }
        if (empty($data['kode_acid'])) { $data['kode_acid'] = '-'; }

        $data['alkali_jam_mulai'] = $request->alkali_jam_mulai ?? '00:00:00';
        $data['alkali_jam_selesai'] = $request->alkali_jam_selesai ?? '00:00:00';
        $data['acid_jam_mulai'] = $request->acid_jam_mulai ?? '00:00:00';
        $data['acid_jam_selesai'] = $request->acid_jam_selesai ?? '00:00:00';


        Chemical::create($data);

        return redirect()->route('chemical.index')
            ->with('message', 'Data pengujian chemical berhasil ditambahkan.');
    }

    public function show(Chemical $chemical)
    {
        $chemical->load(['produk_chemical']);

        return Inertia::render('Chemical/Show', [
            'chemical' => $chemical
        ]);
    }

    public function edit(Chemical $chemical)
    {
        return Inertia::render('Chemical/Edit', [
            'chemical' => $chemical
        ]);
    }

    public function update(Request $request, Chemical $chemical)
    {
        $request->validate([
            'tgl_test'             => 'required|date',
            'kode_alkali'          => 'nullable|string|max:255',
            'alkali_jam_mulai'     => 'nullable',
            'alkali_jam_selesai'   => 'nullable',
            'kode_acid'            => 'nullable|string|max:255',
            'acid_jam_mulai'       => 'nullable',
            'acid_jam_selesai'     => 'nullable',
        ], [
            'tgl_test.required'           => 'Tanggal test wajib diisi.',
            'alkali_jam_mulai.required'   => 'Jam mulai alkali wajib diisi.',
            'alkali_jam_selesai.required' => 'Jam selesai alkali wajib diisi.',
            'acid_jam_mulai.required'     => 'Jam mulai acid wajib diisi.',
            'acid_jam_selesai.required'   => 'Jam selesai acid wajib diisi.',
        ]);

        $data = $request->all();
        if (empty($data['kode_alkali'])) { $data['kode_alkali'] = '-'; }
        if (empty($data['kode_acid'])) { $data['kode_acid'] = '-'; }


        $data['alkali_jam_mulai'] = $request->alkali_jam_mulai ?? '00:00:00';
        $data['alkali_jam_selesai'] = $request->alkali_jam_selesai ?? '00:00:00';
        $data['acid_jam_mulai'] = $request->acid_jam_mulai ?? '00:00:00';
        $data['acid_jam_selesai'] = $request->acid_jam_selesai ?? '00:00:00';

        $chemical->update($data);

        return redirect()->route('chemical.index')
            ->with('message', 'Data pengujian chemical berhasil diperbarui.');
    }

    public function destroy(Chemical $chemical)
    {
        $chemical->delete();

        return redirect()->route('chemical.index')
            ->with('message', 'Data pengujian chemical berhasil dihapus.');
    }
}
