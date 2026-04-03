<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AskAssistantRequest;
use App\Services\FinancialAssistantService;
use App\Services\UsageTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    public function __construct(
        private FinancialAssistantService $assistantService,
        private UsageTrackingService $usageTrackingService,
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

        $this->usageTrackingService->record(Auth::user(), 'ai_assistant');

        return response()->json(['data' => $result]);
    }
}
