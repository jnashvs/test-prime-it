<?php

namespace App\Http\Requests\Pet;

use App\Http\Requests\BaseRequest;

class GetPetPagedRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search' => 'nullable|string',
            'pageIndex' => 'nullable|integer|min:0',
            'pageSize' => 'nullable|integer|min:5',
            'sortBy' => 'nullable|string',
            'sortDesc' => 'nullable|boolean',
            'typeId' => 'nullable|integer',
            'dateFrom' => 'nullable|date',
            'dateTo' => 'nullable|date',
        ];
    }
}
