<?php

namespace App\Service;

use App\DTO\NYT\BestSellersDTO;
use App\DTO\NYT\BooksDTO;
use App\DTO\NYT\IsbnDTO;
use App\DTO\NYT\RankHistoryDTO;
use App\DTO\NYT\ReviewsDTO;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NytClientService
{
    private const BEST_SELLERS_HISTORY_ENDPOINT = '/lists/best-sellers/history.json';

    public function __construct(
        private readonly string $baseUri,
        public readonly string  $apiKey,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function getBestSellersHistory(BestSellersDTO $bestSellersDTO): Collection
    {
        $endpoint = $this->baseUri . self::BEST_SELLERS_HISTORY_ENDPOINT;

        $params = $bestSellersDTO->toArray();
        $params['api-key'] = $this->apiKey;
        $cacheKey = 'api_response_' . md5(json_encode($params));

        try {
            $response = Http::get($endpoint, $params);

            if(Cache::store('redis')->has($cacheKey))
            {
                return $this->convertToDTO(Cache::store('redis')->get($cacheKey));
            }

            if ($response->successful()) {
                return $this->convertToDTO($response->json());
            }

            throw new Exception('Invalid response from API: ' . $response->getStatusCode());
        } catch (Exception $e) {
            throw new Exception('Error fetching data from API: ' . $e->getMessage());
        }
    }

    public function convertToDTO(array $data): Collection
    {
        $books = collect($data['results'])->map(function ($bookData) {
            $isbns = collect($bookData['isbns'])->map(function ($isbnData) {
                return new IsbnDTO(
                    isbn10: $isbnData['isbn10'],
                    isbn13: $isbnData['isbn13'],
                );
            });

            $ranksHistory = collect($bookData['ranks_history'])->map(function ($rankData) {
                return new RankHistoryDTO(
                    primaryISBN10: $rankData['primary_isbn10'],
                    primaryISBN13: $rankData['primary_isbn13'],
                    rank: $rankData['rank'],
                    listName: $rankData['list_name'],
                    displayName: $rankData['display_name'],
                    publishedDate: DateTimeImmutable::createFromFormat(
                        'Y-m-d',
                        $rankData['published_date']
                    ),
                    bestsellersDate: DateTimeImmutable::createFromFormat(
                        'Y-m-d',
                        $rankData['bestsellers_date']
                    ),
                    weeksOnList: $rankData['weeks_on_list'],
                    rankLastWeek: $rankData['rank_last_week'],
                    asterisk: $rankData['asterisk'],
                    dagger: $rankData['dagger'],
                );
            });

            $reviews = collect($bookData['reviews'])->map(function ($reviewData) {
                return new ReviewsDTO(
                    link: $reviewData['book_review_link'],
                    firstChapterLink: $reviewData['first_chapter_link'],
                    sundayReviewLink: $reviewData['sunday_review_link'],
                    articleChapterLink: $reviewData['article_chapter_link']);
            });

            return new BooksDTO(
                $bookData['title'],
                $bookData['description'],
                $bookData['contributor'],
                $bookData['author'],
                $bookData['contributor_note'],
                $bookData['price'],
                $bookData['age_group'],
                $bookData['publisher'],
                collect($isbns),
                collect($ranksHistory),
                collect($reviews),
            );
        });

        return $books;
    }
}
