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
        Schema::create('harvester_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedSmallInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->date('date');
            $table->time('start');
            $table->time('end');
            $table->unsignedInteger('pause')->nullable();
            $table->time('sum')->nullable();
            $table->float('bs_from', 10)->nullable();
            $table->float('bs_to', 10)->nullable();
            $table->float('bs_diff', 10)->nullable();
            $table->float('fm_amount', 10)->nullable();
            $table->float('fm_total', 10)->nullable();
            $table->float('fm_day', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvester_logs');
    }
};
