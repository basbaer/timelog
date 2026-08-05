<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ForstwirtWorkingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slugs = ['motorsage', 'freischneider', 'seilmaschine', 'messkluppe', 'reparatur', 'other'];
        $types = ['Motorsäge', 'Freischneider', 'Seilmaschine', 'Messkluppe', 'Reparatur', 'Sonstiges'];

        foreach ($types as $index => $type) {
            DB::table('forstwirt_working_types')->upsert(
                [
                    'id' => $index+1, // Ensure IDs start from 1
                    'slug' => $slugs[$index],
                    'name' => $type,
                ],
                ['slug'], // Unique constraint for upsert
                ['name']  // Fields to update if the record already exists
            );
        }
    }
}
