<?php

namespace App\Http\Requests;

use App\DTO\NYT\BestSellersDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BestSellersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author' => ['nullable', 'string', 'max:100'],
            'isbn'   => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    // Validate comma-separated ISBNs
                    $isbnList = explode(',', $value);
                    foreach ($isbnList as $isbn) {
                        $isbn = trim($isbn);
                        if (!preg_match('/^(?:\d{10}|\d{13})$/', $isbn)) {
                            $fail("The $attribute field contains an invalid ISBN: $isbn");
                        }
                    }
                },
            ],
            'title'  => ['nullable', 'string', 'max:255'],
            'offset' => ['nullable', 'integer', 'min:0', 'multiple_of:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'author.string' => 'The author field must be a string.',
            'title.string'  => 'The title field must be a string.',
            'offset.string' => 'The offset field must be a string.',
            'isbn.string'   => 'The isbn field must be a string.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 400));
    }
    public function getData(): BestSellersDTO
    {
        return new BestSellersDTO(
            $this->validated('author'),
            $this->validated('isbn'),
            $this->validated('title'),
            $this->validated('offset')
        );
    }
}
