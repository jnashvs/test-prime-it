<?php

namespace App\Http\Requests\Pet;

use App\Http\Requests\BaseRequest;
use App\Models\Pet;
use Illuminate\Validation\Rule;

class EditPetRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'registration_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Pet::class)->ignore($this->route('id')),
            ],
            'animal_type_id' => 'required|integer',
            'breed' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
        ];
    }
}
