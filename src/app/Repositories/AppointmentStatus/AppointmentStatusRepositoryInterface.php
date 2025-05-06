<?php

namespace App\Repositories\AppointmentStatus;

use App\Models\AppointmentStatus;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentStatusRepositoryInterface
{
    /**
     * @param int $id
     * @return ?AppointmentStatus
     */
    public function getById(int $id): ?AppointmentStatus;

    /**
     * @return Collection
     */
    public function getAll(): Collection;
}
