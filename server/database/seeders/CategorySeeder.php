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
                'slug' => Str::slug('Entertainment'),
                'image' => 'entertainment.png',
                'description' => 'Events focused on entertainment, including concerts, shows, and performances.',
                'is_active' => true,
            ],
            [
                'name' => 'Culture & Arts',
                'slug' => Str::slug('Culture & Arts'),
                'image' => 'culture.png',
                'description' => 'Cultural events, art exhibitions, and artistic performances.',
                'is_active' => true,
            ],
            [
                'name' => 'Sports & Fitness',
                'slug' => Str::slug('Sports & Fitness'),
                'image' => 'sports.png',
                'description' => 'Sports events, fitness classes, and athletic competitions.',
                'is_active' => true,
            ],
            [
                'name' => 'Educational & Business',
                'slug' => Str::slug('Educational & Business'),
                'image' => 'education.png',
                'description' => 'Educational workshops, business conferences, and professional development events.',
                'is_active' => true,
            ],
            [
                'name' => 'Technology',
                'slug' => Str::slug('Technology'),
                'image' => 'tech.png',
                'description' => 'Tech conferences, hackathons, and technology-related events.',
                'is_active' => true,
            ],
            [
                'name' => 'Travel & Adventure',
                'slug' => Str::slug('Travel & Adventure'),
                'image' => 'travel.png',
                'description' => 'Travel-related events, adventure activities, and exploration experiences.',
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