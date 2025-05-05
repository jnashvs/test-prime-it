<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Validation\Rules;

class EditUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ];
    }

    public function messages()
    {
        return [
            'firstName.required' => 'O nome é obrigatório.',
            'firstName.min' => 'O primeiro nome deve ter pelo menos :min caracteres.',
            'firstName.max' => 'O primeiro nome não pode exceder :max caracteres.',
        ];
    }
}
