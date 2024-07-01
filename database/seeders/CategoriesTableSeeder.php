<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // make categories data dummy
        $faker = FakerFactory::create();

        // Generate 20 dummy categories
        for ($i = 0; $i < 20; $i++) {
            Category::create([
                'name' => $faker->word,
            ]);
        }
    }
}
