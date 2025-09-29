<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateStripePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all authenticated users to create payments
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Frontend data structure
            'tax_payee_id' => 'required|integer|exists:tax_payees,id',
            'tax_property_id' => 'nullable|integer|exists:tax_properties,id',
            'tax_assessment_id' => 'nullable|integer|exists:tax_assessments,id',
            'amount_paying' => 'required|numeric|min:0.01|max:999999.99',
            'payment' => 'required|numeric|min:0.01|max:999999.99',
            'pay_method' => 'required|string|in:online,cash',
            'pay_date' => 'required|date',
            'currency' => 'required|string|size:3|in:lkr,usd,eur,gbp',
            'officer_id' => 'nullable|integer', // Optional for public payments
            'success_url' => 'required|url|max:500',
            'cancel_url' => 'required|url|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tax_payee_id.required' => 'The tax payee ID is required.',
            'tax_payee_id.integer' => 'The tax payee ID must be an integer.',
            'tax_payee_id.exists' => 'The selected tax payee does not exist.',
            
            'tax_property_id.integer' => 'The tax property ID must be an integer.',
            'tax_property_id.exists' => 'The selected tax property does not exist.',
            
            'tax_assessment_id.integer' => 'The tax assessment ID must be an integer.',
            'tax_assessment_id.exists' => 'The selected tax assessment does not exist.',
            
            'amount_paying.required' => 'The payment amount is required.',
            'amount_paying.numeric' => 'The payment amount must be a valid number.',
            'amount_paying.min' => 'The payment amount must be at least 0.01.',
            'amount_paying.max' => 'The payment amount cannot exceed 999,999.99.',
            
            'payment.required' => 'The payment amount is required.',
            'payment.numeric' => 'The payment amount must be a valid number.',
            'payment.min' => 'The payment amount must be at least 0.01.',
            'payment.max' => 'The payment amount cannot exceed 999,999.99.',
            
            'pay_method.required' => 'The payment method is required.',
            'pay_method.string' => 'The payment method must be a string.',
            'pay_method.in' => 'The payment method must be either online or cash.',
            
            'pay_date.required' => 'The payment date is required.',
            'pay_date.date' => 'The payment date must be a valid date.',
            
            'currency.required' => 'The currency is required.',
            'currency.size' => 'The currency must be exactly 3 characters.',
            'currency.in' => 'The currency must be one of: lkr, usd, eur, gbp.',
            
            'officer_id.integer' => 'The officer ID must be an integer (optional for public payments).',
            
            'success_url.required' => 'The success URL is required.',
            'success_url.url' => 'The success URL must be a valid URL.',
            'success_url.max' => 'The success URL cannot exceed 500 characters.',
            
            'cancel_url.required' => 'The cancel URL is required.',
            'cancel_url.url' => 'The cancel URL must be a valid URL.',
            'cancel_url.max' => 'The cancel URL cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'tax_payee_id' => 'tax payee',
            'tax_property_id' => 'tax property',
            'tax_assessment_id' => 'tax assessment',
            'amount_paying' => 'payment amount',
            'payment' => 'payment amount',
            'pay_method' => 'payment method',
            'pay_date' => 'payment date',
            'currency' => 'currency',
            'officer_id' => 'officer',
            'success_url' => 'success URL',
            'cancel_url' => 'cancel URL',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert amounts to decimal if they're strings
        if ($this->has('amount_paying') && is_string($this->amount_paying)) {
            $this->merge([
                'amount_paying' => (float) $this->amount_paying
            ]);
        }

        if ($this->has('payment') && is_string($this->payment)) {
            $this->merge([
                'payment' => (float) $this->payment
            ]);
        }

        // Set default currency if not provided
        if (!$this->has('currency')) {
            $this->merge([
                'currency' => 'lkr'
            ]);
        }

        // Set default payment method if not provided
        if (!$this->has('pay_method')) {
            $this->merge([
                'pay_method' => 'online'
            ]);
        }

        // Set default payment date if not provided
        if (!$this->has('pay_date')) {
            $this->merge([
                'pay_date' => now()->toDateString()
            ]);
        }
    }
}