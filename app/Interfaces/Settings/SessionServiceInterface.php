<?php

namespace App\Interfaces\Settings;

use Illuminate\Support\Collection;

interface SessionServiceInterface
{
    /**
     * Get all active sessions for the user who made the current request.
     *
     * Returns a collection of active session records owned by the requesting (authenticated) user.
     * "Active" means sessions that are not expired or explicitly invalidated. Each collection item
     * should expose relevant session details (for example: session id, ip_address, user_agent,
     * last_activity, expires_at and a flag indicating whether it represents the current session).
     *
     * @return \Illuminate\Support\Collection<int, array|object> Collection of active session records for the requesting user.
     *
     * @throws \Illuminate\Auth\AuthenticationException|\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException If the requester is not authenticated or not authorized.
     */
    public function getActiveSessionsForRequestingUser(): Collection;

    /**
     * Destroy a session by its unique identifier.
     *
     * Removes all stored data for the session with the given ID and ensures the session is invalidated.
     * Implementations should remove any persistent storage entries (e.g. files, database rows, cache)
     * and invalidate any related cookies or authentication tokens as appropriate.
     *
     * @param  string  $session_id  The unique identifier of the session to destroy.
     *
     * @throws \InvalidArgumentException If $session_id is empty or not a valid session identifier.
     * @throws \RuntimeException If the session could not be destroyed due to an underlying storage error.
     */
    public function destroySessionById(string $session_id): void;
}
