<?php

namespace App\Http\Resources\Api;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Plan */
class PlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => (float) $this->price,
            'billing_cycle' => $this->billing_cycle,
            'trial_days' => $this->trial_days,
            'features' => $this->features->pluck('limit_value', 'feature'),
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
