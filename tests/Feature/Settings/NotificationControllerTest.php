<?php

use App\Models\User;
use App\Notifications\WrittenNotification;
use Inertia\Testing\AssertableInertia;

test('notifications index page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('profile.notifications'));

    $response->assertOk();
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('settings/notifications'));
});

test('user can toggle notification read status', function () {
    $user = User::factory()->create();
    $sender = User::factory()->create();

    $user->notify(new WrittenNotification('Test notification', $sender));

    $notification = $user->notifications()->firstOrFail();

    expect($notification->read_at)->toBeNull();

    $this
        ->actingAs($user)
        ->patch(route('profile.notifications.toggle-read', $notification->id))
        ->assertRedirect();

    $notification->refresh();

    expect($notification->read_at)->not->toBeNull();

    $this
        ->actingAs($user)
        ->patch(route('profile.notifications.toggle-read', $notification->id))
        ->assertRedirect();

    $notification->refresh();

    expect($notification->read_at)->toBeNull();
});

test('user can delete a notification', function () {
    $user = User::factory()->create();
    $sender = User::factory()->create();

    $user->notify(new WrittenNotification('Delete me', $sender));

    $notification = $user->notifications()->firstOrFail();

    $this
        ->actingAs($user)
        ->delete(route('profile.notifications.destroy', $notification->id))
        ->assertRedirect();

    $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
});
