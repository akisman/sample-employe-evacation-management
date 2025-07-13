<?php

namespace App\Controllers;

use App\Models\User;
use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;

/**
 * Base controller class providing common authentication helper methods.
 *
 * All controllers extending this class can use the authentication
 * and authorization helpers to enforce user access control.
 */
abstract class Controller
{
    /**
     * Get the currently authenticated user from the session.
     *
     * Starts the session if not already started, checks for a user_id
     * in the session, and returns the corresponding User model.
     *
     * @return User|null Returns User object if authenticated, null otherwise.
     */
    protected function getAuthenticatedUser(): ?User
    {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return User::find($_SESSION['user_id']);
    }

    /**
     * Require an authenticated user.
     *
     * Returns the authenticated User, or throws an UnauthorizedException
     * if no user is authenticated.
     *
     * @throws UnauthorizedException If no user is authenticated.
     * @return User The authenticated User instance.
     */
    protected function requireAuth(): User
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            throw new UnauthorizedException('Unauthorized');
        }

        return $user;
    }

    /**
     * Require an authenticated user with manager privileges.
     *
     * Returns the authenticated User if they have manager role,
     * otherwise throws ForbiddenException.
     *
     * @throws UnauthorizedException If no user is authenticated.
     * @throws ForbiddenException If authenticated user is not a manager.
     * @return User The authenticated User instance with manager role.
     */
    protected function requireManager(): User
    {
        $user = $this->requireAuth();

        if (!$user->isManager()) {
            throw new ForbiddenException('Forbidden – manager access required');
        }

        return $user;
    }
}
