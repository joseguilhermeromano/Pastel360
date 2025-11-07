<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'mail' => 'required|email|unique:clients,mail,' . $this->route('client'),
            'phone' => 'required|string|max:20',
            'birthdate' => 'required|date',
            'place' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'zipcode' => 'required|string|max:9',
            'district' => 'required|string|max:255',
            'complement' => 'nullable|string|max:255'
        ];
    }
}
