<?php

namespace App\Http\Controllers\Appointment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\EditAppointmentRequest;
use App\Http\Requests\Appointment\GetAppointmentPagedRequest;
use App\Http\Resources\Appointment\AppointmentResource;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\Pet;
use App\Models\User;
use App\Modules\Exceptions\FatalModuleException;
use App\Modules\Exceptions\ValidationException;
use App\Repositories\Appointment\AppointmentRepositoryInterface;
use App\Repositories\AppointmentStatus\AppointmentStatusRepositoryInterface;
use App\Repositories\Pet\PetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

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

    /**
     * @throws AuthorizationException
     */
    public function index(GetAppointmentPagedRequest $request)
    {
        $this->authorize('view all appointments');
        $values = $this->appointmentRepository->get($request);

        return $this->apiResponsePages(AppointmentResource::collection($values['rows']), $values['count']);
    }

    /**
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function getById(int $id)
    {
        $this->authorize('view all appointments');
        $appointment = $this->validateAppointment($id);
        return new AppointmentResource($appointment);
    }

    /**
     * @throws FatalModuleException
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function create(EditAppointmentRequest $request)
    {
        $this->authorize('create appointments');
        $user = $this->validateUser();
        $pet = $this->validatePet($request->input('pet_id'));

        $doctorId = $request->input('doctor_id');
        $statusId = $request->input('status_id', AppointmentStatus::REQUESTED);

        $doctor = null;

        if ($user->isReceptionist() && $user->hasRole('receptionist')) {
            $doctor = $doctorId ? $this->validateDoctor($doctorId) : null;
            $status = $this->validateAppointmentStatus($statusId);
        } else {
            $status = $this->appointmentStatusRepository->getById(AppointmentStatus::REQUESTED);
        }

        $appointment = $this->appointmentRepository->create(
            $pet,
            $doctor,
            $user,
            $status,
            $request->input('date'),
            $request->input('symptoms'),
            $request->input('time_of_day'),
        );

        return $this->apiResponse(new AppointmentResource($appointment));
    }

    /**
     * @throws FatalModuleException
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function update(EditAppointmentRequest $request, int $id)
    {
        $this->authorize('edit appointments');

        $appointment = $this->validateAppointment($id);
        $user = $this->validateUser();

        $pet = $appointment->getPet();
        $doctor = $appointment->getDoctor();
        $status = $appointment->getStatus();
        $date = $request->input('date');
        $symptoms = $request->input('symptoms');
        $timeOfDay = $request->input('time_of_day');

        if ($user->isDoctor() && $user->hasRole('doctor')) {
            if ($appointment->getDoctorId() !== $user->getId()) {
                throw new AuthorizationException("You can only update appointments that are assigned to you.");
            }

            $statusId = $request->input('status_id');
            if (!in_array($statusId, [AppointmentStatus::COMPLETED, AppointmentStatus::CANCELLED])) {
                throw new ValidationException("As a doctor, you can only set the appointment status to Completed or Cancelled.");
            }

            $status = $this->validateAppointmentStatus($statusId);
            $pet = $this->validatePet($request->input('pet_id'));

        } elseif ($user->isUser() && $user->hasRole('user')) {

            if ($pet->getOwnerId() !== $user->getId()) {
                throw new AuthorizationException("You can only update appointments for your own pets.");
            }

        } elseif ($user->isReceptionist() && $user->hasRole('receptionist')) {
            $pet = $this->validatePet($request->input('pet_id'));
            $status = $this->validateAppointmentStatus($request->input('status_id'));
            $doctorId = $request->input('doctor_id');

            if ($doctorId !== null) {
                $doctor = $this->validateDoctor($doctorId);
            } else {
                $doctor = null;
            }

        } else {
            throw new AuthorizationException("Your user role does not have the necessary permissions to update appointments in this manner.");
        }

        $appointment = $this->appointmentRepository->update(
            $appointment,
            $pet,
            $doctor,
            $user,
            $status,
            $date,
            $symptoms,
            $timeOfDay
        );

        return $this->apiResponse(new AppointmentResource($appointment));
    }

    /**
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function delete(int $id)
    {
        $this->authorize('delete appointments');
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
