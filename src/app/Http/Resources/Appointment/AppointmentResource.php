<?php

namespace App\Http\Resources\Appointment;

use App\Http\Resources\AppointmentStatus\AppointmentStatusResource;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Pet\PetResource;
use App\Http\Resources\User\UserResource;
use App\Models\Appointment;

class AppointmentResource extends BaseResource
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
         * @var Appointment $item
         */
        return [
            'id' => $item->getId(),
            'pet_id' => $item->getPetId(),
            'pet' => new PetResource($item->pet),
            'doctor_id' => $item->getDoctorId(),
            'doctor' => $this->when($item->doctor, new UserResource($item->doctor)),
            'created_by' => $item->getCreatedBy(),
            'date' => $item->getDate(),
            'time_of_day' => $item->getTimeOfDay(),
            'status_id' => $item->getStatusId(),
            'status' => new AppointmentStatusResource($item->status),
            'symptoms' => $item->getSymptoms(),
            'created_at' => $item->getCreatedAt(),
            'updated_at' => $item->getUpdatedAt(),
        ];
    }
}
