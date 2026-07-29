<?php

namespace App\Http\Controllers;

use App\Models\ThermalShock;
use App\Models\User;
use App\Models\ThermalPintu;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasAnyRole(['QC Manager', 'Factory Manager', 'General Manager'])) {
            return redirect()->route('persetujuan.manager.index');
        }

        if ($user->hasAnyRole(['Manager', 'Supervisor', 'Leader'])) {
            return redirect()->route('tugas.produksi.index');
        }

        if ($user->hasRole('Operator')) {
            return redirect()->route('thermalshock.index');
        }

        $totalSampel = ThermalShock::count();
        $ok180 = ThermalShock::where('hasil_test_180', 'OK')->count();
        $ng180 = ThermalShock::where('hasil_test_180', 'NG')->count();
        $belum180 = ThermalShock::where('hasil_test_180', 'Belum Tes')->count();
        $ok200 = ThermalShock::where('hasil_test_200', 'OK')->count();
        $belum200 = ThermalShock::where('hasil_test_200', 'Belum Tes')->count();

        $pintuStats = ThermalPintu::select('id', 'thermal_pintu')
            ->withCount('thermalShockDetails')
            ->orderBy('thermal_pintu')
            ->get();

        $recentEntries = ThermalShock::with(['thermalPintu', 'user', 'customer'])
            ->latest()->take(10)->get();

        $totalUsers = User::count();

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'total_sampel' => $totalSampel,
                'ok_180' => $ok180,
                'ng_180' => $ng180,
                'belum_180' => $belum180,
                'ok_200' => $ok200,
                'belum_200' => $belum200,
                'pct_180' => $totalSampel > 0 ? round(($ok180 / $totalSampel) * 100) : 0,
                'pct_200' => $totalSampel > 0 ? round(($ok200 / $totalSampel) * 100) : 0,
            ],
            'pintuStats' => $pintuStats,
            'recentEntries' => $recentEntries,
            'totalUsers' => $totalUsers,
        ]);
    }

    public function testing()
    {
        return Inertia::render('Dashboard/Testing');
    }
}
