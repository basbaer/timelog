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
        // Add activation_code column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('activation_code', 20)->nullable()->after('remember_token');
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove activation_code column from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activation_code');
            $table->string('password')->nullable(false)->change();
        });
    }
};
