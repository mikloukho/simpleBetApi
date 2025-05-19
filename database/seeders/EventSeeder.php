<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Support\Arr;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $sports = Sport::all()->pluck('id')->toArray();
        $teams = Team::all()->pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {
            $start_at = $faker->dateTimeBetween('-1 year', '-1 day');

            $event = Event::create([
                'sport_id' => $faker->randomElement($sports),
                'start_at' => $start_at,
                'status' => Arr::random([EventStatus::Completed, EventStatus::Cancelled, EventStatus::Upcoming]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $teamIds = $faker->randomElements($teams, 2);
            $event->teams()->attach($teamIds);
        }
    }
}
