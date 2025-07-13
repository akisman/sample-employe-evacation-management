<?php

namespace App\Core;

use PDO;

/**
 * Abstract base class for database migrations.
 *
 * Each migration should extend this class and implement the `up` method,
 * which contains the logic to apply schema changes or data transformations.
 */
abstract class Migration
{
    /**
     * Apply the migration changes to the database.
     *
     * This method must be implemented by subclasses to define the
     * specific migration logic, using the provided PDO connection.
     *
     * @param PDO $pdo The PDO database connection instance.
     * @return void
     */
    abstract public function up(PDO $pdo);
}
