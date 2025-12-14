<?php

namespace App\Http\Controllers\Common;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Services\Common\DashboardService;
use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function show(FormRequest $request)
    {
        $validated = $request->validate([
            'yearMonth' => ['sometimes', 'date_format:Y-m'],
        ]);

        $yearMonth = $validated['yearMonth'] ?? null;

        $userRole = Auth::user()->getRoleNames()->first();
        $data = [];

        switch ($userRole) {
            case RoleEnum::SUPER_ADMIN->value:
                $data = $this->dashboardService->getAdminDashboardData($yearMonth);

                return Inertia::render('dashboards/admin-dashboard', [
                    'data' => Inertia::defer(fn () => $data, 'dashboard'),
                ]);
            case RoleEnum::BUYER->value:
            case RoleEnum::VENDOR->value:
            case RoleEnum::FINANCE->value:
            default:
                // $data = $this->dashboardService->getUserDashboardData(); TODO: Implement user dashboard data

                return Inertia::render('dashboards/dashboard', $data);
        }
    }
}
