<?php
require __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Models\Vacation;

echo "Seeding database...\n";

// Helper to create user if not exists
function createUserIfNotExists(array $data): User
{
    $existing = User::findByEmployeeCode($data['employee_code']);
    if ($existing) {
        echo "User with employee_code {$data['employee_code']} already exists, skipping.\n";
        return $existing;
    }
    return User::create($data);
}

// Create a manager
$manager = createUserIfNotExists([
    'name' => 'Alice Manager',
    'email' => 'alice.manager@example.com',
    'password' => password_hash('password', PASSWORD_DEFAULT),
    'role' => 'manager',
    'employee_code' => 1000001,
]);

// Create employees
$employeesData = [
    [
        'name' => 'Bob Employee',
        'email' => 'bob.employee@example.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'employee',
        'employee_code' => 1000002,
    ],
    [
        'name' => 'Carol Employee',
        'email' => 'carol.employee@example.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'employee',
        'employee_code' => 1000003,
    ],
    [
        'name' => 'Dave Employee',
        'email' => 'dave.employee@example.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'employee',
        'employee_code' => 1000004,
    ],
    [
        'name' => 'Eve Employee',
        'email' => 'eve.employee@example.com',
        'password' => password_hash('password', PASSWORD_DEFAULT),
        'role' => 'employee',
        'employee_code' => 1000005,
    ],
];

$employees = [];
foreach ($employeesData as $edata) {
    $employees[] = createUserIfNotExists($edata);
}

// Create some vacations for employees
function createVacationIfNotExists(array $data)
{
    $existing = Vacation::findByUserId($data['user_id']);
    foreach ($existing as $vac) {
        if ($vac->startDate === $data['start_date'] && $vac->endDate === $data['end_date']) {
            echo "Vacation from {$data['start_date']} to {$data['end_date']} already exists for user {$data['user_id']}, skipping.\n";
            return;
        }
    }
    Vacation::create($data);
}

$vacationsData = [
    [
        'user_id' => $employees[0]->id,
        'start_date' => '2025-08-01',
        'end_date' => '2025-08-07',
        'reason' => 'Family trip',
    ],
    [
        'user_id' => $employees[1]->id,
        'start_date' => '2025-07-15',
        'end_date' => '2025-07-20',
        'reason' => 'Beach holiday',
    ],
    [
        'user_id' => $employees[2]->id,
        'start_date' => '2025-09-10',
        'end_date' => '2025-09-15',
        'reason' => 'Conference',
    ],
    [
        'user_id' => $employees[3]->id,
        'start_date' => '2025-12-24',
        'end_date' => '2025-12-31',
        'reason' => 'Christmas vacation',
    ],
];

foreach ($vacationsData as $vdata) {
    createVacationIfNotExists($vdata);
}

echo "Seeding complete.\n";
