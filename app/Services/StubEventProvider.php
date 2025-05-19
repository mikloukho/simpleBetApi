<?php
namespace App\Services;

namespace App\Services;

use App\Contracts\EventProviderInterface;
use App\DTO\EventDto;
use App\DTO\OddDto;
use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Sport;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Faker\Factory as Faker;

class StubEventProvider implements EventProviderInterface
{
    public function fetchEvents(): Collection
    {
        $faker = Faker::create();
        // 4 случайных существующих события
        $existing = Event::whereIn('status', ['upcoming', 'live'])->inRandomOrder()->limit(4)->get()->map(function ($event) use ($faker) {
            $teamA = $faker->company;
            $teamB = $faker->company;
            $newStatus = $event->status == EventStatus::Upcoming ? 'live' : collect(['live', 'completed'])->random();
            return new EventDto(
                sport: $event->sport->name,
                startAt: $event->start_at,
                status: $newStatus,
                odds: [
                    new OddDto('win', $faker->randomFloat(2, 1.2, 3.0), $teamA),
                    new OddDto('win', $faker->randomFloat(2, 1.4, 3.5), $teamB),
                    new OddDto('draw', $faker->randomFloat(2, 2.0, 4.0)),
                ]
            );
        });
        $sports = Sport::all();
        // 4 новых события через Faker
        $new = collect(range(1, 4))->map(function () use ($faker, $sports) {
            $teamA = $faker->company;
            $teamB = $faker->company;

            return new EventDto(
                sport: $sports->random()->slug,
                startAt: Carbon::now()->addHours(random_int(1, 72)),
                status: 'upcoming',
                odds: [
                    new OddDto('win', $faker->randomFloat(2, 1.2, 2.5), $teamA),
                    new OddDto('win', $faker->randomFloat(2, 1.8, 3.2), $teamB),
                    new OddDto('draw', $faker->randomFloat(2, 2.5, 4.5)),
                ]
            );
        });
        return $existing->merge($new);
    }
}


