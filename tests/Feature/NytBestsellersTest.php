<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class NytBestsellersTest extends TestCase
{

    public function test_the_application_returns_a_successful_response(): void
    {
        $jsonResponse = file_get_contents(__DIR__ . '/../Resources/nyt-bestsellers-history-api.json');

        Http::fake(['*' => Http::response($jsonResponse, Response::HTTP_OK, ['Content-Type' => 'application/json'])]);

        $response = $this->get('/api/v1/nyt-bestsellers');
        $response->assertSuccessful();
        $content = $response->getContent();
        $this->assertJson($content);
        $data = json_decode($content);

        $this->assertObjectHasProperty('title', $data[0]);
        $this->assertSame('"I GIVE YOU MY BODY ..."', $data[0]->title);
        $this->assertObjectHasProperty('description', $data[0]);
        $this->assertSame('The author of the Outlander novels gives tips on writing sex scenes, drawing on examples from the books.', $data[0]->description);
        $this->assertObjectHasProperty('contributor', $data[0]);
        $this->assertObjectHasProperty('author', $data[0]);
        $this->assertObjectHasProperty('contributor_note', $data[0]);
        $this->assertObjectHasProperty('price', $data[0]);
        $this->assertSame(0, $data[0]->price);
        $this->assertObjectHasProperty('age_group', $data[0]);
        $this->assertObjectHasProperty('publisher', $data[0]);
        $this->assertObjectHasProperty('publisher', $data[0]);
        $this->assertObjectHasProperty('isbns', $data[0]);
        $this->assertIsArray($data[0]->isbns);
        $this->assertCount(1, $data[0]->isbns);
        $this->assertObjectHasProperty('isbn10', $data[0]->isbns[0]);
        $this->assertObjectHasProperty('isbn13', $data[0]->isbns[0]);
        $this->assertObjectHasProperty('ranks_history', $data[0]);
        $this->assertIsArray($data[0]->ranks_history);
        $this->assertEquals('0399178570', $data[0]->ranks_history[0]->primaryISBN10);
        $this->assertEquals('9780399178573', $data[0]->ranks_history[0]->primaryISBN13);
        $this->assertCount(20, $data);

        $response->assertStatus(200);
    }

    public function test_the_application_returns_a_failure_response(): void
    {
        $jsonResponse = file_get_contents(__DIR__ . '/../Resources/nyt-bestsellers-history-api.json');

        Http::fake(['*' => Http::response($jsonResponse, Response::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/json'])]);

        $response = $this->get('/api/v1/nyt-bestsellers');

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    #[DataProvider('dataForParams')]
    public function test_with_params(array $data, bool $result): void
    {
        $jsonResponse = file_get_contents(__DIR__ . '/../Resources/nyt-bestsellers-history-api.json');

        $params = [
            'isbn'   => $data['isbn'],
            'offset' => $data['offset'],
        ];
        if ($result) {
            Http::fake(['*' => Http::response($jsonResponse, Response::HTTP_OK, ['Content-Type' => 'application/json'])]);
        }

        $response = $this->get(url()->query('/api/v1/nyt-bestsellers', $params));
        $content = json_decode($response->getContent(), true);
        if ($result) {
            $response->assertStatus(Response::HTTP_OK);
        } else {
            $this->assertFalse($content['success']);
            $this->assertIsArray($content['errors']);
            $this->assertCount(2, $content['errors']['isbn']);
            $this->assertSame('The offset field must be a multiple of 20.', $content['errors']['offset'][0]);
            $response->assertStatus(Response::HTTP_BAD_REQUEST);
        }
    }

    public static function dataForParams(): array
    {
        return [
            [
                [
                    'isbn'   => 'fef,2323,1234567890,1234567890123',
                    'offset' => '50',
                ],
                false,
            ],
            [
                [
                    'isbn'   => '0871404427,9780871404428,1234567890,1234567890123',
                    'offset' => '20',
                ],
                true,
            ],
        ];
    }
}
