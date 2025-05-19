<?php

namespace App\Enums;

enum EventStatus: string
{
    case Upcoming = 'upcoming';
    case Live = 'live';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
