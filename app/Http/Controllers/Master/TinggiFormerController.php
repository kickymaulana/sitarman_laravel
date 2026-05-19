<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TinggiFormer;

class TinggiFormerController extends Controller
{
    public function index(Request $request)
    {
        $tinggiformers = TinggiFormer::query()
            ->when($request->search, function ($query, $search) {
                $query->where('tinggi_former', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/TinggiFormer/Index', [
            // Sesuai contoh index, passing data dalam objek bungkus pagination
            'tinggiformers' => $tinggiformers,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/TinggiFormer/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tinggi_former' => 'required|integer|min:1|unique:tinggi_former,tinggi_former',
        ], [
            'tinggi_former.required' => 'Tinggi Former wajib diisi.',
            'tinggi_former.integer'  => 'Tinggi Former harus berupa angka.',
            'tinggi_former.unique'   => 'Tinggi Former ini sudah terdaftar.',
        ]);

        TinggiFormer::create($request->only('tinggi_former'));

        return redirect()->route('tinggiformer.index')->with('message', 'Data tinggi former berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Menggunakan findOrFail agar aman jika data tidak ditemukan
        $tinggiformer = TinggiFormer::findOrFail($id);

        return Inertia::render('Master/TinggiFormer/Edit', [
            'tinggiformer' => $tinggiformer
        ]);
    }

    public function update(Request $request, $id)
    {
        $tinggiformer = TinggiFormer::findOrFail($id);

        $request->validate([
            'tinggi_former' => 'required|integer|min:1|unique:tinggi_former,tinggi_former,' . $tinggiformer->id,
        ], [
            'tinggi_former.required' => 'Tinggi Former wajib diisi.',
            'tinggi_former.integer'  => 'Tinggi Former harus berupa angka.',
            'tinggi_former.unique'   => 'Tinggi Former ini sudah terdaftar.',
        ]);

        $tinggiformer->update($request->only('tinggi_former'));

        return redirect()->route('tinggiformer.index')->with('message', 'Data tinggi former berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tinggiformer = TinggiFormer::findOrFail($id);
        $tinggiformer->delete();

        return redirect()->route('tinggiformer.index')->with('message', 'Data tinggi former berhasil dihapus.');
    }
}
