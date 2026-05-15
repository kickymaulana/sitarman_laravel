<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ModelSize;

class ModelSizeController extends Controller
{
    public function index(Request $request)
    {
        $modelsizes = ModelSize::query()
            ->when($request->search, function ($query, $search) {
                $query->where('modelsize', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/ModelSize/Index', [
            'modelsizes' => $modelsizes,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/ModelSize/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'modelsize' => 'required|string|max:255|unique:modelsize,modelsize',
        ], [
            'modelsize.required' => 'Model/Size wajib diisi.',
            'modelsize.unique' => 'Model/Size ini sudah terdaftar.',
        ]);

        ModelSize::create($request->only('modelsize'));

        return redirect()->route('modelsize.index')->with('message', 'Data model/size berhasil ditambahkan.');
    }

    public function edit(ModelSize $modelsize)
    {
        return Inertia::render('Master/ModelSize/Edit', [
            'modelsize' => $modelsize
        ]);
    }

    public function update(Request $request, ModelSize $modelsize)
    {
        $request->validate([
            'modelsize' => 'required|string|max:255|unique:modelsize,modelsize,' . $modelsize->id,
        ], [
            'modelsize.required' => 'Model/Size wajib diisi.',
            'modelsize.unique' => 'Model/Size ini sudah terdaftar.',
        ]);

        $modelsize->update($request->only('modelsize'));

        return redirect()->route('modelsize.index')->with('message', 'Data model/size berhasil diperbarui.');
    }

    public function destroy(ModelSize $modelsize)
    {
        $modelsize->delete();
        return redirect()->route('modelsize.index')->with('message', 'Data model/size berhasil dihapus.');
    }
}
