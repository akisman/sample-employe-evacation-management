<?php
namespace App\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller handling authentication endpoints.
 */
class AuthController extends Controller
{
    /**
     * Log in a user with email and password.
     *
     * Starts a session and sets session variables on success.
     *
     * @param Request $req Incoming HTTP request with JSON {email, password}.
     * @return Response JSON response with success or error message.
     */
    public function login(Request $req): Response
    {
        $data = json_decode($req->getContent(), true);
        $email = strtolower($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $user = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], 422);
        }

        session_start();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['role'] = $user->role;

        return new JsonResponse([
            'message' => 'ok',
            'role' => $user->role,
            'name' => $user->name
        ]);
    }

    /**
     * Get the currently authenticated user's information.
     *
     * Uses base controller's getAuthenticatedUser() method.
     *
     * @param Request $req Incoming HTTP request.
     * @return Response JSON user data or 401 if unauthenticated.
     */
    public function me(Request $req): Response
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            return new JsonResponse(['message' => 'Unauthenticated'], 401);
        }

        return new JsonResponse($user);
    }

    /**
     * Log out the current user by destroying the session.
     *
     * @param Request $req Incoming HTTP request.
     * @return Response JSON success message.
     */
    public function logout(Request $req): Response
    {
        session_start();
        session_destroy();

        return new JsonResponse(['message' => 'Logged out']);
    }
}
