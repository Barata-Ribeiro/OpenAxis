<?php

use App\Enums\RoleEnum;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

describe('tests for the Admin\NotifierController methods', function () {
    test('the create method returns the correct Inertia response with role options', function () {
        $this->actingAs(getSuperAdmin());

        $response = $this->get(route('administrative.notifier.create'));

        $response->assertInertia(fn (AssertableInertia $page) => $page->component('administrative/notifier/create')
            ->has('roleOptions', count(RoleEnum::cases()))
            ->where('roleOptions.0', [
                'value' => RoleEnum::SUPER_ADMIN->value,
                'label' => RoleEnum::SUPER_ADMIN->label(),
            ])
        );
    });

    test('the notify method sends notification using email input', function () {
        $this->actingAs(getSuperAdmin());

        $postData = [
            'message' => 'Test notification message',
            'email' => User::select('email')->inRandomOrder()->firstOrFail()->email,
        ];

        $this->post(route('administrative.notifier.notify'), $postData)
            ->assertRedirect(route('administrative.notifier.create'))
            ->assertSessionHas('success', 'Notification sent successfully.');
    });

    test('the notify method sends notification using roles input', function () {
        $this->actingAs(getSuperAdmin());

        $postData = [
            'message' => 'Test notification message',
            'roles' => [RoleEnum::FINANCE->value, RoleEnum::BUYER->value],
        ];

        $this->post(route('administrative.notifier.notify'), $postData)
            ->assertRedirect(route('administrative.notifier.create'))
            ->assertSessionHas('success', 'Notification sent successfully.');
    });

    test('the notify method fails validation with invalid data', function () {
        $this->actingAs(getSuperAdmin());

        $postData = [
            'message' => '', // Missing message
            'email' => 'invalid-email-format',
        ];

        $this->post(route('administrative.notifier.notify'), $postData)
            ->assertSessionHasErrors(['message', 'email']);
    });

    test('the notify method fails authorization for non-super-admin users', function () {
        $this->actingAs(getUserWithRole(RoleEnum::BUYER->value));

        $postData = [
            'message' => 'Test notification message',
            'email' => User::select('email')->inRandomOrder()->firstOrFail()->email,
        ];

        $this->post(route('administrative.notifier.notify'), $postData)
            ->assertStatus(403);
    });

    test('the notify method fails if both email and roles are both present', function () {
        $this->actingAs(getSuperAdmin());

        $postData = [
            'message' => 'Test notification message',
            'email' => User::select('email')->inRandomOrder()->firstOrFail()->email,
            'roles' => [RoleEnum::FINANCE->value],
        ];

        $this->post(route('administrative.notifier.notify'), $postData)
            ->assertSessionHasErrors(['email', 'roles']);
    });
});
