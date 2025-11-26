<?php

namespace App\Interfaces\Common;

interface DashboardServiceInterface
{
    public function getAdminDashboardData($yearMonth): mixed;

    public function getUserDashboardData(): mixed;
}
