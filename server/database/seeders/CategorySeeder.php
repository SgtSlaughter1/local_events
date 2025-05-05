<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Entertainment',
                'image' => null,
                'description' => 'Music, movies, parties, and more.',
                'is_active' => true,
            ],
            [
                'name' => 'Culture & Arts',
                'image' => null,
                'description' => 'Art exhibitions, theater, and cultural events.',
                'is_active' => true,
            ],
            [
                'name' => 'Sports & Fitness',
                'image' => null,
                'description' => 'Sports, fitness, and outdoor activities.',
                'is_active' => true,
            ],
            [
                'name' => 'Educational & Business',
                'image' => null,
                'description' => 'Workshops, seminars, and business events.',
                'is_active' => true,
            ],
            [
                'name' => 'Technology',
                'image' => null,
                'description' => 'Tech talks, hackathons, and IT events.',
                'is_active' => true,
            ],
            [
                'name' => 'Travel & Adventure',
                'image' => null,
                'description' => 'Travel, adventure, and exploration.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                ...$category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
