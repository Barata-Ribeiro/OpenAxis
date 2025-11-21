<?php

use App\Mail\NewUserMail;
use App\Models\User;
use Illuminate\Support\Str;

test('mailable content', function () {
    $password = Str::password();
    $user = User::factory()->create(['password' => $password]);

    $mailable = new NewUserMail($user->name, $user->email, $password);

    $mailable->assertFrom(config('mail.from.address'), config('mail.from.name'));
    $mailable->assertTo($user->email);
    $mailable->assertHasSubject(config('app.name').' - Your new account');

    $mailable->assertSeeInHtml($user->name);
    $mailable->assertSeeInHtml($user->email);
    $mailable->assertSeeInHtml($password);
});
