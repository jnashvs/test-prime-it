# Pet Management API - "O Patusco" Veterinary Clinic

This project is a solution for the appointment and management challenge at the "O Patusco" veterinary clinic. The goal is to optimize doctor scheduling and improve client experience by enabling online appointment booking and efficient management.

## Challenge

The clinic faces issues with long waiting lines due to lack of prior appointments and difficulty in planning doctors' schedules. This system was developed to:

- Allow clients to book appointments online
- Facilitate appointment management by the receptionist
- Allow doctors to view and edit only their assigned appointments

## Features

### For Clients
- Book appointments by providing:
  - Person's name
  - Email
  - Animal's name
  - Animal type (dog, cat, etc.)
  - Animal's age
  - Symptoms
  - Date and period (morning/afternoon)

### For Receptionist
- View appointments by date and animal type
- Assign appointments to doctors
- Create, edit, and delete any appointment

### For Doctor
- View assigned appointments, filter by day and animal type
- Edit only appointments assigned to them
- Cannot delete any appointment

## Technologies Used

- **Backend:** Laravel 10/11 (PHP 8+)
- **Frontend:** VueJS 3 (Element-Plus, etc.)
- **Database:** MySQL/MariaDB (configurable)
- **Testing:** PHPUnit (unit and feature tests)
- **Others:** Composer, Node.js & npm

## Project Structure

- `app/Http/Controllers/Pet/PetController.php` - Main controller for pets
- `app/Repositories/` - Data access repositories
- `app/Models/` - Eloquent models (Pet, AnimalType, User)
- `database/factories/` - Factories for testing
- `src/tests/Feature/` and `src/tests/Unit/` - Automated tests

## Setup

1. **Clone the repository:**
    ```bash
    git clone <your-repo-url>
    cd test-prime-it
    ```

2. **Start the containers using Docker Compose:**
    ```bash
    docker compose up -d --build
    ```

3. **Install dependencies:**
    ```bash
    composer install
    ```

4. **Configure environment:**
    ```bash
    cp .example.env .env
    ```
    Edit `.env` according to your environment (database, mail, etc).

5. **Generate application key:**
    ```bash
    php artisan key:generate
    ```

6. **Run migrations and seeders:**
    ```bash
    php artisan migrate --seed
    ```

7. **Start the development server:**
    ```bash
    php artisan serve
    ```

## Main API Endpoints

- Pets
- Appointments
- Animal Types
- Doctors

Other endpoints for animal types, appointments, and doctors are available as defined in the project routes.

## Testing

- **Run unit and feature tests:**
    ```bash
    php artisan test --env=testing
    ```
- Factories available for `Pet`, `AnimalType`, and `User`.

## Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

## License

[MIT](LICENSE)