<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\EditAppointmentRequest;
use App\Http\Requests\Appointment\GetAppointmentPagedRequest;
use App\Http\Requests\Pet\EditPetRequest;
use App\Http\Requests\Pet\GetPetPagedRequest;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Http\Resources\Pet\PetResource;
use App\Models\AnimalType;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Pet;
use App\Models\User;
use App\Modules\Exceptions\FatalModuleException;
use App\Modules\Exceptions\ValidationException;
use App\Repositories\AnimalType\AnimalTypeRepositoryInterface;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Repositories\AppointmentStatus\AppointmentStatusRepositoryInterface;
use App\Repositories\Pet\PetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    private AppointmentRepositoryInterface $appointmentRepository;
    private UserRepositoryInterface $userRepository;
    private PetRepositoryInterface $petRepository;
    private AppointmentStatusRepositoryInterface $appointmentStatusRepository;

    public function __construct(
        AppointmentRepositoryInterface $appointmentRepository,
        UserRepositoryInterface $userRepository,
        PetRepositoryInterface $petRepository,
        AppointmentStatusRepositoryInterface $appointmentStatusRepository
    )
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->userRepository = $userRepository;
        $this->petRepository = $petRepository;
        $this->appointmentStatusRepository = $appointmentStatusRepository;
    }

    public function index(GetAppointmentPagedRequest $request)
    {
        $values = $this->appointmentRepository->get($request);

        return $this->apiResponsePages(AppointmentResource::collection($values['rows']), $values['count']);
    }

    /**
     * @throws ValidationException
     */
    public function getById(int $id)
    {
        $appointment = $this->validateAppointment($id);
        return new AppointmentResource($appointment);
    }

    /**
     * @throws FatalModuleException
     * @throws ValidationException
     */
    public function create(EditAppointmentRequest $request)
    {
        $pet = $this->validatePet($request->input('pet_id'));
        $doctor = $this->validateDoctor($request->input('doctor_id'));
        $appointmentStatus = $this->validateAppointmentStatus($request->input('status_id'));
        $user = $this->validateUser();

        $appointment = $this->appointmentRepository->create(
            $pet,
            $doctor,
            $user,
            $appointmentStatus,
            $request->input('date'),
            $request->input('time_of_day'),
            $request->input('symptoms')
        );

        return $this->apiResponse(new AppointmentResource($appointment));
    }

    /**
     * @throws FatalModuleException
     * @throws ValidationException
     */
    public function update(EditAppointmentRequest $request, int $id)
    {
        $appointment = $this->validateAppointment($id);
        $pet = $this->validatePet($request->input('pet_id'));
        $doctor = $this->validateDoctor($request->input('doctor_id'));
        $appointmentStatus = $this->validateAppointmentStatus($request->input('status_id'));
        $user = $this->validateUser();

        $appointment = $this->appointmentRepository->update(
            $appointment,
            $pet,
            $doctor,
            $user,
            $appointmentStatus,
            $request->input('date'),
            $request->input('time_of_day'),
            $request->input('symptoms')
        );

        return $this->apiResponse(new AppointmentResource($appointment));
    }

    /**
     * @throws ValidationException
     */
    public function delete(int $id)
    {
        $appointment = $this->validateAppointment($id);
        return $this->apiResponse($this->appointmentRepository->delete($appointment));
    }

    private function validateAppointment(int $id): ?Appointment
    {
        $appointment = $this->appointmentRepository->getById($id);

        if (!$appointment) {
            throw new ValidationException("The appointment does not exist.");
        }

        return $appointment;
    }

    /**
     * @throws FatalModuleException
     */
    private function validateUser(): ?User
    {
        return $this->userRepository->getCurrentUser();
    }

    private function validateDoctor(int $id): ?User
    {
        $doctor = $this->userRepository->getById($id);

        if (!$doctor || !$doctor->isDoctor()) {
            throw new ValidationException("The doctor does not exist.");
        }

        return $doctor;
    }

    private function validatePet(int $id): ?Pet
    {
        $pet = $this->petRepository->getById($id);

        if (!$pet) {
            throw new ValidationException("The pet does not exist.");
        }

        return $pet;
    }

    private function validateAppointmentStatus(int $id): ?AppointmentStatus
    {
        $appointmentStatus = $this->appointmentStatusRepository->getById($id);

        if (!$appointmentStatus) {
            throw new ValidationException("The Appointment Status does not exist.");
        }

        return $appointmentStatus;
    }
}
