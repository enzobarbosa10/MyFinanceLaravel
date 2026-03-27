<?php

namespace App\Services\InsightEngine\Contracts;

use App\Models\User;
use Illuminate\Support\Collection;

interface InsightRule
{
    /**
     * Evaluate the rule for a given user.
     *
     * @return Collection<int, array{type: string, title: string, message: string, impact_value: float|null, related_type: string|null, related_id: int|null, expires_at: string|null}>
     */
    public function evaluate(User $user): Collection;
}
