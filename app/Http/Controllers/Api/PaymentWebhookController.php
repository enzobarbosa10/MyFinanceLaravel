<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymentWebhookRequest;
use App\Services\OperationalAlertService;
use App\Services\Payments\PaymentGatewayResolver;
use App\Services\Payments\SubscriptionTelemetryService;
use App\Services\PlanService;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly PlanService $planService,
        private readonly PaymentGatewayResolver $gatewayResolver,
        private readonly SubscriptionTelemetryService $telemetry,
        private readonly OperationalAlertService $alertService,
    ) {}

    public function handle(PaymentWebhookRequest $request, string $gateway)
    {
        $startedAt = microtime(true);

        try {
            $gatewayInstance = $this->gatewayResolver->resolve($gateway);
            $this->planService->handlePaymentWebhook($gatewayInstance, $request->all());

            $this->telemetry->observeWebhookDuration($gateway, (microtime(true) - $startedAt) * 1000);
            $this->alertService->incrementWebhookResult($gateway, true);

            return ApiResponse::success(['received' => true], ['gateway' => $gateway]);
        } catch (InvalidArgumentException) {
            return ApiResponse::error('Gateway inválido para webhook.', 422);
        } catch (Throwable $e) {
            $this->alertService->incrementWebhookResult($gateway, false);
            $this->alertService->alertWebhookFailureRate($gateway, $this->alertService->failureRate($gateway), [
                'message' => $e->getMessage(),
            ]);
            Log::error('payment.webhook.failed', [
                'gateway' => $gateway,
                'message' => $e->getMessage(),
            ]);

            return ApiResponse::error('Falha ao processar webhook.', 500);
        }
    }
}
