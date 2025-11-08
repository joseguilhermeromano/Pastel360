<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'product_id' => 'sometimes|integer|exists:products,id',
            'client_id' => 'sometimes|integer|exists:clients,id',
            'quantity' => 'sometimes|integer|min:1',
            'unit_value' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:pending,approved,canceled,delivered',
            'notes' => 'nullable|string|max:500'
        ];

        if ($this->isMethod('post')) {
            $rules['product_id'] = 'required|integer|exists:products,id';
            $rules['client_id'] = 'required|integer|exists:clients,id';
            $rules['quantity'] = 'required|integer|min:1';
            $rules['unit_value'] = 'required|numeric|min:0';
            $rules['status'] = 'required|string|in:pending,approved,delivered,canceled';
        }

        return $rules;
    }
}
