<?php

namespace Database\Seeders;

use App\Models\Team;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Spartak', 'slug' => 'spartak', 'description' => 'Московский футбольный клуб'],
            ['name' => 'CSKA', 'slug' => 'cska', 'description' => 'Московский футбольный клуб'],
            ['name' => 'Crvena Zvezda', 'slug' => 'сrvena-zvezda', 'description' => 'Белградский футбольный клуб'],
            ['name' => 'Partizan', 'slug' => 'partizan', 'description' => 'Белградский футбольный клуб'],
            ['name' => 'Zenit', 'slug' => 'zenit', 'description' => 'Петербургский футбольный клуб'],
        ];

        foreach ($teams as $team) {
            Team::create([
                'name' => $team['name'],
                'slug' => $team['slug'],
                'description' => $team['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $faker = Faker::create();
        for ($i = 0; $i < 10; $i++) {
            $name = $faker->unique()->company;
            Team::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
