<?php

namespace Modules\Invoices\Application\Presentation\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customerName' => 'required|string|max:255|unique:invoices,customer_name',
            'customerEmail' => 'required|email|max:255|unique:invoices,customer_email',
            'products' => 'required|array|min:1',

            'products.*.name' => 'required|string|max:255',
            'products.*.price' => 'required|integer|min:0',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }
}
