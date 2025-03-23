<?php

namespace App\DTO\NYT;

final readonly class ReviewsDTO
{
    public function __construct(
        public ?string     $link,
        public ?string    $firstChapterLink = null,
        public ?string    $sundayReviewLink = null,
        public ?string     $articleChapterLink = null,
    )
    {
    }
}
