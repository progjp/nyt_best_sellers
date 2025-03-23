<?php

namespace App\DTO\NYT;

final readonly class IsbnDTO
{
    public function __construct(
        public ?string     $isbn10,
        public ?string     $isbn13,
    )
    {
    }
}
