<?php
namespace App\Models;

use PDO;
use App\Core\DB;

/**
 * Abstract base model class providing common ORM-like methods.
 *
 * Models extending this class should define the `$table` property
 * representing the database table and can define `$hidden` fields to exclude
 * when fetching records.
 */
abstract class Model
{
    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected static string $table;

    /**
     * Fields to exclude from results by default.
     *
     * @var array
     */
    protected static array $hidden = []; // Fields to exclude by default

    /**
     * Retrieve all records from the model's table.
     *
     * Optionally exclude additional fields merged with `$hidden`.
     *
     * @param array $excludeFields Additional fields to exclude from the results.
     * @return static[] Array of model instances.
     */
    public static function all(array $excludeFields = []): array
    {
        $pdo = DB::connect();
        $query = "SELECT * FROM " . static::getTableName();
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get hidden fields from child class or use empty array if not defined
        $classHidden = property_exists(static::class, 'hidden')
            ? static::$hidden
            : [];

        $fieldsToExclude = array_merge($classHidden, $excludeFields);

        if (!empty($fieldsToExclude)) {
            $results = array_map(function($item) use ($fieldsToExclude) {
                foreach ($fieldsToExclude as $field) {
                    unset($item[$field]);
                }
                return $item;
            }, $results);
        }

        return array_map(function($data) {
            return new static($data);
        }, $results);
    }

    /**
     * Find a single record by its ID.
     *
     * @param int $id The record's primary key.
     * @return static|null Returns an instance of the model or null if not found.
     */
    public static function find(int $id): ?static
    {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("SELECT * FROM " . static::getTableName() . " WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new static($data) : null;
    }

    /**
     * Get the database table name associated with this model.
     *
     * Automatically generates a table name if not explicitly set,
     * by lowercasing and pluralizing the class name.
     *
     * @return string The table name.
     */
    protected static function getTableName(): string
    {
        if (!isset(static::$table)) {
            // Auto-generate table name if not specified
            $className = substr(strrchr(get_called_class(), '\\'), 1);
            static::$table = strtolower($className) . 's'; // users, products, etc.
        }
        return static::$table;
    }
}
