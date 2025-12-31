<?php

use Illuminate\Support\Facades\Schema;

it('can migrate up and down when audit connection is null', function () {
    config()->set('audit.drivers.database.connection', null);
    config()->set('audit.drivers.database.table', 'audits_test_null_connection');
    config()->set('audit.user.morph_prefix', 'user');

    $migration = require base_path('database/migrations/2025_11_12_211618_create_audits_table.php');

    expect(Schema::hasTable('audits_test_null_connection'))->toBeFalse();

    $migration->up();

    expect(Schema::hasTable('audits_test_null_connection'))->toBeTrue();

    $migration->down();

    expect(Schema::hasTable('audits_test_null_connection'))->toBeFalse();
});

it('can migrate up and down when audit connection is a string', function () {
    $defaultConnection = config('database.default');

    config()->set('audit.drivers.database.connection', $defaultConnection);
    config()->set('audit.drivers.database.table', 'audits_test_string_connection');
    config()->set('audit.user.morph_prefix', 'user');

    $migration = require base_path('database/migrations/2025_11_12_211618_create_audits_table.php');

    expect(Schema::hasTable('audits_test_string_connection'))->toBeFalse();

    $migration->up();

    expect(Schema::hasTable('audits_test_string_connection'))->toBeTrue();

    $migration->down();

    expect(Schema::hasTable('audits_test_string_connection'))->toBeFalse();
});
