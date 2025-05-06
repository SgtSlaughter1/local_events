<?php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ticketTypes = [
            [
                'name' => 'Regular',
                'description' => 'Standard access to the event with basic amenities.',
            ],
            [
                'name' => 'VIP',
                'description' => 'Premium access with additional benefits and exclusive areas.',
            ],
            [
                'name' => 'VVIP',
                'description' => 'Ultimate access with all premium benefits, exclusive services, and special treatment.',
            ],
        ];

        foreach ($ticketTypes as $type) {
            TicketType::create([
                'name' => $type['name'],
                'description' => $type['description'],
                'is_active' => true,
            ]);
        }
    }
}
