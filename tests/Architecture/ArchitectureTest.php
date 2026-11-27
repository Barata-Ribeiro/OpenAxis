<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Controller;
use OwenIt\Auditing\Contracts\Auditable;
use SanderMuller\FluentValidation\HasFluentRules;

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
    ->toImplement(interfaces: Auditable::class)
    ->toUseTrait(trait: OwenIt\Auditing\Auditable::class);

arch(description: 'app')
    ->expect(value: 'App\Enums')
    ->toBeEnums();

arch()
    ->expect(value: 'App\Http\Controllers')
    ->toBeClasses()
    ->toUseStrictTypes()
    ->toBeFinal()
    ->toOnlyBeUsedIn([
        'App\Http\Controllers',
        'routes',
    ])
    ->toExtend(class: Controller::class)
    ->toHaveSuffix('Controller')
    ->not->toHavePublicMethodsBesides([
        '__invoke', 'index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'archive', 'confirmDestroy', 'restore',
    ]);

arch()
    ->expect(value: 'App\Http\Requests')
    ->toBeClasses()
    ->toUseStrictTypes()
    ->toBeFinal()
    ->toExtend(class: FormRequest::class)
    ->toUseTrait(trait: HasFluentRules::class)
    ->toHaveSuffix(suffix: 'Request');

arch()
    ->expect(value: 'App\Jobs')
    ->toBeClasses()
    ->toImplement(interfaces: ShouldQueue::class)
    ->toHaveMethod(method: 'handle');

arch()
    ->expect(value: 'Tests\*')
    ->toHaveSuffix(suffix: 'Test');
