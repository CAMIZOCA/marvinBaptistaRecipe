<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportRecipeChunk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\View\View;
use League\Csv\Reader;

class RecipeImportController extends Controller
{
    public function index(): View
    {
        return view('admin.tools.import');
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ], [
            'csv_file.required' => 'Debes seleccionar un archivo CSV.',
            'csv_file.mimes' => 'El archivo debe ser CSV.',
            'csv_file.max' => 'El archivo no debe superar los 10MB.',
        ]);

        $file = $request->file('csv_file');
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        $records = collect(iterator_to_array($csv->getRecords()));

        // Detect if new format has image downloads — use chunk size 5 to avoid timeouts
        $firstRow  = $records->first();
        $isNewFormat = $firstRow && array_key_exists('titulo', $firstRow);
        $chunkSize = $isNewFormat ? 5 : 50;

        $chunks = $records->chunk($chunkSize);

        $jobs = $chunks->map(fn ($chunk) =>
            new ImportRecipeChunk($chunk->values()->toArray(), auth()->id())
        )->toArray();

        if (empty($jobs)) {
            return response()->json(['error' => 'El archivo CSV está vacío o no tiene filas de datos.'], 422);
        }

        $batch = Bus::batch($jobs)
            ->name('Importación CSV - ' . now()->format('d/m/Y H:i'))
            ->allowFailures()
            ->dispatch();

        return response()->json([
            'batch_id' => $batch->id,
            'total_recipes' => $records->count(),
            'message' => "Importando {$records->count()} recetas...",
        ]);
    }

    public function progress(string $batch): JsonResponse
    {
        $batchRecord = Bus::findBatch($batch);

        if (!$batchRecord) {
            return response()->json(['error' => 'Batch no encontrado.'], 404);
        }

        return response()->json([
            'total_jobs' => $batchRecord->totalJobs,
            'processed_jobs' => $batchRecord->processedJobs(),
            'failed_jobs' => $batchRecord->failedJobs,
            'progress' => $batchRecord->progress(),
            'finished' => $batchRecord->finished(),
            'cancelled' => $batchRecord->cancelled(),
        ]);
    }
}
