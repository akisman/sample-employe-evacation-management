<?php
namespace App\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;
use App\Models\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for managing user CRUD operations.
 *
 * Requires manager authentication for all actions.
 */
class UserController extends Controller
{

    /**
     * List all users.
     *
     * Requires manager authentication.
     *
     * @return JsonResponse JSON array of all users or error message.
     */
    public function index(): JsonResponse
    {
        try {
            $this->requireManager();
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        }
        $users =  User::all();
        return new JsonResponse($users);
    }

    /**
     * Show a single user by ID.
     *
     * Requires manager authentication.
     *
     * @param Request $_ Ignored request parameter.
     * @param array $vars Route variables, expects ['id'].
     * @return JsonResponse JSON user data or error message.
     */
    public function show(Request $_, array $vars): JsonResponse
    {
        try {
            $this->requireManager();
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        }
        $user = User::find($vars['id']);
        return new JsonResponse($user);
    }

    /**
     * Create a new user.
     *
     * Requires manager authentication.
     * Expects JSON with name, email, password, role, employee_code.
     *
     * @param Request $request Incoming HTTP request.
     * @return JsonResponse Created user data or error message.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->requireManager();
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        }
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['email'], $data['password'], $data['role'])) {
            return new JsonResponse(['message' => 'Missing fields'], Response::HTTP_BAD_REQUEST);
        }

        // Validate employee_code is 7-digit int
        if (!is_int($data['employee_code']) && !ctype_digit($data['employee_code'])) {
            return new JsonResponse(['message' => 'Employee code must be a 7-digit integer'], Response::HTTP_BAD_REQUEST);
        }
        $empCode = $data['employee_code'];
        if ($empCode < 1000000 || $empCode > 9999999) {
            return new JsonResponse(['message' => 'Employee code must be a 7-digit integer'], Response::HTTP_BAD_REQUEST);
        }

        if (User::findByEmployeeCode($data['employee_code'])) {
            return new JsonResponse(['message' => 'Employee code must be unique'], Response::HTTP_BAD_REQUEST);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'role' => $data['role'],
            'employee_code' => $data['employee_code']
        ]);

        return new JsonResponse($user, Response::HTTP_CREATED);
    }

    /**
     * Update an existing user by ID.
     *
     * Requires manager authentication.
     * Supports partial updates.
     *
     * @param Request $request Incoming HTTP request.
     * @param array $vars Route variables, expects ['id'].
     * @return JsonResponse Updated user data or error message.
     */
    public function update(Request $request, array $vars): JsonResponse
    {
        try {
            $this->requireManager();
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        }
        $user = User::find($vars['id']);
        $data = json_decode($request->getContent(), true);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['employee_code'])) {
            // Validate employee_code is 7-digit int
            if (!is_int($data['employee_code']) && !ctype_digit($data['employee_code'])) {
                return new JsonResponse(['message' => 'Employee code must be a 7-digit integer'], Response::HTTP_BAD_REQUEST);
            }
            $empCode = $data['employee_code'];
            if ($empCode < 1000000 || $empCode > 9999999) {
                return new JsonResponse(['message' => 'Employee code must be a 7-digit integer'], Response::HTTP_BAD_REQUEST);
            }

            $existing = User::findByEmployeeCode($empCode);
            if ($existing && $existing->id !== $user->id) {
                return new JsonResponse(['message' => 'Employee code must be unique'], Response::HTTP_BAD_REQUEST);
            }
            $user->employee_code = $empCode;
        }

        $user->name = $data['name'] ?? $user->name;
        $user->employee_code = $data['employee_code'] ?? $user->employee_code;
        $user->email = $data['email'] ?? $user->email;
        $user->role = $data['role'] ?? $user->role;

        $hashedPassword = '';
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $user->save([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'role' => $data['role'],
            'employee_code' => $data['employee_code']
        ]);

        return new JsonResponse($user);
    }

    /**
     * Delete a user by ID.
     *
     * Requires manager authentication.
     *
     * @param Request $request Incoming HTTP request.
     * @param array $vars Route variables, expects ['id'].
     * @return JsonResponse Success or error message.
     */
    public function destroy(Request $request, array $vars): JsonResponse
    {
        try {
            $this->requireManager();
        } catch (ForbiddenException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 401);
        } catch (UnauthorizedException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 403);
        }
        $user = User::find($vars['id']);

        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->delete();

        return new JsonResponse(['message' => 'User deleted']);
    }

}
