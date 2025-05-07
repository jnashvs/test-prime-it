<?php

namespace App\Models\Factories\Appointment;

use App\Models\AppointmentStatus;
use App\Models\Factories\AbstractFactory;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Modules\Exceptions\FatalRepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
class AppointmentFactory extends AbstractFactory
{
    public function __construct()
    {
        parent::__construct(Appointment::class);
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
        $appointment = new Appointment();

        return $this->update(
            $appointment,
            $pet,
            $doctor,
            $createdBy,
            $status,
            $date,
            $symptoms,
            $timeOfDay,
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
        $appointment->setPet($pet);
        $appointment->setDoctor($doctor);
        $appointment->setCreatedBy($createdBy);
        $appointment->setStatus($status);
        $appointment->setDate($date);
        $appointment->setSymptoms($symptoms);
        $appointment->setTimeOfDay($timeOfDay);

        if (!$appointment->save()) {
            throw new FatalRepositoryException('Failed to create/update a appointment.');
        }

        return $appointment;
    }

    public function getFilter(
        ?string $searchKeyword,
        array $columns,
        int $pageIndex,
        int $pageSize,
        string $sortBy,
        bool $sortDesc,
        ?int $assignedUserId,
        ?string $dayFilter,
        ?int $animalTypeId
    ): mixed {

        try {
            $query = Appointment::query()->with([
                'pet.owner',
                'doctor',
                'status'
            ]);

            $authUser = Auth::user();

            if ($authUser) {
                if ($authUser->hasRole('doctor')) {
                    $query->where('doctor_id', $authUser->getId());
                } elseif ($authUser->hasRole('user')) { // Assuming 'user' is the role for pet owners
                    $query->whereHas('pet', function ($petQuery) use ($authUser) {
                        $petQuery->where('owner_id', $authUser->getId());
                    });
                } elseif ($authUser->hasRole('receptionist')) {
                    if ($assignedUserId) {
                        $query->where('doctor_id', $assignedUserId);
                    }
                } else {
                    if ($assignedUserId) {
                        $query->where('doctor_id', $assignedUserId);
                    }
                }
            } else {
                if ($assignedUserId) {
                    $query->where('doctor_id', $assignedUserId);
                }
            }

            if ($searchKeyword) {
                $query->where(function ($q) use ($searchKeyword) {
                    $q->orWhere('time_of_day', 'LIKE', '%' . $searchKeyword . '%')
                      ->orWhere('symptoms', 'LIKE', '%' . $searchKeyword . '%');

                    $q->orWhereHas('pet', function ($petQuery) use ($searchKeyword) {
                        $petQuery->where('name', 'LIKE', '%' . $searchKeyword . '%');
                    });

                    $q->orWhereHas('doctor', function ($docQuery) use ($searchKeyword) {
                        $docQuery->where('name', 'LIKE', '%' . $searchKeyword . '%'); // Assumes User model has 'name'
                    });

                    $q->orWhereHas('status', function ($statusQuery) use ($searchKeyword) {
                        $statusQuery->where('name', 'LIKE', '%' . $searchKeyword . '%'); // Assumes AppointmentStatus model has 'name'
                    });
                });
            }

            if ($assignedUserId) {
                $query->where('doctor_id', $assignedUserId);
            }

            if ($dayFilter) {
                $query->whereDate('date', $dayFilter);
            }

            if ($animalTypeId) {
                $query->whereHas('pet', function ($q) use ($animalTypeId) {
                    $q->where('animal_type_id', $animalTypeId);
                });
            }

            $sortOrder = $sortDesc ? 'DESC' : 'ASC';
            $query->orderBy($sortBy, $sortOrder);

            $count = $query->count();
            $rows = $query->skip($pageIndex * $pageSize)->take($pageSize)->get();

            return [
                'count' => $count,
                'rows' => $rows,
            ];
        } catch (\Throwable $th) {
            Log::error('Error in get function: ' . $th->getMessage());
            return [
                'count' => 0,
                'rows' => new Collection(),
            ];
        }
    }
}
