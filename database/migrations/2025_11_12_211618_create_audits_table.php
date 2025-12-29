<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $configuredConnection = config('audit.drivers.database.connection');
        $defaultConnection = config('database.default');
        $connection = $configuredConnection ?? $defaultConnection;

        $table = config('audit.drivers.database.table', 'audits');
        $morphPrefix = config('audit.user.morph_prefix', 'user');

        $buildTable = function (Blueprint $table) use ($morphPrefix): void {
            $table->bigIncrements('id');
            $table->string("{$morphPrefix}_type")->nullable();
            $table->unsignedBigInteger("{$morphPrefix}_id")->nullable();
            $table->string('event');
            $table->morphs('auditable');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();

            $table->index(["{$morphPrefix}_id", "{$morphPrefix}_type"]);
        };

        if (is_string($connection) && $connection !== '') {
            Schema::connection($connection)->create($table, $buildTable);

            return;
        }

        Schema::create($table, $buildTable);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $configuredConnection = config('audit.drivers.database.connection');
        $defaultConnection = config('database.default');
        $connection = $configuredConnection ?? $defaultConnection;

        $table = config('audit.drivers.database.table', 'audits');

        if (is_string($connection) && $connection !== '') {
            Schema::connection($connection)->dropIfExists($table);

            return;
        }

        Schema::dropIfExists($table);
    }
};
