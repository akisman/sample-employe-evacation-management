<?php
namespace App\Traits;

use App\Models\Vacation;
use Symfony\Component\HttpFoundation\JsonResponse;

trait HandlesVacations
{
    /**
     * Handle a manager action on a Vacation.
     *
     * @param array           $vars    Route vars (expects ['id' => …]).
     * @param callable        $action  Callback that mutates the Vacation.
     * @return JsonResponse
     */
    public function withVacation(array $vars, callable $action): JsonResponse
    {
        // Check the route parameter
        if (empty($vars['id']) || !ctype_digit((string) $vars['id'])) {
            return new JsonResponse(['message' => 'Invalid or missing id'], 400);
        }
        $id = (int) $vars['id'];

        // Authenticate & authorise
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }
        if (!$user->isManager()) {
            return new JsonResponse(['message' => 'Forbidden – manager access required'], 403);
        }

        // Fetch record
        $vacation = Vacation::find($id);
        if (!$vacation) {
            return new JsonResponse(['message' => 'Vacation not found'], 404);
        }

        // Run the specific action
        $action($vacation);

        return new JsonResponse($vacation);
    }
}
