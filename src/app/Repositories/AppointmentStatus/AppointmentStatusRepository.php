<?php

namespace App\Repositories\AppointmentStatus;

use App\Models\AppointmentStatus;
use App\Models\Factories\AppointmentStatus\AppointmentStatusFactory;
use Illuminate\Database\Eloquent\Collection;

class AppointmentStatusRepository implements AppointmentStatusRepositoryInterface
{
    private AppointmentStatusFactory $appointmentStatusFactory;

    public function __construct(AppointmentStatusFactory $appointmentStatusFactory)
    {
        $this->appointmentStatusFactory = $appointmentStatusFactory;
    }

    public function getById(int $id): ?AppointmentStatus
    {
        return $this->appointmentStatusFactory->getById($id);
    }

    public function getAll(): Collection
    {
        return $this->appointmentStatusFactory->getAll();
    }
}
