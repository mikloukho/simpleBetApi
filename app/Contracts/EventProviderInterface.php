<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
interface EventProviderInterface
{
    public function fetchEvents(): Collection;
}
