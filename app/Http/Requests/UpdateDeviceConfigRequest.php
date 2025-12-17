<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeviceConfigRequest extends FormRequest
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
            'CAN_id' => 'nullable|integer',
            'RFID_enable' => 'nullable|boolean',
            'SD_enable' => 'nullable|boolean',
            'client_id' => 'nullable|integer',
            'configured_by' => 'nullable|string|max:255',
            'debug_enable' => 'nullable|boolean',
            'endpoint_URL' => 'nullable|string|max:255',
            'frequency' => 'nullable|integer',
            'mode' => 'nullable|integer',
            'sf' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'thresholds' => 'nullable|array',
            'thresholds.ambiant_temperature' => 'nullable|array',
            'thresholds.ambiant_temperature.min' => 'nullable|numeric',
            'thresholds.ambiant_temperature.max' => 'nullable|numeric',
            'thresholds.ambiant_temperature.max_var' => 'nullable|numeric',
            'thresholds.ambiant_humidity' => 'nullable|array',
            'thresholds.ambiant_humidity.min' => 'nullable|numeric',
            'thresholds.ambiant_humidity.max' => 'nullable|numeric',
            'thresholds.ambiant_humidity.max_var' => 'nullable|numeric',
            'thresholds.ambiant_light' => 'nullable|array',
            'thresholds.ambiant_light.min' => 'nullable|numeric',
            'thresholds.ambiant_light.max' => 'nullable|numeric',
            'thresholds.ambiant_light.max_var' => 'nullable|numeric',
            'thresholds.shock_acceleration' => 'nullable|array',
            'thresholds.shock_acceleration.min' => 'nullable|numeric',
            'thresholds.shock_acceleration.max' => 'nullable|numeric',
            'thresholds.shock_acceleration.max_var' => 'nullable|numeric',
            'txp' => 'nullable|integer',
        ];
    }
}
