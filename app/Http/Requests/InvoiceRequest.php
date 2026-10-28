<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        // a percentage can't exceed 100; a fixed amount can't exceed the column limit
        $maxDiscount = $this->input('discount_type') === 'percent' ? 100 : 999999.99;

        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'issue_date'  => ['required', 'date'],
            'due_date'    => ['nullable', 'date', 'after_or_equal:issue_date'],
            'notes'       => ['nullable', 'string', 'max:2000'],

            'discount_type'  => ['required', Rule::in(['fixed', 'percent'])],
            'discount_value' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:' . $maxDiscount],
            'tax_rate'       => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:100'],

            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.qty'         => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999'],
            'items.*.unit_price'  => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:999999.99'],
        ];
    }
}
