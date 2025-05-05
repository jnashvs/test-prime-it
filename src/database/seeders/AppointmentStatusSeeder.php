<?php

namespace Database\Seeders;

use App\Models\AppointmentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $statuses = [
            ['id' => AppointmentStatus::REQUESTED, 'name' => 'requested', 'description' => 'Appointment requested by user'],
            ['id' => AppointmentStatus::PENDING_ASSIGNMENT, 'name' => 'pending assignment', 'description' => 'Awaiting doctor assignment'],
            ['id' => AppointmentStatus::ASSIGNED, 'name' => 'assigned', 'description' => 'Doctor assigned to appointment'],
            ['id' => AppointmentStatus::COMPLETED, 'name' => 'completed', 'description' => 'Appointment completed'],
            ['id' => AppointmentStatus::CANCELLED, 'name' => 'cancelled', 'description' => 'Appointment cancelled'],
        ];

        foreach ($statuses as $status) {
            AppointmentStatus::updateOrCreate(
                ['id' => $status['id']],
                ['name' => $status['name'], 'description' => $status['description']]
            );
        }
    }
}
