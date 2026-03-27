<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AskAssistantRequest;
use App\Services\FinancialAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    public function __construct(
        private FinancialAssistantService $assistantService,
    ) {}

    /**
     * POST /api/assistant/ask
     */
    public function ask(AskAssistantRequest $request): JsonResponse
    {
        $result = $this->assistantService->ask(
            Auth::user(),
            $request->validated('question'),
        );

        return response()->json(['data' => $result]);
    }
}
