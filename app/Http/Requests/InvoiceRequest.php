<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'issue_date'  => ['required', 'date'],
            'due_date'    => ['nullable', 'date', 'after_or_equal:issue_date'],
            'notes'       => ['nullable', 'string', 'max:2000'],

            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['nullable', 'exists:products,id'],
            'items.*.description'  => ['required', 'string', 'max:255'],
            'items.*.qty'          => ['required', 'numeric', 'min:0.01', 'max:9999'],
            'items.*.unit_price'   => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }
}
