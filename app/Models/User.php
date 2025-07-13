<?php
namespace App\Models;

use PDO;
use App\Core\DB;

/**
 * User model representing a user record in the 'users' table.
 *
 * Extends the base Model class to add user-specific methods and properties.
 */
class User extends Model
{
    /**
     * The database table associated with this model.
     *
     * @var string
     */
    protected static string $table = 'users';

    /**
     * Fields to hide from output (e.g., API responses).
     *
     * @var array
     */
    protected static array $hidden = ['password'];

    /**
     * User ID (primary key).
     *
     * @var int
     */
    public int $id;

    /**
     * User's full name.
     *
     * @var string
     */
    public string $name;

    /**
     * User's email address.
     *
     * @var string
     */
    public string $email;

    /**
     * User's hashed password.
     *
     * @var string
     */
    public string $passwordHash;

    /**
     * User's role, e.g., 'employee' or 'manager'.
     *
     * @var string
     */
    public string $role;

    /**
     * Unique employee code number.
     *
     * @var int
     */
    public int $employee_code;

    /**
     * Construct a User instance from an array of data.
     *
     * @param array $data Associative array of user properties.
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->passwordHash = $data['password'] ?? '';
        $this->role = $data['role'] ?? 'employee';
        $this->employee_code = $data['employee_code'] ?? 0;
    }

    /**
     * Find a user by email address.
     *
     * @param string $email The email to search for.
     * @return User|null Returns User instance if found, null otherwise.
     */
    public static function findByEmail(string $email): ?User
    {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new self($data);
    }

    /**
     * Find a user by employee code.
     *
     * @param int $code The employee code to search for.
     * @return User|null Returns User instance if found, null otherwise.
     */
    public static function findByEmployeeCode(int $code): ?User
    {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE employee_code = ?");
        $stmt->execute([$code]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new self($data);
    }

    /**
     * Verify if a given plaintext password matches the stored hash.
     *
     * @param string $password Plaintext password to verify.
     * @return bool True if password matches, false otherwise.
     */
    public function verifyPassword(string $password): bool
    {
         return password_verify($password, $this->passwordHash);
    }

    /**
     * Get all vacations associated with this user.
     *
     * @return array List of Vacation objects.
     */
    public function vacations(): array
    {
        return Vacation::findByUserId($this->id);
    }

    /**
     * Check if this user has manager role.
     *
     * @return bool True if role is 'manager', false otherwise.
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Create a new user record.
     *
     * @param array $data Associative array of user data.
     * @return User Newly created User instance.
     */
    public static function create(array $data): User
    {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, employee_code) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role'],
            $data['employee_code']
        ]);

        $id = $pdo->lastInsertId();
        return self::find($id);
    }

    /**
     * Update the current user record with new data.
     *
     * @param array $data Associative array of updated user data.
     * @throws \LogicException if called on a User without ID.
     * @return User Updated User instance.
     */
    public function save(array $data): User
    {
        if (empty($this->id)) {
            throw new \LogicException('Cannot call save() on a model without an ID.');
        }

        $pdo = DB::connect();

        $sql = "
        UPDATE users SET
            name = :name,
            email = :email,
            role = :role,
            employee_code = :employee_code";

        $params = [
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':role'          => $data['role'],
            ':employee_code' => $data['employee_code'],
            ':id'            => $this->id,
        ];

        if (!empty($this->password)) {
            $sql .= ", password = :password";
            $params[':password'] = $data['password'];
        }

        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Return the fresh, updated user model
        return self::find($this->id);
    }

    /**
     * Delete the current user record from the database.
     *
     * @throws \LogicException if called on a User without ID.
     * @return bool True on success, false on failure.
     */
    public function delete(): bool
    {
        if (empty($this->id)) {
            throw new \LogicException('Cannot call delete() on a model without an ID.');
        }

        $pdo = DB::connect();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$this->id]);
    }
}
