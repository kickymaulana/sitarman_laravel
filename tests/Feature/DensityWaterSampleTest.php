<?php

namespace Tests\Feature;

use App\Models\DensityWaterSample;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DensityWaterSampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_index_but_cannot_write(): void
    {
        $user = User::factory()->create(['username' => 'user-viewer']);
        $user->assignRole(Role::create(['name' => 'User', 'guard_name' => 'web']));

        $this->actingAs($user)
            ->get('/density-water-sample')
            ->assertOk();

        $this->actingAs($user)
            ->get('/density-water-sample/create')
            ->assertForbidden();
    }

    public function test_admin_can_create_stage_one_with_automatic_daily_sequence(): void
    {
        $admin = $this->userWithRole('admin');

        $payload = [
            'tgl_test' => '2026-11-14',
            'kode_sample' => 'S-001',
            'no_open' => 'OPEN-01',
            'ketebalan' => '1.25',
            'density' => '0.98',
        ];

        $this->actingAs($admin)->post('/density-water-sample', $payload)->assertRedirect();
        $this->actingAs($admin)->post('/density-water-sample', [
            ...$payload,
            'kode_sample' => 'S-002',
        ])->assertRedirect();

        $this->assertDatabaseHas('density_water_sample', [
            'tgl_test' => '2026-11-14',
            'kode_sample' => 'S-001',
            'urutan' => 1,
            'created_by' => $admin->id,
            'status' => 'belum_lengkap',
        ]);
        $this->assertDatabaseHas('density_water_sample', [
            'tgl_test' => '2026-11-14',
            'kode_sample' => 'S-002',
            'urutan' => 2,
        ]);
    }

    public function test_stage_one_can_be_created_without_no_open(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post('/density-water-sample', [
            'tgl_test' => '2026-11-14',
            'kode_sample' => 'S-TANPA-OPEN',
        ])->assertRedirect();

        $this->assertDatabaseHas('density_water_sample', [
            'kode_sample' => 'S-TANPA-OPEN',
            'no_open' => null,
        ]);
    }

    public function test_operator_can_complete_stage_two(): void
    {
        $creator = $this->userWithRole('admin');
        $operator = $this->userWithRole('Operator');
        $sample = $this->sample($creator);

        $this->actingAs($operator)->put("/density-water-sample/{$sample->id}/complete", [
            'perlakuan' => 'Normal',
            'palm_wo' => '1.123',
            'palm_wa' => '2.234',
            'palm_water' => '3.345',
            'spec' => 'Standar A',
            'mulai_proses' => '08:00',
            'selesai_proses' => '09:00',
            'temp_air' => 30,
        ])->assertRedirect();

        $this->assertDatabaseHas('density_water_sample', [
            'id' => $sample->id,
            'status' => 'selesai',
            'completed_by' => $operator->id,
            'palm_wo' => '1.123',
            'temp_air' => 30,
        ]);
    }

    public function test_completion_page_uses_latest_process_values_as_defaults(): void
    {
        $admin = $this->userWithRole('admin');
        DensityWaterSample::create([
            'tgl_test' => '2026-11-13',
            'urutan' => 1,
            'kode_sample' => 'S-LAMA',
            'no_open' => 'OPEN-LAMA',
            'spec' => 'Standar Terakhir',
            'mulai_proses' => '07:30',
            'selesai_proses' => '08:30',
            'temp_air' => 28,
            'status' => 'selesai',
            'created_by' => $admin->id,
            'completed_by' => $admin->id,
        ]);
        $sample = $this->sample($admin);

        $this->actingAs($admin)
            ->get("/density-water-sample/{$sample->id}/complete")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('DensityWaterSample/Complete')
                ->where('defaults.spec', 'Standar Terakhir')
                ->where('defaults.mulai_proses', '07:30')
                ->where('defaults.selesai_proses', '08:30')
                ->where('defaults.temp_air', 28));
    }

    public function test_user_can_view_report_and_export_csv_by_date(): void
    {
        $user = $this->userWithRole('User');
        $this->sample($user);

        $this->actingAs($user)
            ->get('/laporan/density-water-sample?tgl_test=2026-11-14')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('DensityWaterSample/Report')
                ->has('samples', 1)
                ->where('filters.tgl_test', '2026-11-14'));

        $this->actingAs($user)
            ->get('/laporan/density-water-sample/export?tgl_test=2026-11-14')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertHeader('content-disposition', 'attachment; filename=laporan-density-water-sample-2026-11-14.csv');
    }

    public function test_deleted_sequence_can_be_reused(): void
    {
        $admin = $this->userWithRole('admin');
        $first = $this->sample($admin, 1, 'S-001');
        $second = $this->sample($admin, 2, 'S-002');

        $this->actingAs($admin)->delete("/density-water-sample/{$first->id}")->assertRedirect();
        $this->actingAs($admin)->post('/density-water-sample', [
            'tgl_test' => '2026-11-14',
            'kode_sample' => 'S-BARU',
            'no_open' => 'OPEN-BARU',
        ])->assertRedirect();

        $this->assertDatabaseHas('density_water_sample', [
            'tgl_test' => '2026-11-14',
            'kode_sample' => 'S-BARU',
            'urutan' => 1,
        ]);
        $this->assertDatabaseHas('density_water_sample', ['id' => $second->id, 'urutan' => 2]);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['username' => fake()->unique()->userName()]);
        $user->assignRole(Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']));

        return $user;
    }

    private function sample(User $creator, int $sequence = 1, string $code = 'S-001'): DensityWaterSample
    {
        return DensityWaterSample::create([
            'tgl_test' => '2026-11-14',
            'urutan' => $sequence,
            'kode_sample' => $code,
            'no_open' => 'OPEN-'.$sequence,
            'status' => 'belum_lengkap',
            'created_by' => $creator->id,
        ]);
    }
}
