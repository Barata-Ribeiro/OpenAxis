<?php

use Illuminate\Support\Facades\Schema;

it('can migrate up and down for the media table', function () {
    Schema::dropIfExists('media');

    expect(Schema::hasTable('media'))->toBeFalse();

    $migration = require base_path('database/migrations/2025_11_22_102311_create_media_table.php');

    $migration->up();

    expect(Schema::hasTable('media'))->toBeTrue();

    expect(Schema::hasColumns('media', [
        'id',
        'model_type',
        'model_id',
        'uuid',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    $migration->down();

    expect(Schema::hasTable('media'))->toBeFalse();
});
