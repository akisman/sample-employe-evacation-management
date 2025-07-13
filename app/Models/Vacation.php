<?php

namespace App\Models;

use PDO;
use App\Core\DB;

/**
 * Vacation model representing a vacation request record in the 'vacations' table.
 *
 * Extends the base Model class to add vacation-specific methods and properties.
 */
class Vacation extends Model
{
    /**
     * The database table associated with this model.
     *
     * @var string
     */
    protected static string $table = 'vacations';

    /**
     * Fields to exclude from output.
     *
     * @var array
     */
    protected static array $hidden = []; // no hidden fields for now

    /**
     * Vacation request ID (primary key).
     *
     * @var int
     */
    public int $id;

    /**
     * User ID who requested the vacation.
     *
     * @var int
     */
    public int $userId;

    /**
     * Vacation start date in YYYY-MM-DD format.
     *
     * @var string
     */
    public string $startDate;

    /**
     * Vacation end date in YYYY-MM-DD format.
     *
     * @var string
     */
    public string $endDate;

    /**
     * Status of the vacation request ('pending', 'approved', 'declined').
     *
     * @var string
     */
    public string $status;

    /**
     * Optional reason text for the vacation request.
     *
     * @var string|null
     */
    public ?string $reason;

    /**
     * Timestamp of when the vacation request was created.
     *
     * @var string
     */
    public string $createdAt;

    /**
     * Construct a Vacation instance from an array of data.
     *
     * @param array $data Associative array of vacation properties.
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
        $this->startDate = $data['start_date'] ?? '';
        $this->endDate = $data['end_date'] ?? '';
        $this->status = $data['status'] ?? 'pending';
        $this->reason = $data['reason'] ?? null;
        $this->createdAt = $data['created_at'] ?? '';
    }

    /**
     * Create a new vacation request record.
     *
     * @param array $data Associative array with keys:
     *                    - user_id (int): ID of the user requesting vacation
     *                    - start_date (string): Start date (YYYY-MM-DD)
     *                    - end_date (string): End date (YYYY-MM-DD)
     *                    - reason (string|null): Optional reason for vacation
     * @return Vacation The newly created Vacation instance.
     */
    public static function create(array $data): Vacation
    {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("
            INSERT INTO vacations (user_id, start_date, end_date, reason)
            VALUES (:user_id, :start_date, :end_date, :reason)
        ");

        $stmt->execute([
            'user_id' => $data['user_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'reason' => $data['reason']
        ]);

        $id = $pdo->lastInsertId();
        return self::find($id);
    }

    /**
     * Find all vacations for a given user ID.
     *
     * @param int $userId The ID of the user.
     * @return Vacation[] Array of Vacation instances.
     */
    public static function findByUserId(int $userId): array
    {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("SELECT * FROM vacations WHERE user_id = ? ORDER BY start_date DESC");
        $stmt->execute([$userId]);

        $vacations = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vacations[] = new self($data);
        }

        return $vacations;
    }

    /**
     * Find all vacations by status.
     *
     * @param string $status The status to filter by ('pending', 'approved', 'declined').
     * @return Vacation[] Array of Vacation instances.
     */
    public static function findByStatus(string $status): array
    {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("SELECT * FROM vacations WHERE status = ? ORDER BY created_at DESC");
        $stmt->execute([$status]);

        $vacations = [];
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $vacations[] = new self($data);
        }

        return $vacations;
    }

    /**
     * Approve this vacation request.
     *
     * Updates the status to 'approved'.
     */
    public function approve(): void
    {
        $this->updateStatus('approved');
    }

    /**
     * Decline this vacation request.
     *
     * Updates the status to 'declined'.
     */
    public function decline(): void
    {
        $this->updateStatus('declined');
    }

    /**
     * Update the status of this vacation request.
     *
     * @param string $status New status value.
     */
    private function updateStatus(string $status): void
    {
        $pdo = DB::connect();
        $stmt = $pdo->prepare("UPDATE vacations SET status = ? WHERE id = ?");
        $stmt->execute([$status, $this->id]);
        $this->status = $status;
    }

    /**
     * Get the User who requested this vacation.
     *
     * @return User|null Returns the User instance or null if not found.
     */
    public function user(): ?User
    {
        return User::find($this->userId);
    }
}
