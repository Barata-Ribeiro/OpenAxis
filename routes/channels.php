<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', fn (User $user, int $id) => (int) $user->id === (int) $id);

Broadcast::channel('online', fn (User $user) => ['id' => (int) $user->id, 'name' => $user->name, 'avatar' => $user->avatar, 'roles' => $user->getRoleNames()]);
