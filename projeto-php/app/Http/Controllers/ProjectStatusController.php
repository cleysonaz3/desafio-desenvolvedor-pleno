<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProjectStatusController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $databaseHealthy = true;
        $databaseMessage = 'Conexão com o banco disponível.';

        try {
            DB::connection()->getPdo();
        } catch (Throwable $exception) {
            $databaseHealthy = false;
            $databaseMessage = $exception->getMessage();
        }

        $healthy = $databaseHealthy;

        return response()->json([
            'name' => config('app.name'),
            'version' => config('app.version'),
            'status' => $healthy ? 'ok' : 'degraded',
            'checks' => [
                'application' => [
                    'status' => 'ok',
                    'message' => 'Aplicação em execução.',
                ],
                'database' => [
                    'status' => $databaseHealthy ? 'ok' : 'error',
                    'message' => $databaseMessage,
                ],
            ],
            'docs_url' => url('/docs'),
            'timestamp' => now()->toISOString(),
        ], $healthy ? 200 : 503);
    }
}
