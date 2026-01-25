<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Log;

class NotificationController extends Controller
{
    public function index(): Response
    {
        $notifications = Auth::user()->notifications()->latest()->cursorPaginate(10, ['*'], 'notification_cursor')->withQueryString();

        return Inertia::render('settings/notifications', [
            'notifications' => Inertia::scroll(fn () => $notifications),
        ]);
    }

    public function toggleRead(string $id): RedirectResponse
    {
        try {
            $user = Auth::user();

            $notification = $user->notifications()->where('id', $id)->firstOrFail();

            if (isset($notification->read_at)) {
                $notification->markAsUnread();
            } else {
                $notification->markAsRead();
            }

            return back()->with('success', 'Notification status updated successfully.');
        } catch (Exception $e) {
            Log::error('Notification: Failed to toggle read status', ['action_user_id' => Auth::id(), 'error' => $e->getMessage()]);

            return back()->with('error', 'Failed to update notification status. Please try again.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $user = Auth::user();

            $notification = $user->notifications()->where('id', $id)->firstOrFail();

            $notification->delete();

            return back()->with('success', 'Notification deleted successfully.');
        } catch (Exception $e) {
            Log::error('Notification: Failed to delete notification', ['action_user_id' => Auth::id(), 'error' => $e->getMessage()]);

            return back()->with('error', 'Failed to delete notification. Please try again.');
        }
    }
}
