<?php

namespace App\Http\Requests\Appointment;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class EditAppointmentRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'pet_id' => 'required|integer',
            'doctor_id' => 'nullable|integer',
            'created_by' => 'nullable|integer',
            'date' => 'required|date',
            'time_of_day' => 'nullable|string',
            'status_id' => 'required|integer',
            'symptoms' => 'nullable|string',
        ];
    }

    /**
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'pet_id' => 'pet',
            'doctor_id' => 'doctor',
            'date' => 'appointment date',
            'time_of_day' => 'time of day',
            'status_id' => 'status',
            'symptoms' => 'symptoms',
        ];
    }
}
