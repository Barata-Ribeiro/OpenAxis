<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Controller;
use OwenIt\Auditing\Contracts\Auditable;

arch()
    ->expect(value: 'App\Console\Commands')
    ->toBeClasses()
    ->toUseStrictTypes()
    ->toExtend(class: Command::class)
    ->toHaveMethod(method: 'handle');

arch()
    ->expect(value: 'App\ValueObjects')
    ->classes()
    ->toBeFinal()
    ->toHaveMethod('__construct')
    ->not->toHaveMethod('set*');

arch()
    ->expect(value: 'App\Models')
    ->toBeClasses()
    ->toExtend(class: Model::class)
    ->toImplement(interfaces: Auditable::class);

arch(description: 'app')
    ->expect(value: 'App\Enums')
    ->toBeEnums();

arch()
    ->expect(value: 'App\Http\Controllers')
    ->toBeClasses()
    ->toUseStrictTypes()
    ->toBeFinal()
    ->classes()
    ->toOnlyBeUsedIn([
        'App\Http\Controllers',
        'routes',
    ])
    ->toExtend(class: Controller::class)
    ->toHaveSuffix('Controller');

arch()
    ->expect(value: 'App\Jobs')
    ->toBeClasses()
    ->toImplement(interfaces: ShouldQueue::class)
    ->toHaveMethod(method: 'handle');

arch()
    ->expect(value: 'Tests\*')
    ->toHaveSuffix(suffix: 'Test');
