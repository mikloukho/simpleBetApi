<?php

namespace Database\Seeders;

use App\Enums\OddType;
use App\Models\Event;
use App\Models\Odd;
use App\Models\Result;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ResultAndOddSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $events = Event::with('teams')->get();
        foreach ($events as $event) {
            [$firstTeam, $secondTeam] = $event->teams;
            Odd::create([
                'event_id' => $event->id,
                'team_id' => $firstTeam->id,
                'type' => OddType::Win,
                'value' => $faker->randomFloat(2, 1.1, 3.0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Odd::create([
                'event_id' => $event->id,
                'team_id' => $secondTeam->id,
                'type' => OddType::Win,
                'value' => $faker->randomFloat(2, 1.1, 3.0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Odd::create([
                'event_id' => $event->id,
                'team_id' => null,
                'type' => OddType::Draw,
                'value' => $faker->randomFloat(2, 2.5, 4.0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $winner = $faker->randomElement([$firstTeam, $secondTeam, null]);
            $homeScore = $faker->numberBetween(0, 5);
            $awayScore = $winner ? ($winner === $firstTeam ? $faker->numberBetween(0, $homeScore - 1) : $faker->numberBetween($homeScore + 1, 5)) : $homeScore;

            Result::create([
                'event_id' => $event->id,
                'team_id' => $winner?->id,
                'score' => [$firstTeam->name => $homeScore, $secondTeam->name => $awayScore],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
