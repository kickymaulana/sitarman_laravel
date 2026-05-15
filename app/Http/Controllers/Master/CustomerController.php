<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->search, function ($query, $search) {
                $query->where('customer', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Master/Customer/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Customer/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer' => 'required|string|max:255|unique:customer,customer',
        ], [
            'customer.required' => 'Nama customer wajib diisi.',
            'customer.unique' => 'Nama customer ini sudah terdaftar.',
        ]);

        Customer::create($request->only('customer'));

        return redirect()->route('customer.index')->with('message', 'Data customer berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Master/Customer/Edit', [
            'customer' => $customer
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'customer' => 'required|string|max:255|unique:customer,customer,' . $customer->id,
        ], [
            'customer.required' => 'Nama customer wajib diisi.',
            'customer.unique' => 'Nama customer ini sudah terdaftar.',
        ]);

        $customer->update($request->only('customer'));

        return redirect()->route('customer.index')->with('message', 'Data customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customer.index')->with('message', 'Data customer berhasil dihapus.');
    }
}
