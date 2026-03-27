<?php

namespace App\Services\InsightEngine;

use App\Models\Insight;
use App\Models\User;
use App\Services\InsightEngine\Contracts\InsightRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class InsightEngine
{
    /** @var InsightRule[] */
    private array $rules = [];

    /**
     * Register a rule in the engine.
     */
    public function addRule(InsightRule $rule): self
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * Register multiple rules at once.
     *
     * @param  InsightRule[]  $rules
     */
    public function addRules(array $rules): self
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }

        return $this;
    }

    /**
     * Execute all registered rules for a user, persist new insights
     * and return the generated collection.
     *
     * @return Collection<int, Insight>
     */
    public function generate(User $user): Collection
    {
        $generated = collect();

        foreach ($this->rules as $rule) {
            try {
                $results = $rule->evaluate($user);

                foreach ($results as $data) {
                    $insight = $this->persist($user, $data);
                    $generated->push($insight);
                }
            } catch (\Throwable $e) {
                Log::error('InsightEngine: rule failed', [
                    'rule'    => $rule::class,
                    'user_id' => $user->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        return $generated;
    }

    /**
     * Avoid duplicating insights with the same title that are still active
     * for this month. Updates existing or creates new.
     */
    private function persist(User $user, array $data): Insight
    {
        return Insight::updateOrCreate(
            [
                'user_id' => $user->id,
                'title'   => $data['title'],
                'type'    => $data['type'],
            ],
            [
                'message'      => $data['message'],
                'impact_value' => $data['impact_value'] ?? null,
                'related_type' => $data['related_type'] ?? null,
                'related_id'   => $data['related_id'] ?? null,
                'expires_at'   => $data['expires_at'] ?? null,
                'is_read'      => false,
                'read_at'      => null,
            ]
        );
    }
}
