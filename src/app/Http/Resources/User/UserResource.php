<?php

namespace App\Http\Resources\User;

use App\Http\Resources\BaseResource;
use App\Http\Resources\Pet\PetResource;
use App\Http\Resources\UserType\UserTypeResource;
use App\Models\User;

class UserResource extends BaseResource
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
         * @var User $item
         */

        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'email' => $item->getEmail(),
            'user_type_id' => $item->getUserTypeId(),
            'user_type' => new UserTypeResource($item->userType),
            'email_verified_at' => $item->getEmailVerifiedAt(),
        ];
    }
}
