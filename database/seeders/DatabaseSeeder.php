<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed service types
        $this->call([
            ServiceTypesSeeder::class,
            AdminSeeder::class,
            // PeraheraSeeder::class,
            // ServiceSeeder::class,
        ]);
    }
}
