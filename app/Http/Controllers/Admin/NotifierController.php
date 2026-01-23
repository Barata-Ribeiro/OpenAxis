<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class NotifierController extends Controller
{
    public function create()
    {
        /** @var array<int, array{value: string, label: string}> $roles */
        $roles = collect(RoleEnum::cases())
            ->map(fn(RoleEnum $role): array => [
                'value' => $role->value,
                'label' => $role->label(),
            ])
            ->values()
            ->all();

        return Inertia::render('administrative/notifier/create', [
            'roles' => $roles,
        ]);
    }
}
