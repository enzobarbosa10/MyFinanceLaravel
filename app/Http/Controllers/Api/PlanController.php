<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ChangePlanRequest;
use App\Http\Requests\Api\SubscribePlanRequest;
use App\Http\Resources\Api\PlanResource;
use App\Http\Resources\Api\SubscriptionResource;
use App\Models\Plan;
use App\Services\PlanService;
use App\Services\Payments\PaymentGatewayResolver;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PlanController extends Controller
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly PaymentGatewayResolver $gatewayResolver,
    ) {}

    /**
     * Lista todos os planos ativos com suas features.
     */
    public function index(): JsonResponse
    {
        $plans = Plan::active()->with('features')->get();

        return ApiResponse::success(PlanResource::collection($plans), [
            'count' => $plans->count(),
        ]);
    }

    /**
     * Retorna a assinatura atual do usuário.
     */
    public function current(Request $request): JsonResponse
    {
        $subscription = $this->planService
            ->current($request->user())
            ?->load('plan.features');

        return ApiResponse::success(
            $subscription ? SubscriptionResource::make($subscription) : null
        );
    }

    /**
     * Assina um plano.
     */
    public function subscribe(SubscribePlanRequest $request): JsonResponse
    {
        try {
            $plan = Plan::bySlug($request->validated('plan'))->firstOrFail();
            $gateway = $this->gatewayResolver->resolve($request->validated('gateway'));

            $subscription = $this->planService->subscribe($request->user(), $plan, $gateway);
            $subscription->load('plan.features');

            return ApiResponse::success(SubscriptionResource::make($subscription), [
                'message' => "Inscrito no plano {$plan->name} com sucesso!",
            ], 201);
        } catch (InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }
    }

    /**
     * Altera o plano (upgrade/downgrade).
     */
    public function changePlan(ChangePlanRequest $request): JsonResponse
    {
        try {
            $plan = Plan::bySlug($request->validated('plan'))->firstOrFail();
            $gateway = $this->gatewayResolver->resolve($request->validated('gateway'));

            $subscription = $this->planService->changePlan($request->user(), $plan, $gateway);
            $subscription->load('plan.features');

            return ApiResponse::success(SubscriptionResource::make($subscription), [
                'message' => "Plano alterado para {$plan->name}!",
            ]);
        } catch (InvalidArgumentException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }
    }

    /**
     * Cancela a assinatura.
     */
    public function cancel(Request $request): JsonResponse
    {
        $canceled = $this->planService->cancel($request->user());

        if (! $canceled) {
            return ApiResponse::error('Nenhuma assinatura ativa encontrada.', 404);
        }

        return ApiResponse::success([
            'canceled' => true,
        ], [
            'message' => 'Assinatura cancelada com sucesso.',
        ]);
    }
}
