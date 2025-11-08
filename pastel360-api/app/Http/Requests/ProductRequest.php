<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    use ValidationRules;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => $this->stringMax255(),
            'description' => $this->stringMax255(),
            'price' => 'sometimes|numeric|min:0',
            'photo' => $this->stringMax255(),
            'stock' => 'sometimes|integer|min:0',
            'enable' => 'sometimes|boolean',
        ];

        if ($this->isMethod('post')) {
            $rules['name'] = $this->requiredStringMax255();
            $rules['description'] = $this->requiredStringMax255();
            $rules['price'] = 'required|numeric|min:0';
            $rules['photo'] = $this->requiredStringMax255();
            $rules['stock'] = 'required|integer|min:0';
            $rules['enable'] = 'required|boolean';
        }

        return $rules;
    }
}
