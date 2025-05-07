<?php

namespace App\Repositories\Appointment;

use App\Http\Requests\Appointment\GetAppointmentPagedRequest;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Pet;
use App\Models\User;

interface AppointmentRepositoryInterface
{
    public function getById(int $id): ?Appointment;

    /**
     * @param ?GetAppointmentPagedRequest $request
     * @return mixed
     */
    public function get(GetAppointmentPagedRequest $request = null): mixed;

    /**
     * @param Pet $pet
     * @param ?User $doctor
     * @param User $createdBy
     * @param AppointmentStatus $status
     * @param string $date
     * @param ?string $symptoms
     * @param ?string $timeOfDay
     * @return Appointment
     */
    public function create(
        Pet $pet,
        ?User $doctor,
        User $createdBy,
        AppointmentStatus $status,
        string $date,
        ?string $symptoms,
        ?string $timeOfDay,
    ): Appointment;

    /**
     * @param Appointment $appointment
     * @param Pet $pet
     * @param ?User $doctor
     * @param User $createdBy
     * @param AppointmentStatus $status
     * @param string $date
     * @param ?string $symptoms
     * @param ?string $timeOfDay
     * @return Appointment
     */
    public function update(
        Appointment $appointment,
        Pet $pet,
        ?User $doctor,
        User $createdBy,
        AppointmentStatus $status,
        string $date,
        ?string $symptoms,
        ?string $timeOfDay
    ): Appointment;

    public function delete(Appointment $appointment): bool;
}
