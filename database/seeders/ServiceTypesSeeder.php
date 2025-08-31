<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'perahara_item', 'description' => 'Items used in Perahera'],
            ['name' => 'perahara_service', 'description' => 'General event services'],
            ['name' => 'elephant_service', 'description' => 'Elephant hire & related services'],
        ];

        foreach ($types as $t) {
            ServiceType::firstOrCreate(['name' => $t['name']], $t);
        }
    }
}
