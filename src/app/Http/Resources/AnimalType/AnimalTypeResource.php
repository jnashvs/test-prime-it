<?php

namespace App\Http\Resources\AnimalType;

use App\Http\Resources\BaseResource;
use App\Models\AnimalType;

class AnimalTypeResource extends BaseResource
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
         * @var AnimalType $item
         */
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
        ];
    }
}
