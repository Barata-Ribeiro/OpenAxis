<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(10)->withQueryString();

        return Inertia::render('settings/notifications', [
            'notifications' => $notifications,
        ]);
    }

    public function toggleRead($id)
    {
        try {
            $user = Auth::user();

            $notification = $user->notifications()->where('id', $id)->firstOrFail();

            if ($notification->pluck('read_at') !== null) {
                $notification->markAsUnread();
            } else {
                $notification->markAsRead();
            }

            return to_route('profile.notifications')->with('success', 'Notification status updated successfully.');
        } catch (Exception $e) {
            Log::error('Notification: Failed to toggle read status', ['action_user_id' => Auth::id(), 'error' => $e->getMessage()]);

            return back()->with('error', 'Failed to update notification status. Please try again.');
        }

    }
}
