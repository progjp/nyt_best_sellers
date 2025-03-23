<?php

namespace Tests\Unit;

use App\Http\Requests\BestSellersRequest;
use Faker\Factory;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BestSellersRequestTest extends TestCase
{
    private array $rules;

    public function setUp(): void
    {
        parent::setUp();

        $this->rules = (new BestSellersRequest())->rules();
    }

    public static function validationProvider(): array
    {
        $faker = Factory::create();

        return [
            'request_should_fail_when_no_title_is_provided'              => [
                'passed' => false,
                'data'   => [
                    'author' => str_repeat('a', 101),
                    'isbn'   => $faker->numberBetween(1000000000, 9000000000),
                    'title'  => $faker->title(),
                    'offset' => $faker->numberBetween(1, 50),
                ],
            ],
            'request_should_fail_when_no_price_is_provided'              => [
                'passed' => false,
                'data'   => [
                    'author' => str_repeat('a', 101),
                    'isbn'   => $faker->numberBetween(1000000000, 9000000000),
                    'title'  => $faker->title(),
                    'offset' => $faker->numberBetween(1, 50),
                ],
            ],
            'request_should_fail_when_title_has_more_than_50_characters' => [
                'passed' => false,
                'data'   => [
                    'title' => str_repeat('a', 300),
                ],
            ],
            'request_should_pass_1'                                      => [
                'passed' => true,
                'data'   => [
                    'author' => $faker->firstName() . ' ' . $faker->lastName(),
                    'isbn'   => $faker->numerify('##########'),
                    'title'  => $faker->title(),
                    'offset' => $faker->numberBetween(1, 50) * 20,
                ],
            ],
            'request_should_pass_2'                                      => [
                'passed' => true,
                'data'   => [
                    'author' => $faker->firstName() . ' ' . $faker->lastName(),
                    'isbn'   => $faker->numerify('#############'),
                    'title'  => $faker->title(),
                    'offset' => $faker->numberBetween(1, 50) * 20,
                ],
            ],
        ];
    }

    #[DataProvider('validationProvider')]
    public function test_validation($passed, $data)
    {
        $this->assertEquals(
            $passed,
            $this->validate($data)
        );
    }

    protected function validate($mockedRequestData): bool
    {
        return Validator::make($mockedRequestData, $this->rules)->passes();
    }
}
