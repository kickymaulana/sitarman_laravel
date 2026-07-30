<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Customer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || !$user->hasAnyRole(['admin', 'Operator'])) {
                abort(403, 'Anda tidak memiliki akses.');
            }
            return $next($request);
        });
    }

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
            'customer' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'spesifikasi' => 'required|string|max:255',
        ], [
            'customer.required' => 'Nama customer wajib diisi.',
            'customer.unique' => 'Nama customer ini sudah terdaftar.',
        ]);

        Customer::create($request->only('customer', 'model', 'size', 'spesifikasi'));

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
            'customer' => 'required|string|max:255' . $customer->id,
            'model' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'spesifikasi' => 'required|string|max:255',
        ], [
            'customer.required' => 'Nama customer wajib diisi.',
            'customer.unique' => 'Nama customer ini sudah terdaftar.',
        ]);

        $customer->update($request->only('customer', 'model', 'size', 'spesifikasi'));

        return redirect()->route('customer.index')->with('message', 'Data customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customer.index')->with('message', 'Data customer berhasil dihapus.');
    }

    public function sync()
    {
        set_time_limit(300);

        try {
            $baseUrl = 'http://192.168.10.216/api/tb-spec-fqc1';
            $apiKey = 'RahasiaFQC2026';

            $currentPage = 1;
            $totalPages = 1;
            $totalNew = 0;
            $totalUpdated = 0;
            $totalSkipped = 0;

            do {
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey
                ])->get($baseUrl, [
                    'st'   => '0',
                    'page' => $currentPage
                ]);

                if (!$response->successful()) {
                    return redirect()->route('customer.index')
                        ->with('error', "Gagal mengambil data pada halaman {$currentPage}.");
                }

                $result = $response->json();

                if (isset($result['status']) && $result['status'] === 'success' && isset($result['data'])) {

                    if (isset($result['meta']['total_pages'])) {
                        $totalPages = (int) $result['meta']['total_pages'];
                    }

                    foreach ($result['data'] as $item) {
                        $apiId = (int) $item['id'];
                        $customerData = [
                            'customer'    => $item['customer'],
                            'model'       => $item['model'],
                            'spesifikasi' => $item['spesifikasi'] ?? '',
                            'size'        => $item['size'] ?? '',
                        ];

                        $existing = Customer::find($apiId);

                        if ($existing) {
                            // Data sudah ada, cek apakah ada perubahan
                            $changed = false;
                            foreach ($customerData as $key => $value) {
                                if ((string) $existing->$key !== (string) $value) {
                                    $changed = true;
                                    break;
                                }
                            }

                            if ($changed) {
                                $existing->update($customerData);
                                $totalUpdated++;
                            } else {
                                $totalSkipped++;
                            }
                        } else {
                            // Data baru
                            $customerData['id'] = $apiId;
                            Customer::create($customerData);
                            $totalNew++;
                        }
                    }
                } else {
                    break;
                }

                $currentPage++;

            } while ($currentPage <= $totalPages);

            $msg = "Sinkronisasi selesai! Baru: {$totalNew}, Diupdate: {$totalUpdated}, Sama: {$totalSkipped} dari {$totalPages} halaman.";

            return redirect()->route('customer.index')->with('message', $msg);

        } catch (\Exception $e) {
            return redirect()->route('customer.index')
                ->with('error', 'Gagal terhubung ke server API: ' . $e->getMessage());
        }
    }

}
