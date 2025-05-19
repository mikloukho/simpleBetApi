<?php

namespace App\Actions;

use App\DTO\EventDto;
use App\Events\EventUpdated;
use App\Models\Event;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreEventAction
{
    public function __invoke(EventDto $eventDto): void
    {
        $event = DB::transaction(function () use ($eventDto) {
            $slugs = $eventDto->teamSlugs();
            if (empty($slugs)) {
                Log::warning('No teams provided for event', ['sport' => $eventDto->sport]);
                return null;
            }

            $event = $this->findExistingEvent($eventDto, $slugs, $eventDto->sport);
            if (!$event) {
                $teams = $this->resolveTeams($slugs, $eventDto->odds);
                $sport = $this->resolveSport($eventDto->sport);

                $event = $this->createNewEvent($eventDto, $teams, $sport);
            }

            if ($event->status !== $eventDto->status) {
                $event->update(['status' => $eventDto->status]);
            }

            $this->syncEventOdds($event, $eventDto->odds);

            return $event;
        });

        if ($event) {
            event(new EventUpdated($event->id));
        }

    }

    private function findExistingEvent(EventDto $eventDto, array $slugs, string $sport): ?Event
    {
        return Event::where('start_at', $eventDto->startAt)
            ->whereHas('sport', fn ($q) => $q->where('slug', Str::slug($sport)))
            ->whereHas('teams', function ($q) use ($slugs) {
                $q->whereIn('slug', $slugs);
            }, '=', count($slugs))
            ->first();
    }

    private function createNewEvent(EventDto $eventDto, Collection $teams, Sport $sport): ?Event
    {
        $event = Event::create([
            'sport_id' => $sport->id,
            'start_at' => $eventDto->startAt,
            'status' => $eventDto->status,
        ]);
        $event->teams()->sync($teams->pluck('id')->toArray());

        return $event;
    }

    private function resolveTeams(array $slugs, array $odds): Collection
    {
        $teams = Team::whereIn('slug', $slugs)->get();
        $existingSlugs = $teams->pluck('slug')->toArray();
        $missingSlugs = array_diff($slugs, $existingSlugs);

        foreach ($missingSlugs as $slug) {
            $teamName = collect($odds)
                ->firstWhere('team', fn($team) => Str::slug($team) === $slug)['team'] ?? $slug;

            $team = Team::create([
                'name' => $teamName,
                'slug' => $slug,
            ]);

            $teams->push($team);
        }

        return $teams;
    }

    private function resolveSport(string $sport): Sport
    {
        $sport = Sport::firstWhere('slug', Str::slug($sport));
        if(!$sport) {
            $sport = Sport::create([
                'name' => $sport,
                'slug' => Str::slug($sport),
            ]);
        }
        return $sport;
    }

    private function syncEventOdds(Event $event, array $odds): void
    {
        $event->odds()->delete();
        $teams = $event->teams()->pluck('team_id', 'slug')->toArray();
        foreach ($odds as $oddDto) {
            $event->odds()->create([
                'type' => $oddDto->type,
                'value' => $oddDto->value,
                'team_id' => $teams[Str::slug($oddDto->team)] ?? null,
            ]);
        }
    }
}
