<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE forstwirt_logs ADD COLUMN pause_time TIME NOT NULL DEFAULT '00:00:00' AFTER end");
            DB::statement('UPDATE forstwirt_logs SET pause_time = SEC_TO_TIME(COALESCE(pause, 0) * 60)');
            DB::statement('ALTER TABLE forstwirt_logs DROP COLUMN pause');
            DB::statement("ALTER TABLE forstwirt_logs CHANGE pause_time pause TIME NOT NULL DEFAULT '00:00:00'");

            DB::statement("ALTER TABLE harvester_logs ADD COLUMN pause_time TIME NOT NULL DEFAULT '00:00:00' AFTER end");
            DB::statement('UPDATE harvester_logs SET pause_time = SEC_TO_TIME(COALESCE(pause, 0) * 60)');
            DB::statement('ALTER TABLE harvester_logs DROP COLUMN pause');
            DB::statement("ALTER TABLE harvester_logs CHANGE pause_time pause TIME NOT NULL DEFAULT '00:00:00'");

            DB::statement("ALTER TABLE rueckezug_logs ADD COLUMN pause_time TIME NOT NULL DEFAULT '00:00:00' AFTER end");
            DB::statement('UPDATE rueckezug_logs SET pause_time = SEC_TO_TIME(COALESCE(pause, 0) * 60)');
            DB::statement('ALTER TABLE rueckezug_logs DROP COLUMN pause');
            DB::statement("ALTER TABLE rueckezug_logs CHANGE pause_time pause TIME NOT NULL DEFAULT '00:00:00'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE forstwirt_logs ADD COLUMN pause_minutes INT UNSIGNED NULL AFTER end');
            DB::statement('UPDATE forstwirt_logs SET pause_minutes = TIME_TO_SEC(pause) / 60');
            DB::statement('ALTER TABLE forstwirt_logs DROP COLUMN pause');
            DB::statement('ALTER TABLE forstwirt_logs CHANGE pause_minutes pause INT UNSIGNED NULL');

            DB::statement('ALTER TABLE harvester_logs ADD COLUMN pause_minutes INT UNSIGNED NULL AFTER end');
            DB::statement('UPDATE harvester_logs SET pause_minutes = TIME_TO_SEC(pause) / 60');
            DB::statement('ALTER TABLE harvester_logs DROP COLUMN pause');
            DB::statement('ALTER TABLE harvester_logs CHANGE pause_minutes pause INT UNSIGNED NULL');

            DB::statement('ALTER TABLE rueckezug_logs ADD COLUMN pause_minutes INT UNSIGNED NULL AFTER end');
            DB::statement('UPDATE rueckezug_logs SET pause_minutes = TIME_TO_SEC(pause) / 60');
            DB::statement('ALTER TABLE rueckezug_logs DROP COLUMN pause');
            DB::statement('ALTER TABLE rueckezug_logs CHANGE pause_minutes pause INT UNSIGNED NULL');
        }
    }
};