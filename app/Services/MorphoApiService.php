<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\DeviceStatus;
use App\Models\DeviceLog;
use App\Models\DeviceConfig;
use App\Models\RebootRequest;

class MorphoApiService
{
    protected string $baseUrl;
    protected ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = config('morpho.api_base_url', 'https://alroomy.a.pinggy.link');
    }

    /**
     * Generate JWT authentication token
     *
     * @return array
     */
    public function authenticate(): array
    {
        try {
            // Get JWT secret from config or use APP_KEY
            $secret = config('morpho.jwt_secret') ?? config('app.key');
            
            // Remove 'base64:' prefix if present (Laravel 5.1+ format)
            if (str_starts_with($secret, 'base64:')) {
                $secret = base64_decode(substr($secret, 7));
            }

            // Token expiration time
            $expirationMinutes = config('morpho.jwt_expiration', 60);
            $issuedAt = time();
            $expirationTime = $issuedAt + ($expirationMinutes * 60);

            // JWT payload
            $payload = [
                'iss' => config('app.url', 'http://localhost'), // Issuer
                'iat' => $issuedAt, // Issued at
                'exp' => $expirationTime, // Expiration time
                'nbf' => $issuedAt, // Not before
                'jti' => uniqid('', true), // JWT ID
            ];

            // Generate JWT token
            $token = JWT::encode($payload, $secret, 'HS256');
            $tokenType = 'bearer';

            // Cache token
            $this->token = $token;
            $cacheDuration = config('morpho.token_cache_duration', 60);
            Cache::put('morpho_api_token', $token, now()->addMinutes($cacheDuration));
            Cache::put('morpho_api_token_type', $tokenType, now()->addMinutes($cacheDuration));

            return [
                'success' => true,
                'access_token' => $token,
                'token_type' => $tokenType,
                'expires_in' => $expirationMinutes * 60,
                'token' => $token, // Keep for backward compatibility
            ];
        } catch (\Exception $e) {
            Log::error('Morpho JWT Token Generation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get cached token or authenticate
     *
     * @return string|null
     */
    protected function getToken(): ?string
    {
        if ($this->token) {
            return $this->token;
        }

        $cachedToken = Cache::get('morpho_api_token');
        if ($cachedToken) {
            $this->token = $cachedToken;
            return $cachedToken;
        }

        $authResult = $this->authenticate();
        return $authResult['access_token'] ?? $authResult['token'] ?? null;
    }

    /**
     * Store device status locally
     *
     * @param array $data
     * @return array
     */
    public function sendDeviceStatus(array $data): array
    {
        try {
            // Extract GPS data
            $gps = $data['gps'] ?? [];

            // Prepare data for database storage
            $deviceStatusData = [
                'device_id' => $data['device_id'],
                'client_id' => $data['client_id'] ?? 0,
                'firmware_version' => $data['firmware_version'],
                'ip_address' => $data['ip_address'],
                'timestamp' => $data['timestamp'],
                'gps_latitude' => $gps['latitude'] ?? null,
                'gps_longitude' => $gps['longitude'] ?? null,
                'gps_altitude' => $gps['altitude'] ?? null,
                'gps_accuracy' => $gps['accuracy'] ?? 0,
                'rssi' => $data['rssi'] ?? null,
                'batterie_level' => $data['batterie_level'] ?? null,
                'temperature' => $data['temperature'] ?? null,
                'humidity' => $data['humidity'] ?? null,
                'mean_vibration' => $data['mean_vibration'] ?? null,
                'light' => $data['light'] ?? null,
                'status' => $data['status'] ?? null,
                'nbrfid' => $data['nbrfid'] ?? 0,
            ];

            // Store device status in database
            $deviceStatus = DeviceStatus::create($deviceStatusData);

            Log::info('Device status stored successfully', [
                'device_id' => $data['device_id'],
                'status_id' => $deviceStatus->id,
            ]);

            return [
                'success' => true,
                'message' => 'Device status stored successfully',
                'data' => [
                    'id' => $deviceStatus->id,
                    'device_id' => $deviceStatus->device_id,
                    'timestamp' => $deviceStatus->timestamp,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Device Status Storage Error: ' . $e->getMessage(), [
                'device_id' => $data['device_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Store device logs locally
     *
     * @param array $data
     * @return array
     */
    public function sendDeviceLogs(array $data): array
    {
        try {
            // Prepare data for database storage
            $deviceLogData = [
                'severity' => $data['severity'],
                'timestamp' => $data['timestamp'],
                'hostname' => $data['hostname'],
                'application' => $data['application'],
                'device_id' => $data['device_id'],
                'event_id' => $data['event_id'] ?? 0,
                'message' => $data['message'],
                'context' => $data['context'] ?? null, // Store context as JSON
            ];

            // Store device log in database
            $deviceLog = DeviceLog::create($deviceLogData);

            Log::info('Device log stored successfully', [
                'device_id' => $data['device_id'],
                'log_id' => $deviceLog->id,
                'severity' => $data['severity'],
            ]);

            return [
                'success' => true,
                'message' => 'Device log stored successfully',
                'data' => [
                    'id' => $deviceLog->id,
                    'device_id' => $deviceLog->device_id,
                    'severity' => $deviceLog->severity,
                    'timestamp' => $deviceLog->timestamp,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Device Log Storage Error: ' . $e->getMessage(), [
                'device_id' => $data['device_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch device configuration from database
     *
     * @param int $deviceId
     * @return array
     */
    public function fetchDeviceConfig(int $deviceId): array
    {
        try {
            $config = DeviceConfig::getLatestForDevice($deviceId);

            if (!$config) {
                return [
                    'success' => false,
                    'error' => 'Configuration not found for device ID: ' . $deviceId,
                ];
            }

            // Format response to match expected structure
            $configData = [
                'device_id' => $config->device_id,
                'CAN_id' => $config->CAN_id,
                'RFID_enable' => $config->RFID_enable,
                'SD_enable' => $config->SD_enable,
                'client_id' => $config->client_id,
                'config_timestamp' => $config->config_timestamp,
                'configured_by' => $config->configured_by,
                'debug_enable' => $config->debug_enable,
                'endpoint_URL' => $config->endpoint_URL,
                'frequency' => $config->frequency,
                'mode' => $config->mode,
                'sf' => $config->sf,
                'status' => $config->status,
                'thresholds' => $config->thresholds ?? [],
                'timestamp' => $config->timestamp ?? time(),
                'txp' => $config->txp,
            ];

            return [
                'success' => true,
                'data' => $configData,
            ];
        } catch (\Exception $e) {
            Log::error('Fetch Device Config Error: ' . $e->getMessage(), [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Update or create device configuration
     * Ensures config_timestamp is always incremented
     *
     * @param int $deviceId
     * @param array $configData
     * @return array
     */
    public function updateDeviceConfig(int $deviceId, array $configData): array
    {
        try {
            // Get current config to ensure we increment timestamp
            $currentConfig = DeviceConfig::getLatestForDevice($deviceId);
            
            // Ensure config_timestamp is always higher than previous
            $newTimestamp = time();
            if ($currentConfig && $currentConfig->config_timestamp >= $newTimestamp) {
                $newTimestamp = $currentConfig->config_timestamp + 1;
            }

            // Prepare data for update/create
            $updateData = array_merge($configData, [
                'device_id' => $deviceId,
                'config_timestamp' => $newTimestamp,
                'timestamp' => $newTimestamp,
            ]);

            // Update or create configuration
            $config = DeviceConfig::updateOrCreate(
                ['device_id' => $deviceId],
                $updateData
            );

            Log::info('Device configuration updated', [
                'device_id' => $deviceId,
                'config_timestamp' => $newTimestamp,
            ]);

            return [
                'success' => true,
                'message' => 'Device configuration updated successfully',
                'data' => [
                    'device_id' => $config->device_id,
                    'config_timestamp' => $config->config_timestamp,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Update Device Config Error: ' . $e->getMessage(), [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch reboot command (Device checks for pending reboot requests)
     *
     * @param int $deviceId
     * @return array
     */
    public function fetchRebootCommand(int $deviceId): array
    {
        try {
            // Check for pending reboot request
            $rebootRequest = RebootRequest::getPendingForDevice($deviceId);

            if ($rebootRequest) {
                // Mark as executed
                $rebootRequest->update([
                    'executed' => true,
                    'executed_at' => now(),
                ]);

                Log::info('Reboot command fetched by device', [
                    'device_id' => $deviceId,
                    'request_id' => $rebootRequest->id,
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'device_id' => $deviceId,
                        'reboot' => true,
                        'requested_at' => $rebootRequest->created_at->toIso8601String(),
                        'requested_by' => $rebootRequest->requested_by,
                    ],
                ];
            }

            // No pending reboot request
            return [
                'success' => true,
                'data' => [
                    'device_id' => $deviceId,
                    'reboot' => false,
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Fetch Reboot Command Error: ' . $e->getMessage(), [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Request reboot for a device (Platform/Admin)
     *
     * @param int $deviceId
     * @param string|null $requestedBy
     * @return array
     */
    public function requestReboot(int $deviceId, ?string $requestedBy = null): array
    {
        try {
            // Create reboot request
            $rebootRequest = RebootRequest::create([
                'device_id' => $deviceId,
                'requested_by' => $requestedBy ?? 'Admin',
                'executed' => false,
            ]);

            Log::info('Reboot requested by platform/admin', [
                'device_id' => $deviceId,
                'request_id' => $rebootRequest->id,
                'requested_by' => $requestedBy,
            ]);

            return [
                'success' => true,
                'message' => 'Reboot request created successfully',
                'data' => [
                    'id' => $rebootRequest->id,
                    'device_id' => $rebootRequest->device_id,
                    'requested_by' => $rebootRequest->requested_by,
                    'created_at' => $rebootRequest->created_at->toIso8601String(),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Request Reboot Error: ' . $e->getMessage(), [
                'device_id' => $deviceId,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

