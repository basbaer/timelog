<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('user_id');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->primary(['project_id', 'user_id']);
        });

        $projectUsers = DB::table('project_role')
            ->join('users', 'users.role_id', '=', 'project_role.role_id')
            ->select('project_role.project_id', 'users.id as user_id')
            ->get()
            ->map(fn ($row) => [
                'project_id' => $row->project_id,
                'user_id' => $row->user_id,
            ])
            ->all();

        if (! empty($projectUsers)) {
            DB::table('project_user')->insertOrIgnore($projectUsers);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
