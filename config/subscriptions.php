<?php

return [
    'webhook' => [
        'replay_tolerance_seconds' => (int) env('PAYMENT_WEBHOOK_REPLAY_TOLERANCE', 300),
    ],
    'gateway' => [
        'timeout_seconds' => (int) env('PAYMENT_GATEWAY_TIMEOUT', 10),
        'retry' => [
            'attempts' => (int) env('PAYMENT_GATEWAY_RETRY_ATTEMPTS', 3),
            'base_delay_ms' => (int) env('PAYMENT_GATEWAY_BASE_DELAY_MS', 200),
        ],
        'circuit_breaker' => [
            'failure_threshold' => (int) env('PAYMENT_CB_FAILURE_THRESHOLD', 5),
            'cooldown_seconds' => (int) env('PAYMENT_CB_COOLDOWN_SECONDS', 60),
        ],
    ],
];
