<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendDeviceLogsRequest extends FormRequest
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
            'severity' => 'required|string|in:error,warning,info,debug',
            'timestamp' => 'required|integer',
            'hostname' => 'required|string|max:255',
            'application' => 'required|string|max:255',
            'device_id' => 'required|integer',
            'event_id' => 'nullable|integer',
            'message' => 'required|string',
            'context' => 'nullable|array',
            'context.last_reading' => 'nullable|numeric',
            'context.thresholds' => 'nullable|array',
            'context.thresholds.min' => 'nullable|numeric',
            'context.thresholds.max' => 'nullable|numeric',
            'context.thresholds.max_var' => 'nullable|numeric',
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
            'severity.required' => 'Severity is required',
            'severity.in' => 'Severity must be one of: error, warning, info, debug',
            'timestamp.required' => 'Timestamp is required',
            'timestamp.integer' => 'Timestamp must be an integer',
            'hostname.required' => 'Hostname is required',
            'application.required' => 'Application is required',
            'device_id.required' => 'Device ID is required',
            'device_id.integer' => 'Device ID must be an integer',
            'message.required' => 'Message is required',
        ];
    }
}

