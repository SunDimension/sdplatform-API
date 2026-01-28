<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DeploymentHubController extends Controller
{
    public function deployToField(Request $request)
    {
        // 1. Validate request
        $request->validate([
            'field_url' => 'required|url',
        ]);

        $fieldDeployUrl = rtrim($request->field_url, '/') . '/api/internal/deploy';

        try {
            // 2. Call field server
            $response = Http::timeout(60)
                ->withHeaders([
                    'X-DEPLOY-TOKEN' => config('deploy.field_token'),
                ])
                ->post($fieldDeployUrl);

            // 3. Log everything
            Log::info('Field deploy triggered', [
                'url' => $fieldDeployUrl,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Field deployment failed',
                    'response' => $response->json(),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Deployment triggered successfully',
                'field_response' => $response->json(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Deploy exception', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Deployment exception',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
