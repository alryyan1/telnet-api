<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Log;

class VerifyJwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Get token from Authorization header
            $authHeader = $request->header('Authorization');
            
            if (!$authHeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization header missing',
                ], 401);
            }

            // Extract token from "Bearer TOKEN" format
            if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid authorization header format',
                ], 401);
            }

            $token = $matches[1];

            // Get JWT secret
            $secret = config('morpho.jwt_secret') ?? config('app.key');
            
            // Remove 'base64:' prefix if present (Laravel 5.1+ format)
            if (str_starts_with($secret, 'base64:')) {
                $secret = base64_decode(substr($secret, 7));
            }

            // Decode and verify token
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            // Store decoded token data in request for use in controllers
            $request->merge(['jwt_payload' => (array) $decoded]);

            return $next($request);
        } catch (ExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token has expired',
            ], 401);
        } catch (SignatureInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token signature',
            ], 401);
        } catch (\Exception $e) {
            Log::error('JWT Verification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Token verification failed',
            ], 401);
        }
    }
}
