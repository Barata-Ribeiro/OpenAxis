<?php

namespace App\Services\Admin;

use App\Http\Requests\Admin\NotificationRequest;
use App\Interfaces\Admin\NotifierServiceInterface;
use App\Models\User;
use App\Notifications\WrittenNotification;
use Auth;
use Illuminate\Support\Facades\Notification;

class NotifierService implements NotifierServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function sendNotification(NotificationRequest $request): void
    {
        $validated = $request->validated();
        $sendingUser = Auth::user();

        $message = $validated['message'];
        $toUser = ! isset($validated['email']) ? null : User::whereEmail($validated['email'])->first();
        $roles = ! isset($validated['roles']) ? [] : $validated['roles'];

        if ($toUser) {
            $toUser->notifyNow(new WrittenNotification($message, $sendingUser));

            return;
        }

        $query = User::query()
            ->when(! empty($roles), fn ($query) => $query->whereHas('roles', fn ($q) => $q->whereIn('name', $roles)));

        $query->chunkById(100, function ($users) use ($message, $sendingUser) {
            Notification::send($users, new WrittenNotification($message, $sendingUser));
        });
    }
}
