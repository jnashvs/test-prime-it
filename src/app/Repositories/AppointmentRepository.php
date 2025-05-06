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
        $sortBy = $request->input('sortBy', 'date');
        $sortDesc = $request->boolean('sortDesc');
        $columns = $request->input('columns', ['date', 'time_of_day']);
        $assignedUserId = $request->input('assignedUserId');
        $dayFilter = $request->input('dayFilter');
        $animalTypeId = $request->input('animalTypeId');

        return $this->appointmentFactory->getFilter(
            $searchKeyword,
            $columns,
            $pageIndex - 1,
            $pageSize,
            $sortBy,
            $sortDesc,
            $assignedUserId,
            $dayFilter,
            $animalTypeId
        );
    }

    // ... existing methods ...
}