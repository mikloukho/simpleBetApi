<?php

namespace App\Console\Commands;

use App\Contracts\EventProviderInterface;
use Illuminate\Console\Command;
use App\Actions\StoreEventAction;

class FetchEventsCommand extends Command
{
    protected $signature = 'events:fetch';
    protected $description = 'Fetch events from external API';

    public function handle(
        EventProviderInterface $provider,
        StoreEventAction $store
    ): void
    {
        foreach ($provider->fetchEvents() as $eventDto) {
            $store($eventDto);
        }

        $this->info("Events fetched and stored.");
    }
}

