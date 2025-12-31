<?php

use Illuminate\Support\Facades\Schema;

it('can migrate up and down when telescope connection is null', function () {
    config()->set('telescope.storage.database.connection', null);

    $schema = Schema::connection(config('telescope.storage.database.connection'));

    $schema->dropIfExists('telescope_entries_tags');
    $schema->dropIfExists('telescope_entries');
    $schema->dropIfExists('telescope_monitoring');

    $migration = require base_path('database/migrations/2025_12_04_093502_create_telescope_entries_table.php');

    expect($schema->hasTable('telescope_entries'))->toBeFalse();
    expect($schema->hasTable('telescope_entries_tags'))->toBeFalse();
    expect($schema->hasTable('telescope_monitoring'))->toBeFalse();

    $migration->up();

    expect($schema->hasTable('telescope_entries'))->toBeTrue();
    expect($schema->hasTable('telescope_entries_tags'))->toBeTrue();
    expect($schema->hasTable('telescope_monitoring'))->toBeTrue();

    expect($schema->hasColumns('telescope_entries', [
        'sequence',
        'uuid',
        'batch_id',
        'family_hash',
        'should_display_on_index',
        'type',
        'content',
        'created_at',
    ]))->toBeTrue();

    expect($schema->hasColumns('telescope_entries_tags', [
        'entry_uuid',
        'tag',
    ]))->toBeTrue();

    expect($schema->hasColumns('telescope_monitoring', [
        'tag',
    ]))->toBeTrue();

    $migration->down();

    expect($schema->hasTable('telescope_entries_tags'))->toBeFalse();
    expect($schema->hasTable('telescope_entries'))->toBeFalse();
    expect($schema->hasTable('telescope_monitoring'))->toBeFalse();
});

it('can migrate up and down when telescope connection is a string', function () {
    $defaultConnection = config('database.default');

    config()->set('telescope.storage.database.connection', $defaultConnection);

    $schema = Schema::connection(config('telescope.storage.database.connection'));

    $schema->dropIfExists('telescope_entries_tags');
    $schema->dropIfExists('telescope_entries');
    $schema->dropIfExists('telescope_monitoring');

    $migration = require base_path('database/migrations/2025_12_04_093502_create_telescope_entries_table.php');

    expect($schema->hasTable('telescope_entries'))->toBeFalse();
    expect($schema->hasTable('telescope_entries_tags'))->toBeFalse();
    expect($schema->hasTable('telescope_monitoring'))->toBeFalse();

    $migration->up();

    expect($schema->hasTable('telescope_entries'))->toBeTrue();
    expect($schema->hasTable('telescope_entries_tags'))->toBeTrue();
    expect($schema->hasTable('telescope_monitoring'))->toBeTrue();

    expect($schema->hasColumns('telescope_entries', [
        'sequence',
        'uuid',
        'batch_id',
        'family_hash',
        'should_display_on_index',
        'type',
        'content',
        'created_at',
    ]))->toBeTrue();

    expect($schema->hasColumns('telescope_entries_tags', [
        'entry_uuid',
        'tag',
    ]))->toBeTrue();

    expect($schema->hasColumns('telescope_monitoring', [
        'tag',
    ]))->toBeTrue();

    $migration->down();

    expect($schema->hasTable('telescope_entries_tags'))->toBeFalse();
    expect($schema->hasTable('telescope_entries'))->toBeFalse();
    expect($schema->hasTable('telescope_monitoring'))->toBeFalse();
});
