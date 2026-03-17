<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportRecipeChunk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use League\Csv\Reader;

class RecipeImportController extends Controller
{
    public function index(): View
    {
        return view('admin.tools.import');
    }

    /**
     * Step 1: Receive the CSV, parse it, save to a temp file, redirect to preview.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'csv_file.required' => 'Debes seleccionar un archivo CSV.',
            'csv_file.mimes'    => 'El archivo debe ser CSV.',
            'csv_file.max'      => 'El archivo no debe superar los 10MB.',
        ]);

        $file = $request->file('csv_file');
        $csv  = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        $records = collect(iterator_to_array($csv->getRecords()))->values()->toArray();

        if (empty($records)) {
            return back()->withErrors(['csv_file' => 'El archivo CSV está vacío o no tiene filas de datos.']);
        }

        // Store rows in a temporary JSON file keyed by user+timestamp
        Storage::makeDirectory('imports');
        $tempKey = 'import_preview_' . auth()->id() . '_' . now()->timestamp;
        Storage::put("imports/{$tempKey}.json", json_encode($records));

        return redirect()->route('admin.recipes.import.preview', ['key' => $tempKey]);
    }

    /**
     * Step 2: Show the preview & bulk-edit screen.
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $key  = $request->query('key');
        $path = "imports/{$key}.json";

        if (!$key || !Storage::exists($path)) {
            return redirect()->route('admin.recipes.import.index')
                ->withErrors(['csv_file' => 'La sesión de previsualización ha expirado. Por favor sube el CSV de nuevo.']);
        }

        $rows = json_decode(Storage::get($path), true);

        // Detect anomalies to warn the user
        $uniquePaises       = collect($rows)->pluck('pais')->unique()->filter()->values();
        $uniqueDificultades = collect($rows)->pluck('dificultad')->unique()->filter()->values();
        $uniqueAmazonLinks  = collect($rows)->pluck('amazon_link')->unique()->filter()->values();

        $warnings = [];
        if ($uniquePaises->count() === 1) {
            $warnings[] = "Todas las recetas tienen el mismo país: <strong>{$uniquePaises->first()}</strong>. ¿Es correcto?";
        }
        if ($uniqueAmazonLinks->count() === 1) {
            $warnings[] = "Todas las recetas tienen el mismo enlace de Amazon. Probablemente es un valor de plantilla — considera vaciarlo.";
        }

        return view('admin.tools.import-preview', compact(
            'rows', 'key', 'warnings', 'uniquePaises', 'uniqueDificultades', 'uniqueAmazonLinks'
        ));
    }

    /**
     * Step 3: Apply overrides and dispatch the import jobs.
     */
    public function confirm(Request $request): JsonResponse
    {
        $key  = $request->input('key');
        $path = "imports/{$key}.json";

        if (!$key || !Storage::exists($path)) {
            return response()->json(['error' => 'La sesión de importación ha expirado. Por favor sube el CSV de nuevo.'], 422);
        }

        $originalRows = json_decode(Storage::get($path), true);
        $overrides    = $request->input('overrides', []);   // indexed by row index
        $skipRows     = $request->input('skip_rows', []);   // array of row indexes to exclude

        // Merge per-row overrides, skip excluded rows
        $finalRows = [];
        foreach ($originalRows as $idx => $row) {
            if (in_array((string) $idx, array_map('strval', $skipRows))) {
                continue;
            }
            if (isset($overrides[$idx]) && is_array($overrides[$idx])) {
                foreach ($overrides[$idx] as $field => $value) {
                    // Allow explicitly clearing amazon_link (empty string = remove)
                    if ($field === 'amazon_link') {
                        $row[$field] = $value; // keep empty string to skip book linking
                    } elseif ($value !== null && $value !== '') {
                        $row[$field] = $value;
                    }
                }
            }
            $finalRows[] = $row;
        }

        // Delete the temporary file
        Storage::delete($path);

        if (empty($finalRows)) {
            return response()->json(['error' => 'No quedan recetas para importar (todas fueron excluidas).'], 422);
        }

        // Dispatch jobs — same logic as original store()
        $chunkSize = 5;
        $chunks    = collect($finalRows)->chunk($chunkSize);
        $jobs      = $chunks->map(fn ($chunk) =>
            new ImportRecipeChunk($chunk->values()->toArray(), auth()->id())
        )->toArray();

        $batch = Bus::batch($jobs)
            ->name('Importación CSV - ' . now()->format('d/m/Y H:i'))
            ->allowFailures()
            ->dispatch();

        return response()->json([
            'batch_id'      => $batch->id,
            'total_recipes' => count($finalRows),
            'message'       => 'Importando ' . count($finalRows) . ' recetas...',
        ]);
    }

    /**
     * Poll the batch progress (unchanged).
     */
    public function progress(string $batch): JsonResponse
    {
        $batchRecord = Bus::findBatch($batch);

        if (!$batchRecord) {
            return response()->json(['error' => 'Batch no encontrado.'], 404);
        }

        return response()->json([
            'total_jobs'     => $batchRecord->totalJobs,
            'processed_jobs' => $batchRecord->processedJobs(),
            'failed_jobs'    => $batchRecord->failedJobs,
            'progress'       => $batchRecord->progress(),
            'finished'       => $batchRecord->finished(),
            'cancelled'      => $batchRecord->cancelled(),
        ]);
    }
}
