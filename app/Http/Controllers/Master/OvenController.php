<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Oven;

class OvenController extends Controller
{
    public function index(Request $request)
    {
        $ovens = Oven::query()
            ->when($request->search, function ($query, $search) {
                $query->where('oven', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Oven/Index', [
            'ovens' => $ovens,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Oven/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'oven' => 'required|string|max:255|unique:oven,oven',
        ], [
            'oven.required' => 'Oven wajib diisi.',
            'oven.unique' => 'Oven ini sudah terdaftar.',
        ]);

        Oven::create($request->only('oven'));

        return redirect()->route('oven.index')->with('message', 'Data oven berhasil ditambahkan.');
    }

    public function edit(Oven $oven)
    {
        return Inertia::render('Master/Oven/Edit', [
            'oven' => $oven
        ]);
    }

    public function update(Request $request, Oven $oven)
    {
        $request->validate([
            'oven' => 'required|string|max:255|unique:oven,oven,' . $oven->id,
        ], [
            'oven.required' => 'Oven wajib diisi.',
            'oven.unique' => 'Oven ini sudah terdaftar.',
        ]);

        $oven->update($request->only('oven'));

        return redirect()->route('oven.index')->with('message', 'Data oven berhasil diperbarui.');
    }

    public function destroy(Oven $oven)
    {
        $oven->delete();
        return redirect()->route('oven.index')->with('message', 'Data oven berhasil dihapus.');
    }
}
