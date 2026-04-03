<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\OpenFinanceService;
use App\Services\TransactionCategorizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncOpenFinanceData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public User $user,
        public string $itemId,
        public ?string $from = null,
        public ?string $to = null,
    ) {
        $this->onQueue('open-finance');
    }

    /**
     * Prevent parallel syncs for the same user + item combination.
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("sync-of-{$this->user->id}-{$this->itemId}"))
                ->releaseAfter(120)
                ->expireAfter(300),
        ];
    }

    public function handle(
        OpenFinanceService $openFinance,
        TransactionCategorizationService $categorization,
    ): void {
        $from = $this->from ?? now()->subMonths(3)->toDateString();
        $to   = $this->to ?? now()->toDateString();

        Log::info('SyncOpenFinanceData: starting', [
            'user_id' => $this->user->id,
            'item_id' => $this->itemId,
            'from'    => $from,
            'to'      => $to,
        ]);

        $accountsSynced     = $openFinance->syncAccounts($this->user, $this->itemId);
        $transactionsSynced = $openFinance->syncTransactions($this->user, $this->itemId, $from, $to);

        $categorized = $categorization->categorizeUncategorized($this->user);

        Log::info('SyncOpenFinanceData: completed', [
            'user_id'      => $this->user->id,
            'accounts'     => $accountsSynced,
            'transactions' => $transactionsSynced,
            'categorized'  => $categorized,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SyncOpenFinanceData: failed', [
            'user_id' => $this->user->id,
            'item_id' => $this->itemId,
            'error'   => $exception->getMessage(),
        ]);
    }
}
