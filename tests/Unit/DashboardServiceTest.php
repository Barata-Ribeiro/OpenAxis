<?php

use App\Services\Common\DashboardService;

it('bypasses concurrency when configured', function (): void {
    config(['app.bypass_concurrency.dashboard' => true]);

    $service = new DashboardService;

    $method = new ReflectionMethod(DashboardService::class, 'shouldBypassConcurrency');
    $method->setAccessible(true);

    expect($method->invoke($service))->toBeTrue();
});
