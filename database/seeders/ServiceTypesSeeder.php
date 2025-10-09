<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Perahara Item', 'description' => 'Items used in Perahera'],
            ['name' => 'Perahara Service', 'description' => 'General event services'],
            ['name' => 'Elephant Service', 'description' => 'Elephant hire & related services'],
        ];

        foreach ($types as $t) {
            ServiceType::firstOrCreate(['name' => $t['name']], $t);
        }
    }
}
