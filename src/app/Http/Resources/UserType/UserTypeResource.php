<?php

namespace App\Http\Resources\UserType;

use App\Http\Resources\BaseResource;
use App\Models\UserType;

class UserTypeResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $item
     * @return array
     */
    public function process($item): array
    {
        /**
         * @var UserType $item
         */
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
        ];
    }
}