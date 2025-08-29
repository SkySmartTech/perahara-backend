<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['service_type' => 'perahara_item', 'description' => 'Items used in Perahera'],
            ['service_type' => 'perahara_service', 'description' => 'General event services'],
            ['service_type' => 'elephant_service', 'description' => 'Elephant hire & related services'],
        ];

        foreach ($types as $t) {
            ServiceType::firstOrCreate(['service_type' => $t['service_type']], $t);
        }
    }
}
