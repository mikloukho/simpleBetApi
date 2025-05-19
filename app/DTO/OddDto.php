<?php
namespace App\DTO;

final readonly class OddDto
{
    public function __construct(
        public string $type,
        public float $value,
        public ?string $team = null
    ) {}
}
