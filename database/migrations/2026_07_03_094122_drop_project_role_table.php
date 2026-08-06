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
        Schema::dropIfExists('project_role');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('project_role', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('role_id');

            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            $table->primary(['project_id', 'role_id']);
        });
    }
};
