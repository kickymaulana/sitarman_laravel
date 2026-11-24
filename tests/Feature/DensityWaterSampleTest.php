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

    public function test_new_session_is_created_automatically_and_sequence_restarts(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->post('/density-water-sample', [
            'tgl_test' => '2026-11-14',
            'kode_sample' => 'S-SESI-1',
        ])->assertRedirect();

        $this->actingAs($admin)->post('/density-water-sample', [
            'tgl_test' => '2026-11-14',
            'sesi_ke' => 2,
            'kode_sample' => 'S-SESI-2',
        ])->assertRedirect();

        $this->assertDatabaseHas('density_water_sample', [
            'kode_sample' => 'S-SESI-1',
            'sesi_ke' => 1,
            'urutan' => 1,
        ]);
        $this->assertDatabaseHas('density_water_sample', [
            'kode_sample' => 'S-SESI-2',
            'sesi_ke' => 2,
            'urutan' => 1,
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

    public function test_empty_session_uses_latest_session_or_starts_at_one(): void
    {
        $admin = $this->userWithRole('admin');

        foreach (['', null] as $index => $session) {
            $this->actingAs($admin)->post('/density-water-sample', [
                'tgl_test' => '2026-11-24',
                'sesi_ke' => $session,
                'kode_sample' => 'S-KOSONG-'.$index,
            ])->assertSessionHasNoErrors()->assertRedirect();

            $this->assertDatabaseHas('density_water_sample', [
                'kode_sample' => 'S-KOSONG-'.$index,
                'sesi_ke' => 1,
                'urutan' => $index + 1,
            ]);
        }

        $this->actingAs($admin)->post('/density-water-sample', [
            'tgl_test' => '2026-11-24',
            'sesi_ke' => 2,
            'kode_sample' => 'S-SESI-DUA',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->actingAs($admin)->post('/density-water-sample', [
            'tgl_test' => '2026-11-24',
            'sesi_ke' => null,
            'kode_sample' => 'S-LANJUT-SESI-DUA',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('density_water_sample', [
            'kode_sample' => 'S-LANJUT-SESI-DUA',
            'sesi_ke' => 2,
            'urutan' => 2,
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

    public function test_operator_can_bulk_complete_all_samples_for_a_date(): void
    {
        $creator = $this->userWithRole('admin');
        $operator = $this->userWithRole('Operator');
        $first = $this->sample($creator, 1, 'S-001');
        $second = $this->sample($creator, 2, 'S-002');

        $this->actingAs($operator)
            ->get('/density-water-sample/bulk-complete?tgl_test=2026-11-14')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('DensityWaterSample/BulkComplete')
                ->has('samples', 2));

        $this->actingAs($operator)->put('/density-water-sample/bulk-complete', [
            'tgl_test' => '2026-11-14',
            'sesi_ke' => 1,
            'spec' => 'Standar Bulk',
            'mulai_proses' => '08:00',
            'selesai_proses' => '09:00',
            'temp_air' => 30,
            'records' => [
                ['id' => $first->id, 'perlakuan' => 'A', 'palm_wo' => '1.111'],
                ['id' => $second->id, 'perlakuan' => 'B', 'palm_wo' => '2.222'],
            ],
        ])->assertRedirect('/density-water-sample?tgl_test=2026-11-14&sesi_ke=1');

        $this->assertDatabaseHas('density_water_sample', [
            'id' => $first->id,
            'perlakuan' => 'A',
            'palm_wo' => '1.111',
            'spec' => 'Standar Bulk',
            'status' => 'selesai',
            'completed_by' => $operator->id,
        ]);
        $this->assertDatabaseHas('density_water_sample', [
            'id' => $second->id,
            'perlakuan' => 'B',
            'palm_wo' => '2.222',
            'temp_air' => 30,
            'status' => 'selesai',
            'completed_by' => $operator->id,
        ]);
    }

    public function test_bulk_completion_rejects_samples_from_another_date(): void
    {
        $admin = $this->userWithRole('admin');
        $first = $this->sample($admin);
        $otherDate = DensityWaterSample::create([
            'tgl_test' => '2026-11-15',
            'sesi_ke' => 1,
            'urutan' => 1,
            'kode_sample' => 'S-LAIN',
            'status' => 'belum_lengkap',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->from('/density-water-sample/bulk-complete?tgl_test=2026-11-14')
            ->put('/density-water-sample/bulk-complete', [
                'tgl_test' => '2026-11-14',
                'sesi_ke' => 1,
                'records' => [
                    ['id' => $first->id],
                    ['id' => $otherDate->id],
                ],
            ])
            ->assertRedirect('/density-water-sample/bulk-complete?tgl_test=2026-11-14')
            ->assertSessionHasErrors('records');

        $this->assertDatabaseHas('density_water_sample', ['id' => $first->id, 'status' => 'belum_lengkap']);
        $this->assertDatabaseHas('density_water_sample', ['id' => $otherDate->id, 'status' => 'belum_lengkap']);
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

    public function test_summary_contains_only_selected_samples_in_sequence(): void
    {
        $user = $this->userWithRole('User');
        $first = $this->sample($user, 1, 'S-PILIH-1');
        $this->sample($user, 2, 'S-TIDAK-DIPILIH');
        $third = $this->sample($user, 3, 'S-PILIH-3');

        $this->actingAs($user)
            ->get("/density-water-sample/struk-ringkasan?ids={$third->id},{$first->id}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('DensityWaterSample/StrukRingkasan')
                ->has('records', 2)
                ->where('records.0.id', $first->id)
                ->where('records.1.id', $third->id)
                ->where('records.0.creator.name', $user->name));
    }

    public function test_summary_rejects_empty_invalid_and_duplicate_selection(): void
    {
        $admin = $this->userWithRole('admin');
        $sample = $this->sample($admin);

        foreach (['', 'abc', '999999999', "{$sample->id},{$sample->id}"] as $ids) {
            $this->actingAs($admin)
                ->getJson('/density-water-sample/struk-ringkasan?ids='.urlencode($ids))
                ->assertUnprocessable();
        }
    }

    public function test_guest_cannot_view_summary(): void
    {
        $this->get('/density-water-sample/struk-ringkasan?ids=1')->assertRedirect('/login');
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
            'sesi_ke' => 1,
            'urutan' => $sequence,
            'kode_sample' => $code,
            'no_open' => 'OPEN-'.$sequence,
            'status' => 'belum_lengkap',
            'created_by' => $creator->id,
        ]);
    }
}
