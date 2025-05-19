<?php

namespace App\Services;

use App\DTO\EventDto;
use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventService
{
    public function __construct(
        private readonly TeamService $teamService
    ) {}

    public function storeOrUpdate(EventDto $dto): void
    {
        $slugs = collect($dto->odds)
            ->pluck('team')
            ->filter()
            ->map(fn($name) => Str::slug($name))
            ->values()
            ->all();

        if (empty($slugs)) {
            Log::warning('No teams provided for event', ['sport' => $dto->sport]);
            return;
        }

        $event = Event::findBySportStartAndTeams($dto->sport, $dto->startAt, $slugs);

        if ($event) {
            $this->updateEvent($event, $dto);
        } else {
            $this->createEvent($dto, $slugs);
        }
    }

    protected function updateEvent(Event $event, EventDto $dto): void
    {
        if ($event->status !== $dto->status) {
            $event->update(['status' => $dto->status]);
        }

        $event->odds()->delete();

        foreach ($dto->odds as $oddDto) {
            $event->odds()->create([
                'type' => $oddDto->type,
                'team' => $oddDto->team,
                'value' => $oddDto->value,
            ]);
        }
    }

    protected function createEvent(EventDto $dto, array $slugs): void
    {
        $teams = $this->teamService->getOrCreateBySlugsFromOdds($slugs, $dto->odds);

        if ($teams->isEmpty()) {
            Log::warning('No teams found or created for event', ['sport' => $dto->sport]);
            return;
        }

        $event = Event::create([
            'sport' => $dto->sport,
            'start_at' => $dto->startAt,
            'status' => $dto->status,
            'teams' => $teams->pluck('name')->toArray(),
        ]);

        $event->teams()->sync($teams->pluck('id')->toArray());

        foreach ($dto->odds as $oddDto) {
            $event->odds()->create([
                'type' => $oddDto->type,
                'team' => $oddDto->team,
                'value' => $oddDto->value,
            ]);
        }
    }
}
