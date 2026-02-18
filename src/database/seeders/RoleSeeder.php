<?php

namespace Database\Seeders;


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
        $slugs = ['admin', 'harvester', 'rueckezug', 'forstwirt'];
        $roles = ['Admin', 'Harvester', 'Rückezug', 'Forstwirt'];

        foreach ($roles as $index => $role) {
            DB::table('roles')->upsert(
                [
                    'id' => $index,
                    'slug' => $slugs[$index],
                    'name' => $role,
                ],
                ['slug'], // Unique constraint for upsert
                ['name']  // Fields to update if the record already exists
            );
        }

    }
}
