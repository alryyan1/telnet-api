<?php

namespace App\Http\Controllers;

use App\Services\MorphoApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MorphoAuthController extends Controller
{
    protected MorphoApiService $morphoApiService;

    public function __construct(MorphoApiService $morphoApiService)
    {
        $this->morphoApiService = $morphoApiService;
    }

    /**
     * Authenticate with Morpho API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticate(Request $request): JsonResponse
    {
        // Log that authentication started
        Log::info('Morpho authentication started', [
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $result = $this->morphoApiService->authenticate();

        // Log the raw result from service
        Log::info('Morpho authenticate() response:', $result);

        if ($result['success'] ?? false) {

            Log::info('Morpho authentication successful', [
                'token_type' => $result['token_type'] ?? 'bearer',
            ]);

            return response()->json([
                'access_token' => $result['access_token'] ?? $result['token'],
                'token_type' => $result['token_type'] ?? 'bearer',
            ], 200);
        }

        Log::error('Morpho authentication failed', [
            'error' => $result['error'] ?? 'Unknown error',
            'status' => $result['status'] ?? 500,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Authentication failed',
            'error' => $result['error'] ?? 'Unknown error',
        ], $result['status'] ?? 500);
    }
}
