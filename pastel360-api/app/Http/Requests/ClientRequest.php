<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    use ValidationRules;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $clientId = $this->route('client');

        $rules = [
            'name' => $this->stringMax255(),
            'mail' => 'sometimes|email|unique:clients,mail,' . $clientId,
            'phone' => 'sometimes|string|max:20',
            'birthdate' => 'sometimes|date',
            'place' => $this->stringMax255(),
            'number' => 'sometimes|string|max:10',
            'zipcode' => 'sometimes|string|max:9',
            'district' => $this->stringMax255(),
            'complement' => 'nullable|string|max:255'
        ];

        if ($this->isMethod('post')) {
            $rules['name'] = $this->requiredStringMax255();
            $rules['mail'] = 'required|email|unique:clients,mail';
            $rules['phone'] = 'required|string|max:20';
            $rules['birthdate'] = 'required|date';
            $rules['place'] = $this->requiredStringMax255();
            $rules['number'] = 'required|string|max:10';
            $rules['zipcode'] = 'required|string|max:9';
            $rules['district'] = $this->requiredStringMax255();
        }

        return $rules;
    }
}
