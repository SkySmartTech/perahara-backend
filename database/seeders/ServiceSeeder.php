<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Carbon\Carbon;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'user_id' => 1,
                'service_type_id' => 1,
                'name' => 'Catering Service',
                'short_description' => 'Delicious food for your events',
                'description' => 'Provides food and beverage services for events.',
                'location' => 'Colombo, Sri Lanka',
                'phone' => '0771234567',
                'price' => 50000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 2,
                'name' => 'Photography Service',
                'short_description' => 'Capture your special moments',
                'description' => 'Professional photography services for events.',
                'location' => 'Kandy, Sri Lanka',
                'phone' => '0772345678',
                'price' => 75000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 3,
                'name' => 'Decoration Service',
                'short_description' => 'Beautiful decorations for your events',
                'description' => 'Event decoration services to make your event memorable.',
                'location' => 'Galle, Sri Lanka',
                'phone' => '0773456789',
                'price' => 60000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 1,
                'name' => 'Music Band Service',
                'short_description' => 'Live music for your events',
                'description' => 'Provides live music performances for events.',
                'location' => 'Negombo, Sri Lanka',
                'phone' => '0774567890',
                'price' => 80000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 2,
                'name' => 'Transport Service',
                'short_description' => 'Reliable transport for your events',
                'description' => 'Transportation services for event attendees.',
                'location' => 'Jaffna, Sri Lanka',
                'phone' => '0775678901',
                'price' => 40000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 3,
                'name' => 'Event Planning Service',
                'short_description' => 'Professional event planning',
                'description' => 'Comprehensive event planning and management services.',
                'location' => 'Matara, Sri Lanka',
                'phone' => '0776789012',
                'price' => 100000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 1,
                'name' => 'Security Service',
                'short_description' => 'Ensure safety at your events',
                'description' => 'Professional security services for events.',
                'location' => 'Anuradhapura, Sri Lanka',
                'phone' => '0777890123',
                'price' => 55000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 2,
                'name' => 'Lighting Service',
                'short_description' => 'Perfect lighting for your events',
                'description' => 'Event lighting services to enhance the ambiance.',
                'location' => 'Trincomalee, Sri Lanka',
                'phone' => '0778901234',
                'price' => 45000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 3,
                'name' => 'Audio-Visual Service',
                'short_description' => 'Top-notch AV equipment for your events',
                'description' => 'Provides audio-visual equipment and support for events.',
                'location' => 'Batticaloa, Sri Lanka',
                'phone' => '0779012345',
                'price' => 70000,
                'status' => 'active',
            ],
            [
                'user_id' => 1,
                'service_type_id' => 1,
                'name' => 'Cleaning Service',
                'short_description' => 'Keep your event venue clean',
                'description' => 'Professional cleaning services for event venues.',
                'location' => 'Puttalam, Sri Lanka',
                'phone' => '0770123456',
                'price' => 30000,
                'status' => 'active',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}