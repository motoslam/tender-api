<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenderRequest extends FormRequest
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
            'external_code' => ['required', 'integer', 'unique:tenders'],
            'number' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'name' => ['required', 'string'],
            'updated_at' => ['required', 'date'],
        ];
    }
}
