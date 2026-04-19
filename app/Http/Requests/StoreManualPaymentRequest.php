<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualPaymentRequest extends FormRequest
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
            'method' => 'required|in:transfer,cash,other',
            'amount' => 'nullable|numeric|min:1000',
            'notes' => 'nullable|string|max:255',
            'proof_attachment' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ];
    }
}
