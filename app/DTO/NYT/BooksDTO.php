<?php

namespace App\DTO\NYT;

use Illuminate\Support\Collection;

final readonly class BooksDTO
{
    public function __construct(
        public string     $title,
        public ?string    $description = null,
        public ?string    $contributor = null,
        public string     $author,
        public ?string    $contributor_note = null,
        public float      $price,
        public ?string    $age_group = null,
        public ?string    $publisher = null,
        public Collection $isbns,
        public Collection $ranks_history,
        public Collection $reviews,
    )
    {
    }
}
