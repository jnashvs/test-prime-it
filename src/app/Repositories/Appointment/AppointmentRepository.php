<?php

namespace App\Repositories\Appointment;

use App\Http\Requests\Appointment\GetAppointmentPagedRequest;
use App\Models\AppointmentStatus;
use App\Models\Factories\Appointment\AppointmentFactory;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Modules\Exceptions\FatalRepositoryException;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    private AppointmentFactory $appointmentFactory;

    public function __construct(AppointmentFactory $appointmentFactory)
    {
        $this->appointmentFactory = $appointmentFactory;
    }

    public function getById(int $id): ?Appointment
    {
        return $this->appointmentFactory->getById($id);
    }

    public function get(?GetAppointmentPagedRequest $request = null): mixed
    {
        if (!$request) {
            $request = new GetAppointmentPagedRequest();
        }

        $searchKeyword = $request->input('search', null);
        $pageIndex = $request->input('pageIndex', 0);
        $pageSize = $request->input('pageSize', 20);
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = $request->boolean('sortDesc');
        $columns = $request->input('columns', ['name', 'registration_number', 'breed']);
        $animalTypeId = $request->input('animalTypeId');
        $assignedUserId = $request->input('assignedUserId');
        $dayFilter = $request->input('date');

        return $this->appointmentFactory->getFilter(
            $searchKeyword,
            $columns,
            $pageIndex - 1,
            $pageSize,
            $sortBy,
            $sortDesc,
            $assignedUserId,
            $dayFilter,
            $animalTypeId,
        );
    }

    /**
     * @param Pet $pet
     * @param User $doctor
     * @param User $createdBy
     * @param AppointmentStatus $status
     * @param string $date
     * @param string $symptoms
     * @param ?string $timeOfDay
     * @return Appointment
     * @throws FatalRepositoryException
     */
    public function create(
        Pet $pet,
        User $doctor,
        User $createdBy,
        AppointmentStatus $status,
        string $date,
        string $symptoms,
        ?string $timeOfDay,
    ): Appointment
    {
        return $this->appointmentFactory->create(
            $pet,
            $doctor,
            $createdBy,
            $status,
            $date,
            $symptoms,
            $timeOfDay
        );
    }

    /**
     * @param Appointment $appointment
     * @param Pet $pet
     * @param User $doctor
     * @param User $createdBy
     * @param AppointmentStatus $status
     * @param string $date
     * @param string $symptoms
     * @param ?string $timeOfDay
     * @return Appointment
     * @throws FatalRepositoryException
     */
    public function update(
        Appointment $appointment,
        Pet $pet,
        User $doctor,
        User $createdBy,
        AppointmentStatus $status,
        string $date,
        string $symptoms,
        ?string $timeOfDay,
    ): Appointment
    {
        return $this->appointmentFactory->update(
            $appointment,
            $pet,
            $doctor,
            $createdBy,
            $status,
            $date,
            $symptoms,
            $timeOfDay
        );
    }

    public function delete(Appointment $appointment): bool
    {
        return $this->appointmentFactory->delete($appointment);
    }
}
