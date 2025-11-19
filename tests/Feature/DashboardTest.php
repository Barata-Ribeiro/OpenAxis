<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs(User::where('email', config('app.admin_email'))->first());

    $this->get(route('dashboard'))->assertOk();
});
