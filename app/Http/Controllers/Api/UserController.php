<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource; // Import Resource
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil query 'search' dari URL (misal: ?search=kicky)
        $search = $request->query('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest() // Urutkan dari yang terbaru
            ->paginate(10); // Pagination 10 data per halaman

        // 2. Gunakan UserResource::collection untuk mengembalikan data
        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'whatsapp' => 'required|string|max:20',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'whatsapp' => $request->whatsapp,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return new UserResource($user);
    }

    public function show(User $user) // Menggunakan Route Model Binding agar lebih singkat
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'username' => [ // Tambahkan ini
                'sometimes', 'required', 'string',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'sometimes', 'required', 'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'whatsapp' => 'sometimes|required|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('whatsapp')) $user->whatsapp = $request->whatsapp;
        if ($request->has('username')) $user->username = $request->username;
        if ($request->filled('password')) $user->password = Hash::make($request->password);

        $user->save();

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}

