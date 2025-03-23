<?php

namespace App\Http\Controllers;

use App\Http\Requests\BestSellersRequest;
use App\Service\NytClientService;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class BestSellersNYTController extends Controller
{

    public function __construct(private readonly NytClientService $nytClientService)
    {
    }

    public function index(BestSellersRequest $request): JsonResponse
    {
        try {
            $response = $this->nytClientService->getBestSellersHistory($request->getData());
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), 400);
        }

        return new JsonResponse($response, 200);
    }
}
