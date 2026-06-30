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
        // Mengantisipasi timeout karena melakukan 50+ request API sekaligus
        set_time_limit(300); // Batas waktu 5 menit

        try {

            DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Untuk MySQL / MariaDB
            Customer::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $baseUrl = 'http://192.168.10.216/api/tb-spec-fqc1';
            $apiKey = 'RahasiaFQC2026';

            $currentPage = 1;
            $totalPages = 1; // Default awal sebelum membaca response API
            $totalSynced = 0;

            // Gunakan perulangan do-while untuk mengambil semua halaman
            do {
                // 1. Ambil data per halaman
                $response = Http::withHeaders([
                    'x-api-key' => $apiKey
                ])->get($baseUrl, [
                    'st'   => '1',
                    'page' => $currentPage // Mengirimkan nomor halaman saat ini
                ]);

                // 2. Validasi response API
                if (!$response->successful()) {
                    return redirect()->route('customer.index')
                        ->with('error', "Gagal mengambil data pada halaman {$currentPage}.");
                }

                $result = $response->json();

                // Pastikan format data sesuai
                if (isset($result['status']) && $result['status'] === 'success' && isset($result['data'])) {

                    // Ambil info total halaman jika ada di dalam meta objek
                    if (isset($result['meta']['total_pages'])) {
                        $totalPages = (int) $result['meta']['total_pages'];
                    }


                    foreach ($result['data'] as $item) {
                        $apiId = (int) $item['id'];

                        // Gunakan create murni untuk melihat apakah terjadi error Duplikat ID
                        Customer::create([
                            'id'          => $apiId,
                            'customer'    => $item['customer'],
                            'model'       => $item['model'],
                            'spesifikasi' => $item['spesifikasi'] ?? '',
                            'size'        => $item['size'] ?? '',
                        ]);

                        $totalSynced++;
                    }
                } else {
                    // Jika di tengah jalan struktur API rusak atau kosong
                    break;
                }

                // Naikkan halaman ke halaman berikutnya
                $currentPage++;

            } while ($currentPage <= $totalPages); // Looping terus selama halaman saat ini tidak melebihi total_pages

            if ($totalSynced > 0) {
                return redirect()->route('customer.index')
                    ->with('message', "Sinkronisasi selesai! Berhasil memproses {$totalSynced} data dari {$totalPages} halaman.");
            }

            return redirect()->route('customer.index')
                ->with('error', 'Format respons API tidak sesuai atau data kosong.');

        } catch (\Exception $e) {
            return redirect()->route('customer.index')
                ->with('error', 'Gagal terhubung ke server API: ' . $e->getMessage());
        }
    }

}
