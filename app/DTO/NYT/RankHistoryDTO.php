<?php

namespace App\DTO\NYT;

use DateTimeImmutable;

final readonly class RankHistoryDTO
{
    public function __construct(
        public string             $primaryISBN10,
        public string             $primaryISBN13,
        public string             $rank,
        public string             $listName,
        public string             $displayName,
        public DateTimeImmutable $publishedDate,
        public DateTimeImmutable $bestsellersDate,
        public int                $weeksOnList,
        public int                $rankLastWeek,
        public int                $asterisk,
        public int                $dagger,
    )
    {
    }
}
