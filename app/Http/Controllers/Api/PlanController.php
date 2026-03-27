<?php

namespace App\Http\Controllers\Api;

use App\Contracts\PaymentGatewayInterface;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\PlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct(private PlanService $planService) {}

    /**
     * Lista todos os planos ativos com suas features.
     */
    public function index(): JsonResponse
    {
        $plans = Plan::active()->with('features')->get();

        return response()->json([
            'plans' => $plans->map(fn (Plan $plan) => [
                'slug' => $plan->slug,
                'name' => $plan->name,
                'price' => $plan->price,
                'billing_cycle' => $plan->billing_cycle,
                'trial_days' => $plan->trial_days,
                'features' => $plan->features->pluck('limit_value', 'feature'),
            ]),
        ]);
    }

    /**
     * Retorna a assinatura atual do usuário.
     */
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();
        $sub = $user->userSubscription?->load('plan.features');

        return response()->json([
            'subscription' => $sub ? [
                'plan' => $sub->plan->slug,
                'plan_name' => $sub->plan->name,
                'status' => $sub->status,
                'starts_at' => $sub->starts_at,
                'expires_at' => $sub->expires_at,
                'trial_ends_at' => $sub->trial_ends_at,
                'features' => $sub->plan->features->pluck('limit_value', 'feature'),
            ] : null,
        ]);
    }

    /**
     * Assina um plano.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'plan' => 'required|string|exists:plans,slug',
            'gateway' => 'nullable|string|in:stripe,pagseguro',
        ]);

        $plan = Plan::where('slug', $request->plan)->firstOrFail();
        $gateway = $this->resolveGateway($request->gateway);

        $subscription = $this->planService->subscribe($request->user(), $plan, $gateway);

        return response()->json([
            'message' => "Inscrito no plano {$plan->name} com sucesso!",
            'subscription' => [
                'plan' => $plan->slug,
                'status' => $subscription->status,
                'expires_at' => $subscription->expires_at,
            ],
        ], 201);
    }

    /**
     * Altera o plano (upgrade/downgrade).
     */
    public function changePlan(Request $request): JsonResponse
    {
        $request->validate([
            'plan' => 'required|string|exists:plans,slug',
            'gateway' => 'nullable|string|in:stripe,pagseguro',
        ]);

        $plan = Plan::where('slug', $request->plan)->firstOrFail();
        $gateway = $this->resolveGateway($request->gateway);

        $subscription = $this->planService->changePlan($request->user(), $plan, $gateway);

        return response()->json([
            'message' => "Plano alterado para {$plan->name}!",
            'subscription' => [
                'plan' => $plan->slug,
                'status' => $subscription->status,
                'expires_at' => $subscription->expires_at,
            ],
        ]);
    }

    /**
     * Cancela a assinatura.
     */
    public function cancel(Request $request): JsonResponse
    {
        $canceled = $this->planService->cancel($request->user());

        if (! $canceled) {
            return response()->json(['message' => 'Nenhuma assinatura ativa encontrada.'], 404);
        }

        return response()->json(['message' => 'Assinatura cancelada com sucesso.']);
    }

    /**
     * Resolve o gateway de pagamento a partir do nome.
     */
    private function resolveGateway(?string $name): ?PaymentGatewayInterface
    {
        if (! $name) {
            return null;
        }

        return match ($name) {
            'stripe' => app(\App\Services\Gateways\StripeGateway::class),
            'pagseguro' => app(\App\Services\Gateways\PagSeguroGateway::class),
            default => null,
        };
    }
}
