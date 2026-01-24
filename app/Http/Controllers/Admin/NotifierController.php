<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NotificationRequest;
use App\Services\Admin\NotifierService;
use Auth;
use Exception;
use Inertia\Inertia;
use Log;

class NotifierController extends Controller
{
    public function __construct(protected NotifierService $notifierService) {}

    public function create()
    {
        /** @var array<int, array{value: string, label: string}> $roles */
        $roles = collect(RoleEnum::cases())
            ->map(fn (RoleEnum $role): array => [
                'value' => $role->value,
                'label' => $role->label(),
            ])
            ->values()
            ->all();

        return Inertia::render('administrative/notifier/create', [
            'roleOptions' => $roles,
        ]);
    }

    public function notify(NotificationRequest $request)
    {
        Log::info('Notifier: Sending notification', ['action_user_id' => Auth::id(), 'data' => $request->validated()]);

        try {

            $this->notifierService->sendNotification($request);

            return to_route('administrative.notifier.create')->with('success', 'Notification sent successfully.');
        } catch (Exception $e) {
            Log::error('Notifier: Failed to log notification attempt', ['action_user_id' => Auth::id(), 'error' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'An error occurred while attempting to send the notification. Please try again.');
        }
    }
}
