<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetWeatherRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array {
        return [
            'lat' => ['required', 'numeric', 'min:-90', 'max:90'],
            'lon' => ['required', 'numeric', 'min:-180', 'max:180'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'lat.required' => 'Latitude is required.',
            'lat.numeric' => 'Latitude must be a number.',
            'lat.min' => 'Latitude must be between -90 and 90.',
            'lat.max' => 'Latitude must be between -90 and 90.',
            'lon.required' => 'Longitude is required.',
            'lon.numeric' => 'Longitude must be a number.',
            'lon.min' => 'Longitude must be between -180 and 180.',
            'lon.max' => 'Longitude must be between -180 and 180.',
        ];
    }
}
