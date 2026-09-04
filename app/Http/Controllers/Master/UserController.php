<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $users = User::query()
            ->select('id', 'name', 'username', 'email', 'nik', 'is_approved', 'requested_role', 'created_at')
            ->with(['roles:id,name'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // Sangat penting agar filter pencarian tidak hilang saat klik page 2

        return Inertia::render('Master/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']) // Kirim balik input pencarian ke frontend
        ]);
    }

    public function create()
    {
        return Inertia::render('Master/Users/Create', [
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'role' => 'required|exists:roles,name',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nik' => 'nullable|string|max:50|unique:users',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nik' => $request->nik,
        ]);

        // Assign Role Spatie
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }


    public function show(User $user)
    {

        return Inertia::render('Master/Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'nik' => $user->nik,
                'email' => $user->email,
                'is_approved' => (bool) $user->is_approved,
                'requested_role' => $user->requested_role,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Master/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'nik' => $user->nik,
                'email' => $user->email,
                // Ambil nama role pertama (asumsi user cuma punya 1 role)
                'role' => $user->getRoleNames()->first(),
            ],
            // Kirim daftar semua role ke Vue
            'roles' => Role::select('id', 'name')->get(),
        ]);
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'exists:roles,name'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'nik' => ['nullable', 'string', 'max:50', 'unique:users,nik,' . $user->id],
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'nik' => $request->nik,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Sync Role Spatie
        $user->syncRoles($request->role);

        return redirect()
            ->route('users.show', $user->id)
            ->with('success', 'User dan Role berhasil diperbarui.');
    }

    public function approve(User $user)
    {
        if ($user->is_approved) {
            return redirect()->route('users.index')->with('message', 'Akun user ini sudah aktif.');
        }

        $user->update(['is_approved' => true]);

        if ($user->requested_role && Role::where('name', $user->requested_role)->exists()) {
            $user->syncRoles($user->requested_role);
        }

        return redirect()->route('users.index')->with('success', 'Akun berhasil diaktifkan.');
    }

    public function destroy(User $user)
    {
        // Pastikan user tidak menghapus dirinya sendiri (Opsional tapi penting!)
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus selamanya.');
    }
}
