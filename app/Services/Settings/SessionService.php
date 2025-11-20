<?php

namespace App\Services\Settings;

use App\Common\Helpers;
use App\Interfaces\Settings\SessionServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SessionService implements SessionServiceInterface
{
    public function getActiveSessionsForRequestingUser(): Collection
    {
        $rawSessions = DB::table(config('session.table'))
            ->distinct()
            ->select('id', 'user_id', 'ip_address', 'user_agent', 'last_activity')
            ->where('user_id', Auth::id())
            ->whereNotNull('user_id')
            ->orderByDesc('last_activity')
            ->get();

        return $rawSessions->map(function ($session) {
            $session->is_current_device = $session->id === session()->getId();
            $session->last_activity_label = Carbon::createFromTimestamp($session->last_activity)->diffForHumans();
            $session->last_activity = Carbon::createFromTimestamp($session->last_activity)->toFormattedDateString();
            $session->user_agent = Helpers::formatUserAgent($session->user_agent);

            return $session;
        });
    }

    public function destroySessionById(string $session_id): void
    {
        DB::table('sessions')
            ->where('id', $session_id)
            ->where('user_id', Auth::id())
            ->delete();
    }
}
