<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(10)->withQueryString();

        return Inertia::render('settings/notifications', [
            'notifications' => $notifications,
        ]);
    }
}
