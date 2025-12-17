<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendDeviceStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'device_id' => 'required|integer',
            'client_id' => 'required|integer',
            'firmware_version' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'timestamp' => 'required|integer',
            'gps' => 'required|array',
            'gps.latitude' => 'required|numeric|between:-90,90',
            'gps.longitude' => 'required|numeric|between:-180,180',
            'gps.altitude' => 'nullable|numeric',
            'gps.accuracy' => 'nullable|numeric|min:0',
            'rssi' => 'nullable|integer',
            'batterie_level' => 'nullable|integer|min:0|max:100',
            'temperature' => 'nullable|numeric',
            'humidity' => 'nullable|numeric|min:0|max:100',
            'mean_vibration' => 'nullable|numeric|min:0',
            'light' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:255',
            'nbrfid' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'device_id.required' => 'Device ID is required',
            'device_id.integer' => 'Device ID must be an integer',
            'client_id.required' => 'Client ID is required',
            'client_id.integer' => 'Client ID must be an integer',
            'firmware_version.required' => 'Firmware version is required',
            'ip_address.required' => 'IP address is required',
            'ip_address.ip' => 'IP address must be a valid IP address',
            'timestamp.required' => 'Timestamp is required',
            'timestamp.integer' => 'Timestamp must be an integer',
            'gps.required' => 'GPS data is required',
            'gps.array' => 'GPS data must be an array',
            'gps.latitude.required' => 'GPS latitude is required',
            'gps.latitude.numeric' => 'GPS latitude must be a number',
            'gps.latitude.between' => 'GPS latitude must be between -90 and 90',
            'gps.longitude.required' => 'GPS longitude is required',
            'gps.longitude.numeric' => 'GPS longitude must be a number',
            'gps.longitude.between' => 'GPS longitude must be between -180 and 180',
        ];
    }
}

