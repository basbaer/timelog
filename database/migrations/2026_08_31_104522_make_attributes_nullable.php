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
        //make start,end, and date from table rueckezug_logs nullable
        Schema::table('rueckezug_logs', function (Blueprint $table) {
            $table->time('start')->nullable()->change();
            $table->time('end')->nullable()->change();
            $table->date('date')->nullable()->change();
        });

        Schema::table('forstwirt_logs', function (Blueprint $table) {
            $table->time('start')->nullable()->change();
            $table->time('end')->nullable()->change();
            $table->date('date')->nullable()->change();
        });

        Schema::table('harvester_logs', function (Blueprint $table) {
            $table->time('start')->nullable()->change();
            $table->time('end')->nullable()->change();
            $table->date('date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rueckezug_logs', function (Blueprint $table) {
            $table->time('start')->nullable(false)->change();
            $table->time('end')->nullable(false)->change();
            $table->date('date')->nullable(false)->change();
        });

        Schema::table('forstwirt_logs', function (Blueprint $table) {
            $table->time('start')->nullable(false)->change();
            $table->time('end')->nullable(false)->change();
            $table->date('date')->nullable(false)->change();
        });

        Schema::table('harvester_logs', function (Blueprint $table) {
            $table->time('start')->nullable(false)->change();
            $table->time('end')->nullable(false)->change();
            $table->date('date')->nullable(false)->change();
        });
    }
};
