<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0.01',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'stock' => 'required|integer|min:0',
            'enable' => 'sometimes|boolean',
        ];

        if ($this->isMethod('post') || $this->isMethod('patch')) {
            $rules['photo'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome do produto é obrigatório.',
            'price.required' => 'O preço é obrigatório.',
            'price.min' => 'O preço deve ser maior que zero.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A imagem deve ser JPEG, PNG, JPG ou GIF.',
            'photo.max' => 'A imagem não pode ser maior que 2MB.',
            'stock.required' => 'O estoque é obrigatório.',
        ];
    }

    protected function prepareForValidation()
    {
        if (!$this->hasFile('photo')) {
            $this->merge(['photo' => null]);
        }
    }
}
