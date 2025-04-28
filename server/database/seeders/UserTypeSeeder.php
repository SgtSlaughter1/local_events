<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $userTypes = [
            [
                'name' => 'Admin',
                'description' => 'System administrator with full access to all features'
            ],
            [
                'name' => 'Organizer',
                'description' => 'Event organizer who can create and manage events'
            ],
            [
                'name' => 'Attendee',
                'description' => 'Regular user who can register for events'
            ]
        ];

        foreach ($userTypes as $userType) {
            UserType::create($userType);
        }
    }
}
