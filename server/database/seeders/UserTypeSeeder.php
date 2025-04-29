<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = [
            [
                'name' => 'Admin',
                'description' => 'Administrator with full system access'
            ],
            [
                'name' => 'Organizer',
                'description' => 'Event organizer with event management capabilities'
            ],
            [
                'name' => 'Attendee',
                'description' => 'Regular user who can attend events'
            ]

        ];

        foreach ($userTypes as $userType) {
            UserType::create($userType);
        }
    }
}
