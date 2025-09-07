<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perahera;
use Carbon\Carbon;

class PeraheraSeeder extends Seeder
{
    public function run()
    {
        Perahera::create([
            'name' => 'Esala Perahera',
            'description' => 'The most famous perahera in Sri Lanka',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(10),
            'location' => 'Kandy, Sri Lanka',
            'image' => 'esala-perahera.jpg',
            'status' => 'active',
            'user_id' => 1 // Make sure this user exists
        ]);

        Perahera::create([
            'name' => 'Vesak Perahera',
            'description' => 'Buddhist festival perahera',
            'start_date' => Carbon::today()->addDays(15),
            'end_date' => Carbon::today()->addDays(16),
            'location' => 'Colombo, Sri Lanka',
            'image' => 'vesak-perahera.jpg',
            'status' => 'active',
            'user_id' => 1
        ]);

        Perahera::create([
            'name' => 'Esala Perahera',
            'description' => 'The most famous perahera in Sri Lanka',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(10),
            'location' => 'Kandy, Sri Lanka',
            'image' => 'esala-perahera.jpg',
            'status' => 'active',
            'user_id' => 1 // Make sure this user exists
        ]);

        Perahera::create([
            'name' => 'Vesak Perahera',
            'description' => 'Buddhist festival perahera',
            'start_date' => Carbon::today()->addDays(15),
            'end_date' => Carbon::today()->addDays(16),
            'location' => 'Colombo, Sri Lanka',
            'image' => 'vesak-perahera.jpg',
            'status' => 'active',
            'user_id' => 1
        ]);

        Perahera::create([
            'name' => 'Esala Perahera',
            'description' => 'The most famous perahera in Sri Lanka',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(10),
            'location' => 'Kandy, Sri Lanka',
            'image' => 'esala-perahera.jpg',
            'status' => 'active',
            'user_id' => 1 // Make sure this user exists
        ]);

        Perahera::create([
            'name' => 'Vesak Perahera',
            'description' => 'Buddhist festival perahera',
            'start_date' => Carbon::today()->addDays(15),
            'end_date' => Carbon::today()->addDays(16),
            'location' => 'Colombo, Sri Lanka',
            'image' => 'vesak-perahera.jpg',
            'status' => 'active',
            'user_id' => 1
        ]);

        Perahera::create([
            'name' => 'Esala Perahera',
            'description' => 'The most famous perahera in Sri Lanka',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(10),
            'location' => 'Kandy, Sri Lanka',
            'image' => 'esala-perahera.jpg',
            'status' => 'active',
            'user_id' => 1 // Make sure this user exists
        ]);

        Perahera::create([
            'name' => 'Vesak Perahera',
            'description' => 'Buddhist festival perahera',
            'start_date' => Carbon::today()->addDays(15),
            'end_date' => Carbon::today()->addDays(16),
            'location' => 'Colombo, Sri Lanka',
            'image' => 'vesak-perahera.jpg',
            'status' => 'active',
            'user_id' => 1
        ]);

        Perahera::create([
            'name' => 'Esala Perahera',
            'description' => 'The most famous perahera in Sri Lanka',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(10),
            'location' => 'Kandy, Sri Lanka',
            'image' => 'esala-perahera.jpg',
            'status' => 'active',
            'user_id' => 1 // Make sure this user exists
        ]);

        Perahera::create([
            'name' => 'Vesak Perahera',
            'description' => 'Buddhist festival perahera',
            'start_date' => Carbon::today()->addDays(15),
            'end_date' => Carbon::today()->addDays(16),
            'location' => 'Colombo, Sri Lanka',
            'image' => 'vesak-perahera.jpg',
            'status' => 'active',
            'user_id' => 1
        ]);
    }
}