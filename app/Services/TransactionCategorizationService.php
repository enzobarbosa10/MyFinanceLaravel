<?php

namespace App\Services;

use App\Enums\CategorizationSource;
use App\Models\CategorizationRule;
use App\Models\Category;
use App\Models\Transaction;

class TransactionCategorizationService
{
    /**
     * Result of the last categorization attempt.
     */
    protected ?array $lastResult = null;

    /**
     * Try to categorize a transaction based on its description.
     *
     * Priority order:
     *   1. User-defined rules  (source = user, highest priority first)
     *   2. System rules        (source = system)
     *   3. AI rules            (source = ai)
     *   4. AI provider (stub)  — prepared for future integration
     *
     * Returns the Category or null if no match found.
     */
    public function categorize(Transaction $transaction): ?Category
    {
        $description = $transaction->raw_description
            ?? $transaction->description
            ?? '';

        if (trim($description) === '') {
            return null;
        }

        // 1. Try rule-based categorization
        $category = $this->applyRules($transaction->user_id, $description, $transaction->type->value);

        if ($category) {
            return $category;
        }

        // 2. Stub: future AI-based categorization
        return $this->applyAi($transaction);
    }

    /**
     * Categorize and persist the result on the transaction.
     *
     * Only updates category_id and confidence_score if a match is found
     * and the transaction does not already have a category (or $force = true).
     */
    public function categorizeAndSave(Transaction $transaction, bool $force = false): Transaction
    {
        if (!$force && $transaction->category_id) {
            return $transaction;
        }

        $category = $this->categorize($transaction);

        if ($category) {
            $transaction->update([
                'category_id'      => $category->id,
                'confidence_score' => $this->lastResult['confidence'] ?? null,
            ]);
            $transaction->refresh();
        }

        return $transaction;
    }

    /**
     * Batch-categorize uncategorized transactions for a user.
     */
    public function categorizeBatch(int $userId, int $limit = 200): int
    {
        $transactions = Transaction::where('user_id', $userId)
            ->whereNull('category_id')
            ->orderByDesc('transaction_at')
            ->limit($limit)
            ->get();

        $categorized = 0;

        foreach ($transactions as $transaction) {
            $category = $this->categorize($transaction);

            if ($category) {
                $transaction->update([
                    'category_id'      => $category->id,
                    'confidence_score' => $this->lastResult['confidence'] ?? null,
                ]);
                $categorized++;
            }
        }

        return $categorized;
    }

    /**
     * Learn a new user rule from a manual categorization.
     *
     * When a user manually categorizes a transaction, we can extract
     * the keyword and create a rule so future transactions match automatically.
     */
    public function learnFromManual(Transaction $transaction, string $keyword = null): ?CategorizationRule
    {
        $pattern = $keyword ?? $this->extractKeyword($transaction->description ?? '');

        if (!$pattern) {
            return null;
        }

        return CategorizationRule::updateOrCreate(
            [
                'user_id' => $transaction->user_id,
                'pattern' => mb_strtoupper($pattern),
            ],
            [
                'category_id' => $transaction->category_id,
                'type'        => $transaction->type->value,
                'source'      => CategorizationSource::User->value,
                'priority'    => 10,
                'is_active'   => true,
            ]
        );
    }

    // ── Private Methods ──────────────────────────────────────

    /**
     * Get the confidence score from the last categorization attempt.
     */
    public function getLastConfidence(): ?float
    {
        return $this->lastResult['confidence'] ?? null;
    }

    /**
     * Walk through active rules (user first, then system, then AI)
     * and return the first matching category.
     */
    protected function applyRules(int $userId, string $description, string $type): ?Category
    {
        $rules = CategorizationRule::active()
            ->forUser($userId)
            ->where('type', $type)
            ->byPriority()
            ->with('category')
            ->get();

        foreach ($rules as $rule) {
            if ($rule->matches($description)) {
                $category = $this->resolveCategory($rule, $userId);

                if (!$category) {
                    continue;
                }

                $this->lastResult = [
                    'rule_id'    => $rule->id,
                    'source'     => $rule->source,
                    'confidence' => $rule->source === CategorizationSource::Ai->value ? 0.75 : 1.0,
                ];

                return $category;
            }
        }

        return null;
    }

    /**
     * Resolve the category for a rule.
     *
     * User rules have a direct category_id FK.
     * System rules store category_name and resolve against the user's categories.
     */
    protected function resolveCategory(CategorizationRule $rule, int $userId): ?Category
    {
        // User/AI rules with direct FK
        if ($rule->category_id) {
            return $rule->category;
        }

        // System rules: resolve by name against user's categories
        if ($rule->category_name) {
            return Category::where('user_id', $userId)
                ->where('name', $rule->category_name)
                ->first();
        }

        return null;
    }

    /**
     * Stub for future AI-based categorization.
     *
     * This method is intentionally left as a placeholder.
     * Replace with an actual AI/ML call when ready.
     *
     * Expected contract:
     *   - Send description + user's existing categories
     *   - Receive { category_name: string, confidence: float }
     *   - Resolve to a Category model
     *   - Optionally create a new AI rule via learnFromAi()
     */
    protected function applyAi(Transaction $transaction): ?Category
    {
        // Future integration point — example pseudocode:
        //
        // $response = AiCategorizationProvider::predict(
        //     description: $transaction->description,
        //     categories: Category::where('user_id', $transaction->user_id)->pluck('name'),
        // );
        //
        // if ($response && $response->confidence >= 0.6) {
        //     $category = Category::where('user_id', $transaction->user_id)
        //         ->where('name', $response->category_name)
        //         ->first();
        //
        //     $this->lastResult = [
        //         'source'     => 'ai',
        //         'confidence' => $response->confidence,
        //     ];
        //
        //     return $category;
        // }

        return null;
    }

    /**
     * Extract a meaningful keyword from a transaction description.
     * Used when learning from manual categorizations.
     */
    protected function extractKeyword(string $description): ?string
    {
        // Remove common noise words and numbers
        $cleaned = preg_replace('/[0-9\/*\-\.]+/', ' ', $description);
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned));

        // Take the first significant word (>= 3 chars)
        $words = explode(' ', $cleaned);

        foreach ($words as $word) {
            if (mb_strlen($word) >= 3) {
                return mb_strtoupper($word);
            }
        }

        return null;
    }
}
