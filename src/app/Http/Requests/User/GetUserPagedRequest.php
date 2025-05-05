<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class GetUserPagedRequest extends BaseRequest
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
        ];
    }
}
