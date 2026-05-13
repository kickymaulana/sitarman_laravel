<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DaftarPenggunaController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            // Mengambil field yang hanya dibutuhkan sesuai permintaan Anda
            ->select('id', 'name', 'username', 'whatsapp', 'created_at')
            ->with(['roles:id,name']) // Tetap load role dari Spatie
            ->when($request->search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('whatsapp', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('DaftarPengguna/Index', [
            'users' => $users,
            'filters' => $request->only(['search'])
        ]);
    }
}
