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
            'customer_id' => 'sometimes|integer|exists:customers,id',
            'status' => 'sometimes|string|in:pending,approved,delivered,canceled',
            'notes' => 'nullable|string|max:500',
            'items' => 'sometimes|array|min:1',
            'items.*.product_id' => 'sometimes|integer|exists:products,id',
            'items.*.quantity' => 'sometimes|integer|min:1',
            'items.*.unit_value' => 'sometimes|numeric|min:0.01'
        ];

        if ($this->isMethod('post')) {
            $rules['customer_id'] = 'required|integer|exists:customers,id';
            $rules['status'] = 'required|string|in:pending,approved,in_preparation,ready,delivered,canceled';
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.product_id'] = 'required|integer|exists:products,id';
            $rules['items.*.quantity'] = 'required|integer|min:1';
            $rules['items.*.unit_value'] = 'required|numeric|min:0.01';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'O campo cliente é obrigatório.',
            'customer_id.exists' => 'O cliente selecionado não existe.',
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'Status deve ser: pending, approved, in_preparation, ready, delivered ou canceled.',
            'items.required' => 'Pelo menos um item é obrigatório no pedido.',
            'items.array' => 'Os items devem ser um array.',
            'items.min' => 'Pelo menos um item é obrigatório no pedido.',
            'items.*.product_id.required' => 'O produto é obrigatório para cada item.',
            'items.*.product_id.exists' => 'Um dos produtos selecionados não existe.',
            'items.*.quantity.required' => 'A quantidade é obrigatória para cada item.',
            'items.*.quantity.min' => 'A quantidade deve ser no mínimo 1.',
            'items.*.unit_value.required' => 'O valor unitário é obrigatório para cada item.',
            'items.*.unit_value.min' => 'O valor unitário deve ser maior que zero.'
        ];
    }
}
