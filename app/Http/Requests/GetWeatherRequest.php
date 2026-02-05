<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetWeatherRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'lat' => 'required|numeric|min:-90|max:90',
            'lon' => 'required|numeric|min:-180|max:180',
        ];
    }

    public function messages(): array {
        return [
            'lat.required' => 'Latitude is required',
            'lat.numeric' => 'Latitude must be a number',
            'lat.min' => 'Latitude must be at least -90',
            'lat.max' => 'Latitude must be at most 90',
            'lon.required' => 'Longitude is required',
            'lon.numeric' => 'Longitude must be a number',
            'lon.min' => 'Longitude must be at least -180',
            'lon.max' => 'Longitude must be at most 180',
        ];
    }
}
