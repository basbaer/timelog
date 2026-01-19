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
        // Rename columns
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'user_id');
            $table->renameColumn('name', 'username');
        });

        
        Schema::table('users', function (Blueprint $table) {
            // Modify existing columns
            $table->dropColumn('user_id');
            $table->smallIncrements('user_id')->first();
            $table->string('username', 30)->unique()->change();
            $table->string('password')->after('username')->change();
            // Make email nullable
            $table->string('email')->nullable()->change();

            // Add the columns your application actually uses
            $table->string('first_name', 30)->after('password');
            $table->string('last_name', 30)->after('first_name')->nullable();
            $table->unsignedTinyInteger('role_id')->after('last_name');
            $table->string('phone',20)->nullable()->after('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // reverse modifications columns
            $table->dropColumn('user_id');
            $table->bigIncrements('id')->primary()->before('username')->change();
            $table->string('username')->change();
            // Make email nullable
            $table->string('email')->unique()->change();
            $table->string('password')->after('email_verified_at')->change();

            // Add the columns your application actually uses
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('role_id');
            $table->dropColumn('phone');
        });
        
        // Rename columns
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('user_id', 'id');
            $table->renameColumn('username', 'name');
        });
    }
};
