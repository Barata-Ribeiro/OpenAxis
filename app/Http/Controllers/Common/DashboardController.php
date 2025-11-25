<?php

namespace App\Http\Controllers\Common;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function show()
    {
        $userRole = Auth::user()->getRoleNames()->first();
        $data = [];

        switch ($userRole) {
            case RoleEnum::SUPER_ADMIN->value:
                return Inertia::render('dashboards/admin-dashboard', $data);
            case RoleEnum::BUYER->value:
            case RoleEnum::VENDOR->value:
            case RoleEnum::FINANCE->value:
            default:
                return Inertia::render('dashboards/dashboard', $data);
        }
    }
}
