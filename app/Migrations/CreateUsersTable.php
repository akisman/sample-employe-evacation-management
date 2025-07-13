<?php

namespace App\Migrations;

use PDO;
use App\Core\Migration;

/**
 * Migration to create the `users` table.
 *
 * Defines the schema for storing user information including
 * name, email, password, role, employee code, and timestamps.
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migration to create the `users` table if it does not exist.
     *
     * The table includes columns for:
     * - id (primary key, auto-increment)
     * - name (string, required)
     * - email (unique string, required)
     * - password (string, required)
     * - role (enum: 'employee' or 'manager', default 'employee')
     * - employee_code (unique unsigned int, between 1000000 and 9999999)
     * - created_at (timestamp, default current time)
     *
     * @param PDO $pdo PDO database connection.
     * @return void
     */
    public function up(PDO $pdo)
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role ENUM('employee', 'manager') NOT NULL DEFAULT 'employee',
                employee_code INT UNSIGNED NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CHECK (employee_code BETWEEN 1000000 AND 9999999)
            )
        ");
    }
}
