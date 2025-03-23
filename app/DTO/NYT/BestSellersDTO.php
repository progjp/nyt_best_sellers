<?php

namespace App\DTO\NYT;

final readonly class BestSellersDTO
{
    public function __construct(
        private ?string $author = null,
        private ?string $isbn = null,
        private ?string $title = null,
        private ?string $offset = null,
    )
    {
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), fn($value) => !is_null($value));
    }
}
