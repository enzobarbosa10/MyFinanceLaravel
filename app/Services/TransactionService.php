<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetAlert;
use App\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        protected TransactionCategorizationService $categorizationService,
        protected FinancialNotificationService $notificationService,
        protected UsageTrackingService $usageTrackingService,
    ) {}

    /**
     * List transactions with filters and summary.
     */
    public function list(int $userId, array $filters): array
    {
        $month = $filters['month'] ?? date('Y-m');

        $query = Transaction::with(['account', 'category'])
            ->forUser($userId)
            ->forMonth($month);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['type'])) {
            $type = TransactionType::tryFrom($filters['type']);
            if ($type) {
                $query->ofType($type);
            }
        }

        $transactions = $query
            ->orderByDesc('transaction_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->appends($filters);

        $entradas = Transaction::sumByType($userId, TransactionType::Entrada, $month);
        $saidas   = Transaction::sumByType($userId, TransactionType::Saida, $month);
        $saldo    = $entradas - $saidas;

        return compact('transactions', 'entradas', 'saidas', 'saldo', 'month');
    }

    /**
     * Create a transaction and update the account balance.
     * If no category_id is provided, attempts auto-categorization via rules.
     */
    public function store(int $userId, array $data): Transaction
    {
        // Auto-categorize when category is not explicitly set
        if (empty($data['category_id'])) {
            $data['category_id'] = $this->autoResolveCategory($userId, $data);
        }

        $this->verifyOwnership($userId, $data['account_id'], $data['category_id']);

        $transaction = Transaction::createWithBalance([
            'user_id'        => $userId,
            'account_id'     => $data['account_id'],
            'category_id'    => $data['category_id'],
            'type'           => $data['type'],
            'amount'         => $data['amount'],
            'description'    => $data['description'] ?? null,
            'raw_description' => $data['raw_description'] ?? null,
            'transaction_at' => $data['transaction_at'],
            'confidence_score' => $data['confidence_score'] ?? null,
        ]);

        $this->checkBudgetAlertsIfSaida($transaction);

        // Financial notification checks
        $this->notificationService->checkUnusualSpending($transaction);
        $this->notificationService->checkLowBalance($transaction->account);
        $this->usageTrackingService->record($transaction->user, 'transactions_per_month');

        return $transaction;
    }

    /**
     * Update a transaction and adjust the account balance.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        $this->verifyOwnership($transaction->user_id, $data['account_id'], $data['category_id']);

        $updated = Transaction::updateWithBalance($transaction, [
            'account_id'     => $data['account_id'],
            'category_id'    => $data['category_id'],
            'type'           => $data['type'],
            'amount'         => $data['amount'],
            'description'    => $data['description'] ?? null,
            'transaction_at' => $data['transaction_at'],
        ]);

        $this->checkBudgetAlertsIfSaida($updated);

        return $updated;
    }

    /**
     * Delete a transaction and revert the account balance.
     */
    public function destroy(Transaction $transaction): void
    {
        Transaction::deleteWithBalance($transaction);
    }

    // ── Private Helpers ──────────────────────────────────────

    private function verifyOwnership(int $userId, int $accountId, int $categoryId): void
    {
        Account::where('id', $accountId)->where('user_id', $userId)->firstOrFail();
        \App\Models\Category::where('id', $categoryId)->where('user_id', $userId)->firstOrFail();
    }

    private function checkBudgetAlertsIfSaida(Transaction $transaction): void
    {
        if ($transaction->type !== TransactionType::Saida) {
            return;
        }

        $month  = $transaction->transaction_at->format('Y-m');
        $userId = $transaction->user_id;

        $budget = Budget::where('user_id', $userId)
            ->where('category_id', $transaction->category_id)
            ->where('month', $month)
            ->first();

        if (!$budget || $budget->amount <= 0) {
            return;
        }

        $spent = Transaction::forUser($userId)
            ->ofType(TransactionType::Saida)
            ->where('category_id', $transaction->category_id)
            ->forMonth($month)
            ->sum('amount');

        $percentage = round(($spent / $budget->amount) * 100, 1);

        if ($percentage >= 100) {
            BudgetAlert::updateOrCreate(
                ['budget_id' => $budget->id, 'alert_type' => 'exceeded', 'month' => $month],
                ['user_id' => $userId, 'percentage' => $percentage, 'seen' => false]
            );
        }

        if ($percentage >= 80) {
            BudgetAlert::updateOrCreate(
                ['budget_id' => $budget->id, 'alert_type' => 'warning', 'month' => $month],
                ['user_id' => $userId, 'percentage' => $percentage, 'seen' => false]
            );
        }
    }

    /**
     * Build a temporary Transaction to run through the categorization service.
     * Returns the resolved category_id or throws if no match is found.
     */
    private function autoResolveCategory(int $userId, array $data): int
    {
        // Build a transient model so the service can inspect it
        $probe = new Transaction([
            'user_id'         => $userId,
            'type'            => $data['type'],
            'description'     => $data['description'] ?? null,
            'raw_description' => $data['raw_description'] ?? null,
        ]);

        $category = $this->categorizationService->categorize($probe);

        if ($category) {
            $data['confidence_score'] = $this->categorizationService->getLastConfidence();
            return $category->id;
        }

        // Fallback: require category_id when auto-categorization fails
        throw new \InvalidArgumentException('category_id is required when auto-categorization cannot resolve a category.');
    }
}
