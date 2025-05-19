<?php

namespace Database\Seeders;

use App\Models\Sport;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = [
            ['name' => 'Football', 'slug' => 'football'],
            ['name' => 'Basketball', 'slug' => 'basketball'],
            ['name' => 'Tennis', 'slug' => 'tennis'],
            ['name' => 'Hockey', 'slug' => 'hockey'],
            ['name' => 'Volleyball', 'slug' => 'volleyball'],
        ];

        foreach ($sports as $sport) {
            Sport::create([
                'name' => $sport['name'],
                'slug' => $sport['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $faker = Faker::create();
        for ($i = 0; $i < 5; $i++) {
            $name = $faker->unique()->word . ' Sport';
            Sport::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
