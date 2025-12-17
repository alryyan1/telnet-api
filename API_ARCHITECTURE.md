# Morpho IoT Device Integration - API Architecture

## Final API Structure

### Authentication
- **DEVICE (POST)** → `/auth`
  - Device authenticates and receives JWT token
  - No authentication required
  - Returns: `{ "access_token": "JWT_TOKEN", "token_type": "bearer" }`

### Device Status
- **DEVICE (POST)** → `/device/status`
  - Device periodically pushes telemetry (GPS, sensors, firmware info, battery, etc.)
  - Requires: `Authorization: Bearer TOKEN`
  - Stores device status in database

### Device Logs
- **DEVICE (POST)** → `/device/logs`
  - Device sends log entries with severity, message, and context
  - Requires: `Authorization: Bearer TOKEN`
  - Stores device logs in database

### Device Configuration
- **DEVICE (GET)** → `/device/config?device_id=7777`
  - Device pulls new configuration
  - Requires: `Authorization: Bearer TOKEN`
  - Returns: Device configuration JSON

- **PLATFORM (POST)** → `/device/config`
  - Admin/platform updates device configuration
  - Requires: `Authorization: Bearer TOKEN`
  - Automatically increments `config_timestamp` to ensure device detects updates

### Device Reboot
- **DEVICE (GET)** → `/device/reboot?device_id=7777`
  - Device checks for pending reboot requests
  - Requires: `Authorization: Bearer TOKEN`
  - Returns: `{ "device_id": 7777, "reboot": true/false, ... }`
  - If reboot requested, marks request as executed

- **PLATFORM (POST)** → `/device/reboot`
  - Admin/platform requests device reboot
  - Requires: `Authorization: Bearer TOKEN`
  - Creates reboot request that device will fetch on next check

## Complete Route List

| Method | Endpoint | Actor | Description |
|--------|----------|-------|-------------|
| POST | `/auth` | Device | Authenticate and get JWT token |
| POST | `/device/status` | Device | Send device status/telemetry |
| POST | `/device/logs` | Device | Send device logs |
| GET | `/device/config` | Device | Fetch device configuration |
| POST | `/device/config` | Platform | Update device configuration |
| GET | `/device/reboot` | Device | Check for reboot command |
| POST | `/device/reboot` | Platform | Request device reboot |

## Authentication

All endpoints except `/auth` require JWT Bearer token authentication:
```
Authorization: Bearer YOUR_JWT_TOKEN
```

## Example Requests

### 1. Authenticate
```bash
POST /auth
Content-Type: application/json

Response:
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer"
}
```

### 2. Send Device Status
```bash
POST /device/status
Authorization: Bearer TOKEN
Content-Type: application/json

{
  "device_id": 7777,
  "client_id": 0,
  "firmware_version": "2.1.4B",
  "ip_address": "165.51.150.150",
  "timestamp": 1762275949,
  "gps": {
    "latitude": 36.8001,
    "longitude": 10.1874,
    "altitude": 23,
    "accuracy": 0
  },
  "rssi": -51,
  "batterie_level": 100,
  "temperature": 32.43,
  "humidity": 30.70,
  "mean_vibration": 1.03,
  "light": 527.90,
  "status": "Connected",
  "nbrfid": 0
}
```

### 3. Send Device Logs
```bash
POST /device/logs
Authorization: Bearer TOKEN
Content-Type: application/json

{
  "severity": "error",
  "timestamp": 1762275881,
  "hostname": "165.51.150.150",
  "application": "2.1.4B",
  "device_id": 7777,
  "event_id": 0,
  "message": "Minimum threshold exceeded on SDCard Current",
  "context": {
    "last_reading": -8.81,
    "thresholds": {
      "min": -1,
      "max": 1000,
      "max_var": 250
    }
  }
}
```

### 4. Get Device Configuration
```bash
GET /device/config?device_id=7777
Authorization: Bearer TOKEN

Response:
{
  "device_id": 7777,
  "CAN_id": 26,
  "RFID_enable": true,
  "SD_enable": true,
  "client_id": 105,
  "config_timestamp": 1763468864,
  "configured_by": "Admin",
  "debug_enable": false,
  "endpoint_URL": "morpho.challengeone.tn",
  "frequency": 864500000,
  "mode": 2,
  "sf": 12,
  "status": true,
  "thresholds": {
    "ambiant_temperature": { "min": -20, "max": 50, "max_var": 5 },
    "ambiant_humidity": { "min": 10, "max": 80, "max_var": 5 },
    "ambiant_light": { "min": 0, "max": 5, "max_var": 1 },
    "shock_acceleration": { "min": 0, "max": 3, "max_var": 1 }
  },
  "timestamp": 1763469062,
  "txp": 1
}
```

### 5. Update Device Configuration (Platform)
```bash
POST /device/config
Authorization: Bearer TOKEN
Content-Type: application/json

{
  "device_id": 7777,
  "CAN_id": 26,
  "RFID_enable": true,
  "SD_enable": true,
  "client_id": 105,
  "configured_by": "Admin",
  "debug_enable": false,
  "endpoint_URL": "morpho.challengeone.tn",
  "frequency": 864500000,
  "mode": 2,
  "sf": 12,
  "status": true,
  "thresholds": {
    "ambiant_temperature": { "min": -20, "max": 50, "max_var": 5 },
    "ambiant_humidity": { "min": 10, "max": 80, "max_var": 5 },
    "ambiant_light": { "min": 0, "max": 5, "max_var": 1 },
    "shock_acceleration": { "min": 0, "max": 3, "max_var": 1 }
  },
  "txp": 1
}
```

### 6. Check Reboot Command (Device)
```bash
GET /device/reboot?device_id=7777
Authorization: Bearer TOKEN

Response (if reboot requested):
{
  "device_id": 7777,
  "reboot": true,
  "requested_at": "2025-12-07T09:42:11+00:00",
  "requested_by": "Admin"
}

Response (if no reboot):
{
  "device_id": 7777,
  "reboot": false
}
```

### 7. Request Reboot (Platform)
```bash
POST /device/reboot
Authorization: Bearer TOKEN
Content-Type: application/json

{
  "device_id": 7777,
  "requested_by": "Admin"
}

Response:
{
  "success": true,
  "message": "Reboot request created successfully",
  "data": {
    "id": 1,
    "device_id": 7777,
    "requested_by": "Admin",
    "created_at": "2025-12-07T09:42:11+00:00"
  }
}
```

## Database Tables

- `device_statuses` - Stores device telemetry data
- `device_logs` - Stores device log entries
- `device_configs` - Stores device configurations
- `reboot_requests` - Stores reboot requests from platform

## Important Notes

1. **JWT Authentication**: All endpoints except `/auth` require valid JWT Bearer token
2. **Config Timestamp**: Platform automatically increments `config_timestamp` on each update to ensure device detects changes
3. **Reboot Requests**: Platform creates reboot requests that devices check periodically via GET `/device/reboot`
4. **Route Structure**: All routes are accessible directly at domain root (no `/api` or `/morpho` prefix after Apache configuration)

