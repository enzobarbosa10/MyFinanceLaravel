<?php

namespace App\Http\Resources\Api;

use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin UserSubscription */
class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'gateway' => $this->gateway,
            'plan' => [
                'slug' => $this->plan?->slug,
                'name' => $this->plan?->name,
                'price' => $this->plan ? (float) $this->plan->price : null,
                'billing_cycle' => $this->plan?->billing_cycle,
                'features' => $this->plan?->features?->pluck('limit_value', 'feature') ?? [],
            ],
            'starts_at' => optional($this->starts_at)?->toISOString(),
            'expires_at' => optional($this->expires_at)?->toISOString(),
            'trial_ends_at' => optional($this->trial_ends_at)?->toISOString(),
            'canceled_at' => optional($this->canceled_at)?->toISOString(),
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
