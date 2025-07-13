<?php

namespace App\Core;

use PDO;

/**
 * Singleton class to manage the PDO database connection.
 *
 * Loads database configuration from environment variables using Dotenv
 * and creates a single PDO instance to be reused throughout the app.
 */
class DB
{
    /**
     * @var PDO|null Holds the singleton PDO instance.
     */
    public static ?PDO $pdo = null;

    /**
     * Get the PDO database connection instance.
     *
     * If the connection has already been established, returns the existing PDO instance.
     * Otherwise, loads environment variables, initializes a new PDO connection with
     * settings for MySQL using utf8mb4 charset, and returns the PDO instance.
     *
     * @return PDO The PDO database connection instance.
     * @throws \Dotenv\Exception\InvalidPathException If environment file cannot be loaded.
     * @throws \PDOException If the connection to the database fails.
     */
    public static function connect(): PDO
    {
        if (self::$pdo) return self::$pdo;

        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '../../../');
        $dotenv->load();

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_DATABASE']
        );

        self::$pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        return self::$pdo;
    }
}
