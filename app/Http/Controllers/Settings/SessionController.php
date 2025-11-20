<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\Settings\SessionService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class SessionController extends Controller
{
    public function __construct(protected SessionService $sessionService) {}

    public function index()
    {
        $sessions = $this->sessionService->getActiveSessionsForRequestingUser();

        return Inertia::render('settings/sessions', [
            'sessions' => $sessions,
        ]);
    }

    public function destroy(string $session_id)
    {
        try {
            $this->sessionService->destroySessionById($session_id);

            return to_route('sessions.index')->with('success', 'Session terminated successfully.');
        } catch (Exception $e) {
            Log::error('Failed to terminate session.', ['action_user_id' => Auth::id(), 'session_id' => $session_id, 'error' => $e->getMessage()]);

            return to_route('sessions.index')->with('error', 'Failed to terminate session.');
        }
    }
}
