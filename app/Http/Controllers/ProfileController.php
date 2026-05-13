<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'user' => $request->user(), // Mengambil data user yang sedang login
        ]);
    }

    /**
     * Memperbarui informasi profil.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'whatsapp' => ['required', 'string', 'regex:/^628[0-9]{7,12}$/', 'unique:users,whatsapp,' . $user->id],
        ], [
            // Custom message agar user tidak bingung dengan regex
            'whatsapp.regex' => 'Format nomor WhatsApp harus diawali dengan 628.',
        ]);

        // Update data dasar
        $user->fill([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
        ]);

        // Cek jika email berubah, biasanya reset email_verified_at (opsional)
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
