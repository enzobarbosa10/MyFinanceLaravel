<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Insight;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private const CACHE_TTL = 120; // seconds

    public function __construct(
        private FinancialProjectionService $projectionService,
    ) {}

    /**
     * Resumo completo do dashboard financeiro.
     */
    public function getSummary(User $user): array
    {
        $userId = $user->id;
        $month = now()->format('Y-m');

        return Cache::remember("dashboard_api:{$userId}:{$month}", self::CACHE_TTL, function () use ($user, $userId, $month) {
            return [
                'saldo_total'        => $this->getSaldoTotal($userId),
                'gastos_mes'         => $this->getGastosMes($userId, $month),
                'gastos_por_categoria' => $this->getGastosPorCategoria($userId, $month),
                'projecao'           => $this->getProjecao($user),
                'insights'           => $this->getInsightsRecentes($userId),
            ];
        });
    }

    /**
     * Saldo total: soma dos saldos de todas as contas.
     */
    public function getSaldoTotal(int $userId): array
    {
        $accounts = Account::where('user_id', $userId)
            ->select('id', 'name', 'type', 'balance')
            ->get();

        return [
            'total'  => round((float) $accounts->sum('balance'), 2),
            'contas' => $accounts->map(fn ($a) => [
                'id'    => $a->id,
                'nome'  => $a->name,
                'tipo'  => $a->type,
                'saldo' => round((float) $a->balance, 2),
            ])->values()->all(),
        ];
    }

    /**
     * Gastos do mês atual vs mês anterior com delta percentual.
     */
    public function getGastosMes(int $userId, string $month): array
    {
        $lastMonth = now()->subMonth()->format('Y-m');

        // Uma única query para buscar entradas e saídas dos dois meses
        [$year, $mon] = explode('-', $month);
        [$yearLast, $monLast] = explode('-', $lastMonth);

        $rows = Transaction::where('user_id', $userId)
            ->where(function ($q) use ($year, $mon, $yearLast, $monLast) {
                $q->where(function ($q2) use ($year, $mon) {
                    $q2->whereYear('transaction_at', $year)
                       ->whereMonth('transaction_at', $mon);
                })->orWhere(function ($q2) use ($yearLast, $monLast) {
                    $q2->whereYear('transaction_at', $yearLast)
                       ->whereMonth('transaction_at', $monLast);
                });
            })
            ->selectRaw("
                DATE_FORMAT(transaction_at, '%Y-%m') as mes,
                type,
                SUM(amount) as total
            ")
            ->groupBy('mes', 'type')
            ->get();

        $totals = [];
        foreach ($rows as $row) {
            $totals[$row->mes][$row->type] = (float) $row->total;
        }

        $entradas   = $totals[$month][TransactionType::Entrada->value] ?? 0;
        $saidas     = $totals[$month][TransactionType::Saida->value] ?? 0;
        $saidasLast = $totals[$lastMonth][TransactionType::Saida->value] ?? 0;

        $deltaSaidas = $saidasLast > 0
            ? round((($saidas - $saidasLast) / $saidasLast) * 100, 1)
            : 0;

        return [
            'entradas'     => round($entradas, 2),
            'saidas'       => round($saidas, 2),
            'saldo_mes'    => round($entradas - $saidas, 2),
            'delta_saidas' => $deltaSaidas,
            'mes'          => $month,
        ];
    }

    /**
     * Gastos agrupados por categoria (somente saídas do mês).
     */
    public function getGastosPorCategoria(int $userId, string $month): array
    {
        [$year, $mon] = explode('-', $month);

        return DB::table('transactions')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', TransactionType::Saida->value)
            ->whereYear('transactions.transaction_at', $year)
            ->whereMonth('transactions.transaction_at', $mon)
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->select('categories.id', 'categories.name')
            ->selectRaw('SUM(transactions.amount) as total')
            ->selectRaw('COUNT(*) as quantidade')
            ->get()
            ->map(fn ($row) => [
                'categoria_id' => $row->id,
                'categoria'    => $row->name,
                'total'        => round((float) $row->total, 2),
                'quantidade'   => (int) $row->quantidade,
            ])
            ->all();
    }

    /**
     * Projeção financeira usando o serviço existente.
     */
    public function getProjecao(User $user): array
    {
        return $this->projectionService->project($user);
    }

    /**
     * Últimos insights ativos (não expirados).
     */
    public function getInsightsRecentes(int $userId, int $limit = 10): array
    {
        return Insight::where('user_id', $userId)
            ->active()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->select('id', 'type', 'title', 'message', 'impact_value', 'is_read', 'created_at')
            ->get()
            ->map(fn ($i) => [
                'id'           => $i->id,
                'tipo'         => $i->type->value,
                'titulo'       => $i->title,
                'mensagem'     => $i->message,
                'impacto'      => $i->impact_value ? round((float) $i->impact_value, 2) : null,
                'lido'         => $i->is_read,
                'criado_em'    => $i->created_at?->toIso8601String(),
            ])
            ->all();
    }
}
