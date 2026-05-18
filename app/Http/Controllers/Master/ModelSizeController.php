<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ModelSize;
use App\Models\Customer;

class ModelSizeController extends Controller
{
    public function index(Request $request)
    {
        $modelsizes = ModelSize::query()
            ->with('customer')
            ->when($request->search, function ($query, $search) {
                $query->where('modelsize', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('customer', 'like', "%{$search}%");
                      });
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
        return Inertia::render('Master/ModelSize/Create', [
            'customers' => Customer::select('id', 'customer')->orderBy('customer')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'modelsize' => 'required|string|max:255',
        ], [
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists' => 'Customer tidak valid.',
            'modelsize.required' => 'Model/Size wajib diisi.',
        ]);

        ModelSize::create($request->only(['customer_id', 'modelsize']));

        return redirect()->route('modelsize.index')->with('message', 'Data model/size berhasil ditambahkan.');
    }

    public function edit(ModelSize $modelsize)
    {
        return Inertia::render('Master/ModelSize/Edit', [
            'modelsize' => $modelsize,
            'customers' => Customer::select('id', 'customer')->orderBy('customer')->get()
        ]);
    }

    public function update(Request $request, ModelSize $modelsize)
    {
        $request->validate([
            'customer_id' => 'required|exists:customer,id',
            'modelsize' => 'required|string|max:255',
        ], [
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists' => 'Customer tidak valid.',
            'modelsize.required' => 'Model/Size wajib diisi.',
        ]);

        $modelsize->update($request->only(['customer_id', 'modelsize']));

        return redirect()->route('modelsize.index')->with('message', 'Data model/size berhasil diperbarui.');
    }

    public function destroy(ModelSize $modelsize)
    {
        $modelsize->delete();
        return redirect()->route('modelsize.index')->with('message', 'Data model/size berhasil dihapus.');
    }
}
