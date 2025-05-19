<?php
namespace App\DTO;

use Carbon\Carbon;
use Illuminate\Support\Str;

final readonly class EventDto
{
    public function __construct(
        public string $sport,
        public Carbon $startAt,
        public string $status,
        /** @var OddDto[] */
        public array $odds,
    ) {}

    public function teamSlugs(): array
    {
        return collect($this->odds)
            ->pluck('team')
            ->filter()
            ->map(fn($name) => Str::slug($name))
            ->values()
            ->all();
    }
    public function hasNoTeams(): bool
    {
        return empty($this->teamSlugs());
    }
}
