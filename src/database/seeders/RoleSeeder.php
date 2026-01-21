<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Insert admin, harvester, rückezug and forstwirt roles into the roles table.
     */
    public function run(): void
    {
        //
        $roles = ['admin', 'harvester', 'rückezug', 'forstwirt'];

        foreach ($roles as $role) {
            DB::table('roles')->upsert(['name' => $role], 'name');
        }

    }
}
