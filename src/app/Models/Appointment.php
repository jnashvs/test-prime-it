<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'doctor_id',
        'created_by',
        'date',
        'time_of_day',
        'status_id',
        'symptoms',
    ];

    // relationships
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'status_id');
    }

    // Getters and Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getPetId(): ?int
    {
        return $this->pet_id;
    }

    public function getPet(): ?Pet
    {
        return $this->pet;
    }

    public function setPet(Pet $pet): void
    {
        $this->pet_id = $pet->getId();
    }

    public function getDoctorId(): ?int
    {
        return $this->doctor_id;
    }

    public function getDoctor(): ?User
    {
        return $this->doctor;
    }

    public function setDoctor(?User $user): void
    {
        $this->doctor_id = $user?->getId();
    }

    public function getCreatedBy(): ?int
    {
        return $this->created_by;
    }

    public function setCreatedBy(?User $user): void
    {
        $this->created_by = $user?->getId();
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): void
    {
        $this->date = $date;
    }

    public function getTimeOfDay(): ?string
    {
        return $this->time_of_day;
    }

    public function setTimeOfDay(?string $timeOfDay): void
    {
        $this->time_of_day = $timeOfDay;
    }

    public function getStatusId(): ?int
    {
        return $this->status_id;
    }

    public function getStatus(): ?AppointmentStatus
    {
        return $this->status;
    }

    public function setStatus(?AppointmentStatus $status): void
    {
        $this->status_id = $status?->getId();
    }

    public function getSymptoms(): ?string
    {
        return $this->symptoms;
    }

    public function setSymptoms(?string $symptoms): void
    {
        $this->symptoms = $symptoms;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at?->toDateTimeString();
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at?->toDateTimeString();
    }
    }
