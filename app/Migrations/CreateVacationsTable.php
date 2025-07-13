<?php

namespace App\Migrations;

use PDO;
use App\Core\Migration;

/**
 * Migration to create the `vacations` table.
 *
 * Defines the schema for storing vacation requests by users,
 * including start and end dates, status, reason, and timestamps.
 */
class CreateVacationsTable extends Migration
{
    /**
     * Run the migration to create the `vacations` table if it does not exist.
     *
     * The table includes columns for:
     * - id (primary key, auto-increment)
     * - user_id (integer, foreign key reference to users)
     * - start_date (date, required)
     * - end_date (date, required)
     * - status (enum: 'pending', 'approved', 'declined', default 'pending')
     * - reason (text, optional)
     * - created_at (timestamp, default current time)
     *
     * @param PDO $pdo PDO database connection.
     * @return void
     */
    public function up(PDO $pdo)
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS vacations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
                reason TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
}
