<?php

namespace App\Controllers;

use App\Models\Vacation;
use App\Traits\HandlesVacations;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller handling vacation-related HTTP requests.
 *
 * Provides endpoints to list user vacations, list all vacations (manager only),
 * create vacation requests, and approve or decline vacations.
 */
class VacationController extends Controller
{
    use HandlesVacations;

    /**
     * List vacations for the authenticated user.
     *
     * @return JsonResponse JSON array of the authenticated user's vacations
     *                      or 401 Unauthorized error if not authenticated.
     */
    public function index(): JsonResponse
    {
        try {
            $user = $this->requireAuth();
            return new JsonResponse($user->vacations());
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * List all vacations in the system (manager access required).
     *
     * @return JsonResponse JSON array of all vacations, or 401/403 error responses
     *                      if not authenticated or lacking manager privileges.
     */
    public function all(): JsonResponse
    {
        try {
            $this->requireManager();
            return new JsonResponse(Vacation::all());
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        }
    }

    /**
     * Create a new vacation request for the authenticated user.
     *
     * Expects JSON payload with `start_date`, `end_date`, and optional `reason`.
     * Returns the created Vacation object with 201 status, or validation errors.
     *
     * @param Request $req The HTTP request containing JSON payload.
     * @return JsonResponse JSON response with created Vacation or error message.
     */
    public function store(Request $req): JsonResponse
    {
        try {
            $user = $this->requireAuth();
            $data = json_decode($req->getContent(), true);

            $start_date = $data['start_date'] ?? null;
            $end_date = $data['end_date'] ?? null;
            $reason = $data['reason'] ?? '';

            if (!$start_date || !$end_date) {
                return new JsonResponse(['message' => 'Start and end dates required'], 422);
            }

            $vacation = Vacation::create([
                'user_id' => $user->id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reason' => $reason,
            ]);

            return new JsonResponse($vacation, 201);
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Approve a vacation request (manager access required).
     *
     * Uses the HandlesVacations trait method `withVacation` to fetch the vacation
     * by ID and perform the approve action.
     *
     * @param Request $_ The HTTP request (unused).
     * @param array $vars Route parameters, expected to contain vacation ID.
     * @return JsonResponse JSON response indicating success or failure.
     */
    public function approve(Request $_, array $vars): JsonResponse
    {
        try {
            $this->requireManager();
            return $this->withVacation($vars, fn ($v) => $v->approve());
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        }
    }

    /**
     * Decline a vacation request (manager access required).
     *
     * Uses the HandlesVacations trait method `withVacation` to fetch the vacation
     * by ID and perform the decline action.
     *
     * @param Request $_ The HTTP request (unused).
     * @param array $vars Route parameters, expected to contain vacation ID.
     * @return JsonResponse JSON response indicating success or failure.
     */
    public function decline(Request $_, array $vars): JsonResponse
    {
        try {
            $this->requireManager();
            return $this->withVacation($vars, fn ($v) => $v->decline());
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        }
    }
}
