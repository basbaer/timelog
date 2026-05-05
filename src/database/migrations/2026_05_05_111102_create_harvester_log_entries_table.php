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
        Schema::create('harvester_log_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('harvester_log_id');
            $table->foreign('harvester_log_id')->references('id')->on('harvester_logs')->onDelete('cascade');
            $table->unsignedBigInteger('working_type_id');
            $table->foreign('working_type_id')->references('id')->on('forstwirt_working_types')->onDelete('cascade');
            $table->time('hours');
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvester_log_entries');
    }
};
