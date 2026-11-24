<?php

namespace App\Http\Controllers;

use App\Models\DensityWaterSample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class DensityWaterSampleController extends Controller
{
    public function index(Request $request): Response
    {
        $request->validate([
            'tgl_test' => ['nullable', 'date'],
            'sesi_ke' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $samples = DensityWaterSample::query()
            ->with(['creator:id,name,username', 'completer:id,name,username'])
            ->when($request->tgl_test, fn ($query, $date) => $query->whereDate('tgl_test', $date))
            ->when($request->sesi_ke, fn ($query, $session) => $query->where('sesi_ke', $session))
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('kode_sample', 'like', "%{$search}%")
                        ->orWhere('no_open', 'like', "%{$search}%")
                        ->orWhere('urutan', $search);
                });
            })
            ->orderByDesc('tgl_test')
            ->orderBy('sesi_ke')
            ->orderBy('urutan')
            ->paginate(29)
            ->withQueryString();

        $sessions = $request->tgl_test
            ? DensityWaterSample::query()->whereDate('tgl_test', $request->tgl_test)->distinct()->orderBy('sesi_ke')->pluck('sesi_ke')
            : collect();

        return Inertia::render('DensityWaterSample/Index', [
            'samples' => $samples,
            'sessions' => $sessions,
            'filters' => $request->only(['tgl_test', 'sesi_ke', 'search']),
            'canWrite' => $request->user()->hasAnyRole(['admin', 'Operator']),
        ]);
    }

    public function strukRingkasan(Request $request): Response
    {
        $validated = validator([
            'ids' => is_string($request->query('ids')) ? explode(',', $request->query('ids')) : $request->query('ids'),
        ], [
            'ids' => ['required', 'array', 'min:1', 'max:200'],
            'ids.*' => ['required', 'integer', 'distinct', Rule::exists('density_water_sample', 'id')],
        ], [
            'ids.required' => 'Pilih data yang ingin ditampilkan pada struk.',
        ])->validate();

        return Inertia::render('DensityWaterSample/StrukRingkasan', [
            'records' => DensityWaterSample::query()
                ->with(['creator:id,name', 'completer:id,name'])
                ->whereIn('id', $validated['ids'])
                ->orderBy('tgl_test')
                ->orderBy('sesi_ke')
                ->orderBy('urutan')
                ->get(),
            'tanggal' => now()->format('d-m-Y H:i'),
        ]);
    }

    public function report(Request $request): Response
    {
        $validated = $request->validate([
            'tgl_test' => ['nullable', 'date'],
            'sesi_ke' => ['nullable', 'integer', 'min:1'],
        ]);

        $samples = collect();
        $sessions = collect();
        if (! empty($validated['tgl_test'])) {
            $sessions = DensityWaterSample::query()
                ->whereDate('tgl_test', $validated['tgl_test'])
                ->distinct()
                ->orderBy('sesi_ke')
                ->pluck('sesi_ke');

            $samples = DensityWaterSample::query()
                ->with(['creator:id,name,username', 'completer:id,name,username'])
                ->whereDate('tgl_test', $validated['tgl_test'])
                ->when($validated['sesi_ke'] ?? null, fn ($query, $session) => $query->where('sesi_ke', $session))
                ->orderBy('sesi_ke')
                ->orderBy('urutan')
                ->get();
        }

        return Inertia::render('DensityWaterSample/Report', [
            'samples' => $samples,
            'sessions' => $sessions,
            'filters' => $request->only(['tgl_test', 'sesi_ke']),
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'tgl_test' => ['required', 'date'],
            'sesi_ke' => ['nullable', 'integer', 'min:1'],
        ], [
            'tgl_test.required' => 'Tanggal test wajib dipilih.',
        ]);

        $date = $validated['tgl_test'];
        $session = $validated['sesi_ke'] ?? null;
        $fileName = "laporan-density-water-sample-{$date}".($session ? "-sesi-{$session}" : '').'.csv';

        return response()->streamDownload(function () use ($date, $session) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Tanggal Test',
                'Sesi Ke',
                'Urutan',
                'Kode Sample',
                'No. Open',
                'Ketebalan (mm)',
                'Berat Awal (gr)',
                'Berat Akhir (gr)',
                'Volume (ml)',
                'Density (p=m/V)',
                'Perlakuan',
                'Palm WO (gr)',
                'Palm WA (gr)',
                'Palm Water (gr)',
                'MC WO (gr)',
                'MC WA (gr)',
                'MC Water (gr)',
                'SL WO (gr)',
                'SL WA (gr)',
                'SL Water (gr)',
                'Spec',
                'Mulai Proses',
                'Selesai Proses',
                'Temp Air (°C)',
                'Status',
                'Penginput Tahap Pertama',
                'Penginput Tahap Kedua',
            ], ';');

            DensityWaterSample::query()
                ->with(['creator:id,name,username', 'completer:id,name,username'])
                ->whereDate('tgl_test', $date)
                ->when($session, fn ($query, $value) => $query->where('sesi_ke', $value))
                ->orderBy('sesi_ke')
                ->orderBy('urutan')
                ->each(function (DensityWaterSample $sample) use ($output) {
                    fputcsv($output, [
                        $sample->tgl_test->format('Y-m-d'),
                        $sample->sesi_ke,
                        $sample->urutan,
                        $sample->kode_sample,
                        $sample->no_open,
                        $sample->ketebalan,
                        $sample->berat_awal,
                        $sample->berat_akhir,
                        $sample->volume,
                        $sample->density,
                        $sample->perlakuan,
                        $sample->palm_wo,
                        $sample->palm_wa,
                        $sample->palm_water,
                        $sample->mc_wo,
                        $sample->mc_wa,
                        $sample->mc_water,
                        $sample->sl_wo,
                        $sample->sl_wa,
                        $sample->sl_water,
                        $sample->spec,
                        $sample->mulai_proses,
                        $sample->selesai_proses,
                        $sample->temp_air,
                        $sample->status === 'selesai' ? 'Selesai' : 'Belum Lengkap',
                        $sample->creator?->name,
                        $sample->completer?->name,
                    ], ';');
                });

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create(Request $request): Response
    {
        $date = $request->query('tgl_test');
        $sessions = $date
            ? DensityWaterSample::query()->whereDate('tgl_test', $date)->distinct()->orderBy('sesi_ke')->pluck('sesi_ke')
            : collect();
        $selectedSession = $request->query('sesi_ke');

        if ($date && ! $selectedSession && $request->boolean('new_session')) {
            $selectedSession = ((int) DensityWaterSample::query()->whereDate('tgl_test', $date)->max('sesi_ke')) + 1;
        }

        return Inertia::render('DensityWaterSample/Create', [
            'sessions' => $sessions,
            'selectedDate' => $date,
            'selectedSession' => $selectedSession,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->stageOneRules(), $this->validationMessages());

        $sample = DB::transaction(function () use ($validated, $request) {
            $session = (int) ($validated['sesi_ke'] ?? DensityWaterSample::query()
                ->whereDate('tgl_test', $validated['tgl_test'])
                ->max('sesi_ke'));
            $session = max(1, $session);

            $usedSequences = DensityWaterSample::query()
                ->whereDate('tgl_test', $validated['tgl_test'])
                ->where('sesi_ke', $session)
                ->lockForUpdate()
                ->orderBy('urutan')
                ->pluck('urutan')
                ->all();

            $sequence = 1;
            foreach ($usedSequences as $usedSequence) {
                if ((int) $usedSequence !== $sequence) {
                    break;
                }
                $sequence++;
            }

            return DensityWaterSample::create([
                ...$validated,
                'sesi_ke' => $session,
                'urutan' => $sequence,
                'status' => 'belum_lengkap',
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('density-water-sample.index', ['tgl_test' => $sample->tgl_test->format('Y-m-d'), 'sesi_ke' => $sample->sesi_ke])
            ->with('success', 'Data tahap pertama berhasil disimpan.');
    }

    public function bulkComplete(Request $request): Response
    {
        $validated = $request->validate([
            'tgl_test' => ['required', 'date'],
            'sesi_ke' => ['nullable', 'integer', 'min:1'],
        ], [
            'tgl_test.required' => 'Tanggal test wajib dipilih.',
        ]);

        $samples = DensityWaterSample::query()
            ->whereDate('tgl_test', $validated['tgl_test'])
            ->when($validated['sesi_ke'] ?? null, fn ($query, $session) => $query->where('sesi_ke', $session))
            ->orderBy('urutan')
            ->get();

        $latest = DensityWaterSample::query()
            ->where(function ($query) {
                $query->whereNotNull('spec')
                    ->orWhereNotNull('mulai_proses')
                    ->orWhereNotNull('selesai_proses')
                    ->orWhereNotNull('temp_air');
            })
            ->latest('updated_at')
            ->first(['spec', 'mulai_proses', 'selesai_proses', 'temp_air']);

        return Inertia::render('DensityWaterSample/BulkComplete', [
            'tglTest' => $validated['tgl_test'],
            'sesiKe' => $validated['sesi_ke'] ?? null,
            'samples' => $samples,
            'defaults' => [
                'spec' => $samples->firstWhere('spec', '!=', null)?->spec ?? $latest?->spec,
                'mulai_proses' => $samples->firstWhere('mulai_proses', '!=', null)?->mulai_proses ?? $latest?->mulai_proses,
                'selesai_proses' => $samples->firstWhere('selesai_proses', '!=', null)?->selesai_proses ?? $latest?->selesai_proses,
                'temp_air' => $samples->firstWhere('temp_air', '!=', null)?->temp_air ?? $latest?->temp_air,
            ],
        ]);
    }

    public function storeBulkCompletion(Request $request)
    {
        $validated = $request->validate([
            'tgl_test' => ['required', 'date'],
            'sesi_ke' => ['required', 'integer', 'min:1'],
            'spec' => ['nullable', 'string', 'max:255'],
            'mulai_proses' => ['nullable', 'date_format:H:i'],
            'selesai_proses' => ['nullable', 'date_format:H:i'],
            'temp_air' => ['nullable', 'integer'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.id' => ['required', 'integer', 'distinct', Rule::exists('density_water_sample', 'id')],
            'records.*.perlakuan' => ['nullable', 'string', 'max:255'],
            'records.*.palm_wo' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'records.*.palm_wa' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'records.*.palm_water' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'records.*.mc_wo' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'records.*.mc_wa' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'records.*.mc_water' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'records.*.sl_wo' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'records.*.sl_wa' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'records.*.sl_water' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
        ], $this->validationMessages());

        $ids = collect($validated['records'])->pluck('id')->all();

        DB::transaction(function () use ($ids, $validated, $request) {
            $samples = DensityWaterSample::query()
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($samples->count() !== count($ids) || $samples->contains(fn (DensityWaterSample $sample) => $sample->tgl_test->format('Y-m-d') !== $validated['tgl_test'] || (int) $sample->sesi_ke !== (int) $validated['sesi_ke'])) {
                throw ValidationException::withMessages([
                    'records' => 'Semua data harus berasal dari tanggal dan sesi yang dipilih.',
                ]);
            }

            foreach ($validated['records'] as $record) {
                $samples[$record['id']]->update([
                    ...collect($record)->except('id')->all(),
                    'spec' => $validated['spec'],
                    'mulai_proses' => $validated['mulai_proses'],
                    'selesai_proses' => $validated['selesai_proses'],
                    'temp_air' => $validated['temp_air'],
                    'status' => 'selesai',
                    'completed_by' => $request->user()->id,
                ]);
            }
        });

        return redirect()->route('density-water-sample.index', ['tgl_test' => $validated['tgl_test'], 'sesi_ke' => $validated['sesi_ke']])
            ->with('success', count($ids).' data tahap kedua berhasil disimpan.');
    }

    public function edit(DensityWaterSample $densityWaterSample): Response
    {
        return Inertia::render('DensityWaterSample/Edit', [
            'sample' => $densityWaterSample->load(['creator:id,name,username', 'completer:id,name,username']),
        ]);
    }

    public function update(Request $request, DensityWaterSample $densityWaterSample)
    {
        $validated = $request->validate([
            ...$this->stageOneRules(),
            'urutan' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('density_water_sample', 'urutan')
                    ->where(fn ($query) => $query->whereDate('tgl_test', $request->tgl_test)->where('sesi_ke', $request->sesi_ke ?? $densityWaterSample->sesi_ke))
                    ->ignore($densityWaterSample->id),
            ],
            ...$this->stageTwoRules(),
        ], $this->validationMessages());

        $densityWaterSample->update($validated);

        return redirect()->route('density-water-sample.index', ['tgl_test' => $densityWaterSample->tgl_test->format('Y-m-d'), 'sesi_ke' => $densityWaterSample->sesi_ke])
            ->with('success', 'Data density water sample berhasil diperbarui.');
    }

    public function complete(DensityWaterSample $densityWaterSample): Response
    {
        $latest = DensityWaterSample::query()
            ->where('id', '!=', $densityWaterSample->id)
            ->where(function ($query) {
                $query->whereNotNull('spec')
                    ->orWhereNotNull('mulai_proses')
                    ->orWhereNotNull('selesai_proses')
                    ->orWhereNotNull('temp_air');
            })
            ->latest('updated_at')
            ->first(['spec', 'mulai_proses', 'selesai_proses', 'temp_air']);

        return Inertia::render('DensityWaterSample/Complete', [
            'sample' => $densityWaterSample,
            'defaults' => [
                'spec' => $densityWaterSample->spec ?? $latest?->spec,
                'mulai_proses' => $densityWaterSample->mulai_proses ?? $latest?->mulai_proses,
                'selesai_proses' => $densityWaterSample->selesai_proses ?? $latest?->selesai_proses,
                'temp_air' => $densityWaterSample->temp_air ?? $latest?->temp_air,
            ],
        ]);
    }

    public function storeCompletion(Request $request, DensityWaterSample $densityWaterSample)
    {
        $validated = $request->validate($this->stageTwoRules(), $this->validationMessages());

        $densityWaterSample->update([
            ...$validated,
            'status' => 'selesai',
            'completed_by' => $request->user()->id,
        ]);

        return redirect()->route('density-water-sample.index', ['tgl_test' => $densityWaterSample->tgl_test->format('Y-m-d'), 'sesi_ke' => $densityWaterSample->sesi_ke])
            ->with('success', 'Data tahap kedua berhasil dilengkapi.');
    }

    public function destroy(DensityWaterSample $densityWaterSample)
    {
        $date = $densityWaterSample->tgl_test->format('Y-m-d');
        $densityWaterSample->delete();

        return redirect()->route('density-water-sample.index', ['tgl_test' => $date])
            ->with('success', 'Data density water sample berhasil dihapus.');
    }

    private function stageOneRules(): array
    {
        return [
            'tgl_test' => ['required', 'date'],
            'sesi_ke' => ['nullable', 'integer', 'min:1'],
            'kode_sample' => ['required', 'string', 'max:255'],
            'no_open' => ['nullable', 'string', 'max:255'],
            'ketebalan' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'berat_awal' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'berat_akhir' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'volume' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'density' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
        ];
    }

    private function stageTwoRules(): array
    {
        return [
            'perlakuan' => ['nullable', 'string', 'max:255'],
            'palm_wo' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'palm_wa' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'palm_water' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'mc_wo' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'mc_wa' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'mc_water' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'sl_wo' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'sl_wa' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'sl_water' => ['nullable', 'numeric', 'min:0', 'decimal:0,3'],
            'spec' => ['nullable', 'string', 'max:255'],
            'mulai_proses' => ['nullable', 'date_format:H:i'],
            'selesai_proses' => ['nullable', 'date_format:H:i'],
            'temp_air' => ['nullable', 'integer'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'tgl_test.required' => 'Tanggal test wajib diisi.',
            'kode_sample.required' => 'Kode sample wajib diisi.',
            'urutan.unique' => 'Nomor urutan sudah digunakan pada tanggal test tersebut.',
            '*.decimal' => 'Nilai memiliki terlalu banyak angka di belakang koma.',
        ];
    }
}
