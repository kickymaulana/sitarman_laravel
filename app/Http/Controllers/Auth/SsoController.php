<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class SsoController extends Controller
{
    public function redirect()
    {
        $state = Str::random(40);
        session(['sso_state' => $state]);

        $query = http_build_query([
            'client_id' => config('services.sso.client_id'),
            'redirect_uri' => route('sso.callback'),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
        ]);

        return redirect()->away(config('services.sso.base_url') . '/oauth/authorize?' . $query);
    }

    public function callback(Request $request)
    {
        if (! hash_equals(session('sso_state', ''), (string) $request->query('state'))) {
            return $this->fail('State tidak valid. Silakan ulangi dari halaman login.');
        }

        $request->session()->forget('sso_state');

        if ($request->query('error')) {
            return $this->fail('Autentikasi SSO dibatalkan.');
        }

        $code = $request->query('code');
        if (! $code) {
            return $this->fail('Kode autentikasi SSO tidak ditemukan.');
        }

        $verifySsl = app()->environment('local') ? false : true;

        $tokenResponse = Http::withOptions(['verify' => $verifySsl])
            ->asForm()
            ->post(config('services.sso.base_url') . '/oauth/token', [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.sso.client_id'),
                'client_secret' => config('services.sso.client_secret'),
                'redirect_uri' => route('sso.callback'),
                'code' => $code,
            ]);

        if ($tokenResponse->failed()) {
            Log::warning('SSO token exchange gagal.', ['response' => $tokenResponse->body()]);

            return $this->fail('Gagal mendapatkan token SSO.');
        }

        $accessToken = $tokenResponse->json('access_token');

        $userResponse = Http::withOptions(['verify' => $verifySsl])
            ->withHeaders(['Authorization' => 'Bearer ' . $accessToken])
            ->get(config('services.sso.base_url') . '/api/user');

        if ($userResponse->failed()) {
            return $this->fail('Gagal mengambil data user.');
        }

        $ssoUser = $userResponse->json();

        $nik = $ssoUser['nik'] ?? null;
        if (! $nik) {
            return $this->fail('Data NIK tidak ditemukan.');
        }

        try {
            $user = User::where('nik', $nik)->first();

            if ($user) {
                $updates = ['name' => $ssoUser['name'] ?? $user->name];
                if (! empty($ssoUser['email']) && $ssoUser['email'] !== $user->email) {
                    $updates['email'] = $ssoUser['email'];
                }
                $user->update($updates);
            } else {
                $user = User::create([
                    'nik' => $nik,
                    'name' => $ssoUser['name'] ?? 'User ' . $nik,
                    'username' => $nik,
                    'email' => $ssoUser['email'] ?? Str::lower($nik) . '@sso',
                    'whatsapp' => '-',
                    'password' => Str::random(32),
                    'is_approved' => false,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('SSO sync user gagal.', ['nik' => $nik, 'error' => $e->getMessage()]);

            return $this->fail('Gagal menyiapkan akun. Hubungi Admin.');
        }

        if (! $user->is_approved) {
            if (! $user->requested_role) {
                $request->session()->put('pending_user_id', $user->id);

                return redirect()->route('sso.pending');
            }

            return $this->fail('Akun Anda belum diaktifkan. Silakan hubungi Admin.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function pendingRole(Request $request)
    {
        $userId = $request->session()->get('pending_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user || $user->is_approved || $user->requested_role) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/PendingRole', [
            'user' => [
                'name' => $user->name,
                'nik' => $user->nik,
            ],
            'roles' => Role::where('name', '!=', 'admin')->orderBy('name')->pluck('name'),
        ]);
    }

    public function submitRole(Request $request)
    {
        $userId = $request->session()->get('pending_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login');
        }

        $allowedRoles = Role::where('name', '!=', 'admin')->pluck('name')->all();

        $request->validate([
            'role' => ['required', 'string', 'in:' . implode(',', $allowedRoles)],
        ]);

        $user->update(['requested_role' => $request->role]);

        $request->session()->forget('pending_user_id');

        return redirect()->route('login')
            ->with('success', 'Permintaan role "' . $request->role . '" terkirim. Silakan tunggu aktivasi Admin.');
    }

    private function fail(string $message)
    {
        return redirect()->route('login')->withErrors(['message' => $message]);
    }
}
