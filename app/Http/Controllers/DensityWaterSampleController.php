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
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $samples = DensityWaterSample::query()
            ->with(['creator:id,name,username', 'completer:id,name,username'])
            ->when($request->tgl_test, fn ($query, $date) => $query->whereDate('tgl_test', $date))
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('kode_sample', 'like', "%{$search}%")
                        ->orWhere('no_open', 'like', "%{$search}%")
                        ->orWhere('urutan', $search);
                });
            })
            ->orderByDesc('tgl_test')
            ->orderBy('urutan')
            ->paginate(29)
            ->withQueryString();

        return Inertia::render('DensityWaterSample/Index', [
            'samples' => $samples,
            'filters' => $request->only(['tgl_test', 'search']),
            'canWrite' => $request->user()->hasAnyRole(['admin', 'Operator']),
        ]);
    }

    public function report(Request $request): Response
    {
        $validated = $request->validate([
            'tgl_test' => ['nullable', 'date'],
        ]);

        $samples = collect();
        if (! empty($validated['tgl_test'])) {
            $samples = DensityWaterSample::query()
                ->with(['creator:id,name,username', 'completer:id,name,username'])
                ->whereDate('tgl_test', $validated['tgl_test'])
                ->orderBy('urutan')
                ->get();
        }

        return Inertia::render('DensityWaterSample/Report', [
            'samples' => $samples,
            'filters' => $request->only('tgl_test'),
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'tgl_test' => ['required', 'date'],
        ], [
            'tgl_test.required' => 'Tanggal test wajib dipilih.',
        ]);

        $date = $validated['tgl_test'];
        $fileName = "laporan-density-water-sample-{$date}.csv";

        return response()->streamDownload(function () use ($date) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'Tanggal Test',
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
                ->orderBy('urutan')
                ->each(function (DensityWaterSample $sample) use ($output) {
                    fputcsv($output, [
                        $sample->tgl_test->format('Y-m-d'),
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

    public function create(): Response
    {
        return Inertia::render('DensityWaterSample/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->stageOneRules(), $this->validationMessages());

        $sample = DB::transaction(function () use ($validated, $request) {
            $usedSequences = DensityWaterSample::query()
                ->whereDate('tgl_test', $validated['tgl_test'])
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
                'urutan' => $sequence,
                'status' => 'belum_lengkap',
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('density-water-sample.index', ['tgl_test' => $sample->tgl_test->format('Y-m-d')])
            ->with('success', 'Data tahap pertama berhasil disimpan.');
    }

    public function bulkComplete(Request $request): Response
    {
        $validated = $request->validate([
            'tgl_test' => ['required', 'date'],
        ], [
            'tgl_test.required' => 'Tanggal test wajib dipilih.',
        ]);

        $samples = DensityWaterSample::query()
            ->whereDate('tgl_test', $validated['tgl_test'])
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

            if ($samples->count() !== count($ids) || $samples->contains(fn (DensityWaterSample $sample) => $sample->tgl_test->format('Y-m-d') !== $validated['tgl_test'])) {
                throw ValidationException::withMessages([
                    'records' => 'Semua data harus berasal dari tanggal test yang dipilih.',
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

        return redirect()->route('density-water-sample.index', ['tgl_test' => $validated['tgl_test']])
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
                    ->where(fn ($query) => $query->whereDate('tgl_test', $request->tgl_test))
                    ->ignore($densityWaterSample->id),
            ],
            ...$this->stageTwoRules(),
        ], $this->validationMessages());

        $densityWaterSample->update($validated);

        return redirect()->route('density-water-sample.index', ['tgl_test' => $densityWaterSample->tgl_test->format('Y-m-d')])
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

        return redirect()->route('density-water-sample.index', ['tgl_test' => $densityWaterSample->tgl_test->format('Y-m-d')])
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
