<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (string) $user->id === (string) $id;
});

Broadcast::channel('erp.inventory', fn (User $user): bool => $user->hasVerifiedEmail() && $user->can('product.index'));
Broadcast::channel('erp.movements', fn (User $user): bool => $user->hasVerifiedEmail() && $user->can('stock-movement.index'));
Broadcast::channel('erp.cash-flow', fn (User $user): bool => $user->hasVerifiedEmail() && $user->canAny(['cash-entry.index', 'financial-account.index']));
