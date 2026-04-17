<?php

namespace App\Http\Controllers;

use App\Support\OpenApiFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class SwaggerController extends Controller
{
    public function index(): View
    {
        return view('swagger.index', [
            'specUrl' => route('swagger.json'),
        ]);
    }

    public function json(OpenApiFactory $openApiFactory): JsonResponse
    {
        return response()->json(
            $openApiFactory->make(),
            200,
            [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
