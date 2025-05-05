<?php

namespace App\Http\Resources\AppointmentStatus;

use App\Http\Resources\BaseResource;
use App\Models\AppointmentStatus;

class AppointmentStatusResource extends BaseResource
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
         * @var AppointmentStatus $item
         */
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'description' => $item->getDescription(),
        ];
    }
}